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
    <!-- Sidebar -->
    <!-- <div class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg">
        <div class="flex items-center justify-center h-16 border-b">
            <span class="text-xl font-bold text-gray-800">Support Center</span>
        </div>
        <nav class="mt-6">
            <div class="px-4 py-2">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-600">MENU</span>
                </div>
                <div class="mt-3 space-y-1">
                    <a href="#" class="flex items-center px-4 py-2.5 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg">
                        <i class="fas fa-ticket-alt mr-3"></i>
                        Tickets
                    </a>
                    <a href="#" class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-chart-bar mr-3"></i>
                        Dashboard
                    </a>
                    <a href="#" class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-cog mr-3"></i>
                        Settings
                    </a>
                </div>
            </div>
        </nav>
    </div> -->

    <!-- Main Content -->
    <div class="ml-64 p-8">
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
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
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
        </div>

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
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-medium text-gray-900">#1 Login issue with my account</div>
                                <div class="text-sm text-gray-500">Last updated 2 hours ago</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                Open
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">Apr 28, 2025</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-comment-alt mr-1"></i> 3
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-medium text-gray-900">#2 Payment not processed</div>
                                <div class="text-sm text-gray-500">Last updated 5 hours ago</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                Open
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">Apr 27, 2025</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-comment-alt mr-1"></i> 2
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-medium text-gray-900">#3 Feature request: dark mode</div>
                                <div class="text-sm text-gray-500">Last updated 1 day ago</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                Closed
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">Apr 25, 2025</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-comment-alt mr-1"></i> 5
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- Pagination -->
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing <span class="font-medium">1</span> to <span class="font-medium">3</span> of <span class="font-medium">3</span> tickets
                    </div>
                    <div class="flex space-x-2">
                        <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50" disabled>
                            Previous
                        </button>
                        <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50" disabled>
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Ticket Modal -->
    <div id="modalBackdrop" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 transform transition-all">
            <form>
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
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                            <select 
                                id="priority" 
                                name="priority" 
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
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
    </script>
</body>
</html>