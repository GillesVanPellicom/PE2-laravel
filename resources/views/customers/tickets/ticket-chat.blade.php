<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Support Ticket Chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto max-w-4xl p-4 h-screen py-6">
        <!-- Chat Container -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 h-full flex flex-col">
            <!-- Chat Header -->
            <div class="p-4 border-b border-gray-200 flex-none">
            <a href="{{ route('tickets.nytickets') }}" class="mb-6 flex items-center text-blue-500 hover:text-blue-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Tickets
                </a>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">#{{$ticket->id}} - {{$ticket->subject}}</h2>
                        <p class="text-sm text-gray-500">Created: {{$ticket->created_at}}</p>
                    </div>
                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800
                                {{ $ticket->status === 'open' ? 'bg-blue-100 text-blue-800' : 
                                ($ticket->status === 'resolved' ? 'bg-green-100 text-green-800' : 
                                'bg-red-100 text-red-800') }}
                            ">
                                {{$ticket->status}}
                    </span>
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4" id="chat-messages">

                @foreach($messages as $message)
                
                @if($message->is_customer_message == 1)
                <!-- Customer Message -->
                <div class="flex flex-col items-end space-y-1">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs text-gray-500">{{$ticket->user->first_name}} {{$ticket->user->last_name}} • {{ $message->created_at->format('M d, Y H:i') }}</span>
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-user text-gray-500"></i>
                        </div>
                    </div>
                    <div class="bg-blue-600 text-white rounded-lg rounded-tr-none py-2 px-4 max-w-[80%]">
                        <p>{{$message->message}}</p>
                    </div>
                </div>
                @else
                <!-- Support Message -->
                <div class="flex flex-col items-start space-y-1">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-headset text-blue-600"></i>
                        </div>
                        <span class="text-xs text-gray-500">Support Team • {{ $message->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="bg-gray-100 text-gray-800 rounded-lg rounded-tl-none py-2 px-4 max-w-[80%]">
                        <p>{{$message->message}}</p>
                    </div>
                </div>
                @endif
                @endforeach
            </div>

            <!-- Message Input -->
            <div class="p-4 border-t border-gray-200 flex-none">
            @if($ticket->status == 'open')
                <form id="messageForm" class="flex items-center space-x-2">
                @csrf
                    <div class="flex-1 relative">
                        <textarea 
                            name="message"
                            class="w-full px-4 py-2 pr-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                            placeholder="Type your message..."
                            rows="1"
                            style="min-height: 42px; max-height: 120px;"
                        ></textarea>
                    </div>
                    <button 
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-150 flex items-center"
                    >
                        <span>Send</span>
                        <i class="fas fa-paper-plane ml-2"></i>
                    </button>
                </form>
            @endif
            </div>
        </div>
    </div>

    <script>
        // Auto-resize textarea
        const textarea = document.querySelector('textarea');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Scroll to bottom of chat
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;

        document.getElementById('messageForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = this;
            const submitButton = form.querySelector('button[type="submit"]');
            const messageInput = form.querySelector('textarea');
            const originalButtonText = submitButton.innerHTML;
            
            // Get the ticket ID from the URL
            const ticketId = window.location.pathname.split('/').pop();
            
            try {
                // Validate message
                if (!messageInput.value.trim()) {
                    throw new Error('Please enter a message');
                }

                // Disable the submit button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sending...
                `;

                // Get form data
                const formData = new FormData(form);

                // Send request
                const response = await fetch(`/tickets/${ticketId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Failed to send message');
                }

                // Show success message
                showNotification('Success', 'Message sent successfully', 'success');

                // Clear the input
                messageInput.value = '';
                messageInput.style.height = 'auto'; // Reset height if you're using auto-resize

                // Add the new message to the chat (optional)
                appendNewMessage(result.data);

                // Scroll to bottom
                const chatMessages = document.getElementById('chat-messages');
                chatMessages.scrollTop = chatMessages.scrollHeight;

            } catch (error) {
                // Show error message
                showNotification('Error', error.message, 'error');
            } finally {
                // Reset button state
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        });

        // Function to append new message to the chat
        function appendNewMessage(message) {
            const chatMessages = document.getElementById('chat-messages');
            const messageHTML = `
                <div class="flex flex-col items-end space-y-1">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs text-gray-500">You • Just now</span>
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-user text-gray-500"></i>
                        </div>
                    </div>
                    <div class="bg-blue-600 text-white rounded-lg rounded-tr-none py-2 px-4 max-w-[80%]">
                        <p>${escapeHtml(message.message)}</p>
                    </div>
                </div>
            `;
            
            chatMessages.insertAdjacentHTML('beforeend', messageHTML);
        }

        // Helper function to escape HTML and prevent XSS
        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Notification function (if not already defined)
        function showNotification(title, message, type = 'success') {
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

            // Add click handler to close button
            notification.querySelector('button').addEventListener('click', () => {
                notification.remove();
            });

            // Add to document
            document.body.appendChild(notification);

            // Remove after 5 seconds
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    </script>
</body>
</html>