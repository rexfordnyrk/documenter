<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Signature Request') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('signature-requests.store') }}" enctype="multipart/form-data" class="max-w-lg mx-auto bg-gray-800 shadow-lg rounded-lg p-6 text-gray-100">
                @csrf

                <div class="mb-4">
                    <label for="title" class="block text-yellow-400 font-bold mb-2">Title:</label>
                    <input type="text" name="title" id="title" class="form-input w-full bg-gray-700 text-gray-100 border-gray-600 focus:bg-gray-600 focus:border-gray-400" value="{{ old('title') }}" required>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-yellow-400 font-bold mb-2">Description:</label>
                    <textarea name="description" id="description" class="form-textarea w-full bg-gray-700 text-gray-100 border-gray-600 focus:bg-gray-600 focus:border-gray-400">{{ old('description') }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="document" class="block text-yellow-400 font-bold mb-2">Document:</label>
                    <input type="file" name="document" id="document" class="form-input w-full bg-gray-700 text-gray-100 border-gray-600 focus:bg-gray-600 focus:border-gray-400" required>
                    <p class="text-gray-500 text-sm mt-1">Upload a PDF document.</p>
                </div>

                <div class="mb-4">
                    <label for="document_type_id" class="block text-yellow-400 font-bold mb-2">Document Type:</label>
                    <select name="document_type_id" id="document_type_id" class="form-select w-full bg-gray-700 text-gray-100 border-gray-600 focus:bg-gray-600 focus:border-gray-400" required>
                        <option value="">Select Document Type</option>
                        @foreach($documentTypes as $documentType)
                            <option value="{{ $documentType->id }}">{{ $documentType->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-yellow-400 font-bold mb-2">Signatories:</label>
                    <div id="signatories">
                        <!-- Signatory fields for each signatory -->
                        @for ($i = 0; $i < 1; $i++)
                            <div class="mb-6">
                                <h3 class="text-yellow-400 font-bold mb-2">Signatory {{ $i + 1 }}</h3>
                                <div class="mb-2">
                                    <input type="text" name="signatory_name[]" class="form-input w-full bg-gray-700 text-gray-100 border-gray-600 focus:bg-gray-600 focus:border-gray-400" placeholder="Name" required>
                                </div>
                                <div class="mb-2">
                                    <input type="email" name="signatory_email[]" class="form-input w-full bg-gray-700 text-gray-100 border-gray-600 focus:bg-gray-600 focus:border-gray-400" placeholder="Email" required>
                                </div>
                                <div class="mb-2">
                                    <input type="text" name="signatory_position[]" class="form-input w-full bg-gray-700 text-gray-100 border-gray-600 focus:bg-gray-600 focus:border-gray-400" placeholder="Position" required>
                                </div>
                            </div>
                        @endfor
                    </div>
                    <button type="button" id="addSignatory" class="bg-yellow-400 hover:bg-yellow-500 text-gray-800 font-bold py-2 px-4 rounded mt-2">Add Signatory</button>
                </div>
                <div class="mb-4">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Create Signature Request</button>
                </div>

            </form>
        </div>
    </div>
    <script>
        // JavaScript code for adding signatories dynamically
        document.getElementById('addSignatory').addEventListener('click', function() {
            var signatoriesContainer = document.getElementById('signatories');
            var signatoryIndex = signatoriesContainer.childElementCount + 1;
            var signatoryTemplate = `
            <div class="mb-6">
                <h3 class="text-yellow-400 font-bold mb-2">Signatory ${signatoryIndex}</h3>
                <div class="mb-2">
                    <input type="text" name="signatory_name[]" class="form-input w-full bg-gray-700 text-gray-100 border-gray-600 focus:bg-gray-600 focus:border-gray-400" placeholder="Name" required>
                </div>
                <div class="mb-2">
                    <input type="email" name="signatory_email[]" class="form-input w-full bg-gray-700 text-gray-100 border-gray-600 focus:bg-gray-600 focus:border-gray-400" placeholder="Email" required>
                </div>
                <div class="mb-2">
                    <input type="text" name="signatory_position[]" class="form-input w-full bg-gray-700 text-gray-100 border-gray-600 focus:bg-gray-600 focus:border-gray-400" placeholder="Position" required>
                </div>
            </div>
        `;
            var signatoryElement = document.createElement('div');
            signatoryElement.innerHTML = signatoryTemplate;
            signatoriesContainer.appendChild(signatoryElement);
        });
    </script>

</x-app-layout>
