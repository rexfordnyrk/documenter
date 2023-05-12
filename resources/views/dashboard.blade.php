<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <div class="container mx-auto">
                    <div class="my-4 p-4 flex justify-between items-center">
                        <h1 class="text-yellow-400 text-2xl font-bold">Signature Requests</h1>
                        <a href="{{ route('signature-requests.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Create Signature Request</a>
                    </div>

                    <div class="bg-gray-200 rounded-lg p-4">
                        <div class="flex items-center mb-4">
                            <label for="search" class="mr-2">Search:</label>
                            <input type="text" id="search" class="form-input w-64" placeholder="Search">
                        </div>

                        <table class="min-w-full bg-white rounded-lg overflow-hidden">
                            <thead>
                            <tr class="bg-blue-500 text-white">
                                <th class="py-2 px-4 cursor-pointer" onclick="sortTable('title')">Title</th>
                                <th class="py-2 px-4 cursor-pointer" onclick="sortTable('description')">Description</th>
                                <th class="py-2 px-4 cursor-pointer" onclick="sortTable('document_type')">Document Type</th>
                                <th class="py-2 px-4 cursor-pointer" onclick="sortTable('initiator')">Initiator</th>
                                <th class="py-2 px-4 cursor-pointer" onclick="sortTable('status')">Status</th>
                                <th class="py-2 px-4">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($signatureRequests as $signatureRequest)
                                <tr>
                                    <td class="py-2 px-4">{{ $signatureRequest->title }}</td>
                                    <td class="py-2 px-4">{{ $signatureRequest->description }}</td>
                                    <td class="py-2 px-4">{{ $signatureRequest->documentType->title }}</td>
                                    <td class="py-2 px-4">{{ $signatureRequest->initiator->name }}</td>
                                    <td class="py-2 px-4">{{ $signatureRequest->status }}</td>
                                    <td class="py-2 px-4">
                                        <a href="{{ route('signature-requests.show', $signatureRequest) }}" class="text-blue-500 hover:text-blue-700 mr-2">View</a>
                                        <a href="{{ route('signature-requests.edit', $signatureRequest) }}" class="text-yellow-500 hover:text-yellow-700 mr-2">Edit</a>
                                        <form action="{{ route('signature-requests.destroy', $signatureRequest) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <script    <script>
                    function sortTable(column) {
                        const table = document.querySelector('table');
                        const rows = Array.from(table.rows).slice(1); // Exclude the header row
                        const isAscending = table.getAttribute('data-sort') === column && table.getAttribute('data-order') === 'asc';

                        rows.sort((rowA, rowB) => {
                            const cellA = rowA.querySelector(`td[data-sort="${column}"]`).textContent.trim().toLowerCase();
                            const cellB = rowB.querySelector(`td[data-sort="${column}"]`).textContent.trim().toLowerCase();

                            if (isAscending) {
                                return cellA.localeCompare(cellB);
                            } else {
                                return cellB.localeCompare(cellA);
                            }
                        });

                        table.tBodies[0].append(...rows);
                        table.setAttribute('data-sort', column);
                        table.setAttribute('data-order', isAscending ? 'desc' : 'asc');
                    }
                </script>

            </div>
        </div>
    </div>
</x-app-layout>
