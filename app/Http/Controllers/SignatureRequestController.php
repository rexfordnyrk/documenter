<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use App\Models\Signatory;
use App\Models\SignatureRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

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
            'reference_id' => 'ahahahaha', //generateReferenceId(), // Replace with your logic to generate reference ID
            'document_type_id' => $request->input('document_type_id'),
        ]);

        // Create signatories
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

        // Save signatories in database
        $signatureRequest->signatories()->saveMany($signatories);

        // Redirect or return response
        return Redirect::to('/')->with('success', 'Signature request created successfully.');
    }
}
