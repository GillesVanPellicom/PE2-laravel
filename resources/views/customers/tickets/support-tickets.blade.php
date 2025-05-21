<x-app-layout>
    @section('title', 'Support Tickets')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Tickets</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Main Content -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Support Tickets</h1>
                <p class="mt-1 text-sm text-gray-600">Manage and track your support requests</p>
            </div>
            <button
                id="openModalBtn"
                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg flex items-center transition-colors duration-150"
            >
                <i class="fas fa-plus mr-2"></i>
                New Ticket
            </button>
        </div>

        <!-- Filters -->
        <!-- <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex space-x-2">
                    <button class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg font-medium transition-colors duration-150">
                        All Tickets (4)
                    </button>
                    <button class="px-4 py-2 text-gray-600 hover:bg-gray-50 rounded-lg font-medium transition-colors duration-150">
                        Open (2)
                    </button>
                    <button class="px-4 py-2 text-gray-600 hover:bg-gray-50 rounded-lg font-medium transition-colors duration-150">
                        Closed (2)
                    </button>
                </div>
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Search tickets..."
                        class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg w-64 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
        </div> -->

        <!-- Tickets Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Messages</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($tickets as $ticket)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-medium text-gray-900">#{{$ticket->id}} {{$ticket->subject}}</div>
                                <div class="text-sm text-gray-500">Last updated: {{$ticket->updated_at}}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-medium rounded-full
                            {{ $ticket->status === 'open' ? 'bg-blue-100 text-blue-800' :
                                    ($ticket->status === 'resolved' ? 'bg-green-100 text-green-800' :
                                        'bg-red-100 text-red-800') }}
                            ">
                                {{$ticket->status}}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{$ticket->created_at->format('Y-m-d')}}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-comment-alt mr-1"></i> {{$ticket->message_count}}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <a href="{{ route('tickets.ticketchat', $ticket->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            <!-- <button class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-ellipsis-v"></i>
                            </button> -->
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <!-- Pagination -->
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Showing {{ $tickets->firstItem() ?? 0 }} to {{ $tickets->lastItem() ?? 0 }} of {{ $tickets->total() }} tickets
                </div>
                <div class="flex space-x-2">
                    {{ $tickets->links() }}
                </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Ticket Modal -->
    <div id="modalBackdrop" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 transform transition-all">
            <form action="{{ route('tickets.store') }}" method="POST" id="ticketForm">
            @csrf
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Create New Support Ticket</h3>
                        <button type="button" id="closeModalBtn" class="text-gray-400 hover:text-gray-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                            <input
                                type="text"
                                name="subject"
                                id="subject"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Brief description of your issue"
                            >
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                            <textarea
                                id="message"
                                name="message"
                                rows="4"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Detailed explanation of your issue..."
                            ></textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end space-x-3">
                    <button
                        type="button"
                        id="closeModalBtn2"
                        class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-150"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-150"
                    >
                        Create Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal functionality
        const openModalBtn = document.getElementById('openModalBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const closeModalBtn2 = document.getElementById('closeModalBtn2');
        const modalBackdrop = document.getElementById('modalBackdrop');

        function openModal() {
            modalBackdrop.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modalBackdrop.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        openModalBtn.addEventListener('click', openModal);
        closeModalBtn.addEventListener('click', closeModal);
        closeModalBtn2.addEventListener('click', closeModal);

        modalBackdrop.addEventListener('click', (e) => {
            if (e.target === modalBackdrop) {
                closeModal();
            }
        });

        // Close modal on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        document.getElementById('ticketForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = this;
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;

            try {
                // Disable the submit button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Creating...
                `;

                // Get form data
                const formData = new FormData(form);

                // Send request
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Something went wrong');
                }

                // Show success message
                showNotification('Success', 'Ticket created successfully', 'success');

                // Clear form
                form.reset();

                // Close modal
                closeModal();

                window.location.reload();

            } catch (error) {
                // Show error message
                showNotification('Error', error.message || 'Failed to create ticket', 'error');
            } finally {
                // Reset button state
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        });

        function showNotification(title, message, type = 'success') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed bottom-4 right-4 bg-white rounded-lg shadow-lg p-4 border-l-4 ${
                type === 'success' ? 'border-green-500' : 'border-red-500'
            } z-50`;

            notification.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        ${type === 'success'
                            ? '<i class="fas fa-check-circle text-green-500 text-xl"></i>'
                            : '<i class="fas fa-exclamation-circle text-red-500 text-xl"></i>'
                        }
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">${title}</p>
                        <p class="text-sm text-gray-500">${message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button class="inline-flex text-gray-400 hover:text-gray-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;

            notification.querySelector('button').addEventListener('click', () => {
                notification.remove();
            });

            document.body.appendChild(notification);

            // Remove after 5 seconds
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    </script>
</body>
</html>
</x-app-layout>
