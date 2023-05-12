<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Request Details') }}
        </h2>
    </x-slot>

    <div class="container mx-auto">
        <div class="my-4">
            <h1 class="text-2xl font-bold text-white">Signature Request Details</h1>
            <div class="bg-gray-800 rounded-lg shadow-lg p-4 mt-4">
                <div class="flex flex-wrap -mx-2 mb-4">
                    <div class="w-full px-2">
                        <label for="title" class="block text-yellow-500 font-bold">Title:</label>
                        <div id="title" class="bg-gray-700 text-white p-2 rounded">{{ $signatureRequest->title }}</div>
                    </div>
                </div>
                <div class="flex flex-wrap -mx-2 mb-4">
                    <div class="w-full px-2">
                        <label for="description" class="block text-yellow-500 font-bold">Description:</label>
                        <div id="description" class="bg-gray-700 text-white p-2 rounded">{{ $signatureRequest->description }}</div>
                    </div>
                </div>
                <div class="flex flex-wrap -mx-2 mb-4">
                    <div class="w-full sm:w-1/2 md:w-1/3 px-2">
                        <label for="initiator" class="block text-yellow-500 font-bold">Initiator:</label>
                        <div id="initiator" class="bg-gray-700 text-white p-2 rounded">{{ $signatureRequest->initiator->name }}</div>
                    </div>
                    <div class="w-full sm:w-1/2 md:w-1/3 px-2">
                        <label for="document_type" class="block text-yellow-500 font-bold">Document Type:</label>
                        <div id="document_type" class="bg-gray-700 text-white p-2 rounded">{{ $signatureRequest->documentType->title }}</div>
                    </div>
                    <div class="w-full sm:w-1/2 md:w-1/3 px-2">
                        <label for="status" class="block text-yellow-500 font-bold">Status:</label>
                        <div id="status" class="bg-gray-700 text-white p-2 rounded">{{ $signatureRequest->status }}</div>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="signatories" class="block text-yellow-500 font-bold">Signatories:</label>
                    <div id="signatories" class="bg-gray-700 text-white p-2 rounded">
                        <table class="w-full">
                            <thead>
                            <tr class="text-left">
                                <th class="py-2 px-4 border-b-2 border-gray-600">Name</th>
                                <th class="py-2 px-4 border-b-2 border-gray-600">Position</th>
                                <th class="py-2 px-4 border-b-2 border-gray-600">Email</th>
                                <th class="py-2 px-4 border-b-2 border-gray-600">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($signatureRequest->signatories as $signatory)
                                <tr>
                                    <td class="py-2 px-4">{{ $signatory->name }}</td>
                                    <td class="py-2 px-4">{{ $signatory->position }}</td>
                                    <td class="py-2 px-4">{{ $signatory->email }}</td>
                                    <td class="py-2 px-4">{{ $signatory->status }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <a href="{{ route('signature-requests.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Back</a>
                </div>
                <div class="mt-4">
                    <label for="document" class="block text-yellow-500 font-bold">Document:</label>
                    <div id="document" class="bg-gray-700 rounded-lg">
{{--                        <embed src="{{ route('documents.show', ['filename' => $signatureRequest->document]) }}" type="application/pdf" width="100%" height="500px">--}}
                        <embed src="{{ '/'.$signatureRequest->document }}" type="application/pdf" width="100%" height="800px">
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>

