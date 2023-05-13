<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use App\Models\Signatory;
use App\Models\SignatureRequest;
use Dropbox\Sign\Api\SignatureRequestApi;
use Dropbox\Sign\ApiException;
use Dropbox\Sign\Configuration;
use Dropbox\Sign\Model\SignatureRequestCreateEmbeddedRequest;
use Dropbox\Sign\Model\SignatureRequestGetResponse;
use Dropbox\Sign\Model\SubSignatureRequestSigner;
use Dropbox\Sign\Model\SubSigningOptions;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use SplFileObject;
use function PHPUnit\Framework\isEmpty;

class SignatureRequestController extends Controller
{

    //Fetch records and display on the dashboard
    public function index(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $user = Auth::user();

        if ($user->id === 1) {
            //user id 1 is considered admin and can see all records
            $signatureRequests = SignatureRequest::latest()->paginate(10);
        } else {
            // Get the signature requests initiated by the current user or where the current user is a signatory
            $signatureRequests = SignatureRequest::where('initiator_id', $user->id)
                ->orWhereHas('signatories', function ($query) use ($user) {
                    $query->where('email', $user->email);
                })
                ->get();        }
        return view('dashboard', compact('signatureRequests'));
    }

    //Show details of one Signature Request
    public function show(SignatureRequest $signatureRequest): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('signature-requests.show', compact('signatureRequest'));
    }

    //Get a SignatureRequest Form
    public function create(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $documentTypes = DocumentType::all();

        return view('signature-requests.create', compact('documentTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        //validating all input fields
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document' => 'required|mimes:pdf|max:2048',
            'document_type_id' => 'required|exists:document_types,id',
            'signatory_name' => 'required|array',
            'signatory_name.*' => 'required|string|max:255',
            'signatory_email' => 'required|array',
            'signatory_email.*' => 'required|email',
            'signatory_position' => 'required|array',
            'signatory_position.*' => 'required|string|max:255',
        ]);

        // Storing uploaded document on filesystem
        $documentPath = $request->file('document')->store('documents');

        // Create signature request record into database
        $signatureRequest = SignatureRequest::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'document' => $documentPath,
            'initiator_id' => auth()->user()->id,
            'status' => 'pending',
            'reference_id' => '', //to be updated with Dropbox Sign Signature Request ID
            'document_type_id' => $request->input('document_type_id'),
        ]);

        // Extracting signatories from form input
        $signatories = [];
        for ($i = 0; $i < count($request->input('signatory_name')); $i++) {
            $signatories[] = new Signatory([
                'name' => $request->input('signatory_name')[$i],
                'email' => $request->input('signatory_email')[$i],
                'position' => $request->input('signatory_position')[$i],
                'status' => 'pending',
                'signature_request_id' => $signatureRequest->id
            ]);
        }

        $results = $this->performDSRequest($signatories, $signatureRequest);
        if ($results instanceof SignatureRequestGetResponse) {

            echo $results;
            $signatureRequest->reference_id = $results->getSignatureRequest()->getSignatureRequestId();
            $signatureRequest->save();
            $signers = $results->getSignatureRequest()->getSignatures();

            //looping through the signers and assigning their DS signature_id
            // to the signatory to be used to generate signing page.
            if (count($signers) > 1){
                for ($i = 0; $i < count($signers); $i++) {
                    $signatories[$signers[$i]->getOrder()]->ds_signature_id = $signers[$i]->getSignatureId();
                }
            }else{
                $signatories[0]->ds_signature_id = $signers[0]->getSignatureId();
            }
        }else{
            return Redirect::to('/dashboard')
                ->with('error', 'There was an error completing your request.Please contact tech support.\n'.$results);
        }
        // Save signatories in database
        $signatureRequest->signatories()->saveMany($signatories);

        // Redirect or return response
        return Redirect::to('/')->with('success', 'Signature request created successfully.');
    }

    public function performDSRequest(Array $signatories, SignatureRequest $request){
        //getting default configuration
        $config = Configuration::getDefaultConfiguration();
        // Configure HTTP basic authorization: api_key
        $config->setUsername(env('DS_API_KEY'));
        $signatureRequestApi = new SignatureRequestApi($config);

        $signers = [];
        for ($i = 0; $i < count($signatories); $i++) {
            //creating a new Dropbox Sign Signer
            $signer = new SubSignatureRequestSigner();
            //Setting Signer Properties from Signatories Specified in the form
            $signer->setEmailAddress($signatories[$i]->email)
                ->setName($signatories[$i]->name)
                ->setOrder($i);
            //adding signer to list (array) of signers
            $signers[] = $signer;
        }


        $signingOptions = new SubSigningOptions();
        $signingOptions->setDraw(true)
            ->setType(true)
            ->setUpload(true)
            ->setPhone(true)
            ->setDefaultType(SubSigningOptions::DEFAULT_TYPE_DRAW);

        //Creating a Dropbox Sign Embedded signature request and assigning the necessary properties
        $data = new SignatureRequestCreateEmbeddedRequest();
        //using client id place in .env file
        $data->setClientId(env('DS_CLIENT_ID'))
            ->setTitle("New Signature Request")
            ->setSubject($request->title)
            ->setMessage($request->description)
            ->setSigners($signers)
            ->setCcEmailAddresses([
                "legal@wehire.io",
            ])
            ->setFiles([new SplFileObject(storage_path('app/' . $request->document))])
            ->setSigningOptions($signingOptions)
            ->setTestMode(true);

        try {
            //Making API call to Dropbox sign to create Embedded signature request and returning response
            return $signatureRequestApi->signatureRequestCreateEmbedded($data);
        } catch (ApiException $e) {
            // if there is an error return the error
            return $e->getResponseObject()->getError();
        }
    }
}
