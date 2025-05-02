<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        $tickets = [
            [
                'subject' => 'Login issue with my account',
                'status' => 'open',
                'user_id' => $users->random()->id,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subHours(2),
                'messages' => [
                    [
                        'is_customer_message' => true,
                        'message' => "I'm unable to log in to my account. I keep getting an 'Invalid credentials' error even though I'm sure my password is correct.",
                    ],
                    [
                        'is_customer_message' => false,
                        'message' => "I understand you're having trouble logging in. Could you please try clearing your browser cache and cookies? Also, make sure caps lock is off when entering your password.",
                    ],
                    [
                        'is_customer_message' => true,
                        'message' => "I tried clearing my cache and cookies, but I'm still having the same issue. Could there be an issue with my account?",
                    ],
                ],
            ],
            [
                'subject' => 'Payment not processed',
                'status' => 'open',
                'user_id' => $users->random()->id,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subHours(5),
                'messages' => [
                    [
                        'is_customer_message' => true,
                        'message' => "I tried to make a payment but the transaction failed. The money was deducted from my account but the order shows as unpaid.",
                    ],
                    [
                        'is_customer_message' => false,
                        'message' => "I apologize for the inconvenience. I can see the pending transaction in our system. Please allow 24-48 hours for the payment to be reversed if you don't want to proceed.",
                    ],
                ],
            ],
            [
                'subject' => 'Feature request: dark mode',
                'status' => 'closed',
                'user_id' => $users->random()->id,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(1),
                'messages' => [
                    [
                        'is_customer_message' => true,
                        'message' => "It would be great if you could add a dark mode option. It's easier on the eyes when working late.",
                    ],
                    [
                        'is_customer_message' => false,
                        'message' => "Thank you for your suggestion! We've added this to our feature request list and our development team will review it.",
                    ],
                    [
                        'is_customer_message' => true,
                        'message' => "That's great to hear! Looking forward to this feature.",
                    ],
                    [
                        'is_customer_message' => false,
                        'message' => "We're happy to inform you that dark mode has been added to our development roadmap for the next quarter.",
                    ],
                    [
                        'is_customer_message' => true,
                        'message' => "Awesome! Thanks for keeping me updated.",
                    ],
                ],
            ],
            [
                'subject' => 'Cannot access my previous orders',
                'status' => 'resolved',
                'user_id' => $users->random()->id,
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subHours(12),
                'messages' => [
                    [
                        'is_customer_message' => true,
                        'message' => "I'm trying to view my order history but getting an error message. Can you help?",
                    ],
                    [
                        'is_customer_message' => false,
                        'message' => "Of course! We're currently experiencing some technical issues with the order history page. Our team is working on fixing this.",
                    ],
                    [
                        'is_customer_message' => false,
                        'message' => "The issue has been resolved now. You should be able to access your order history. Please let us know if you need anything else!",
                    ],
                ],
            ],
        ];

        foreach ($tickets as $ticketData) {
            $messages = $ticketData['messages'];
            unset($ticketData['messages']);
            
            // Create the ticket
            $ticket = Ticket::create($ticketData);
            
            // Create messages for this ticket
            foreach ($messages as $index => $messageData) {
                TicketMessage::create([
                    'ticket_id' => $ticket->id,
                    'is_customer_message' => $messageData['is_customer_message'],
                    'message' => $messageData['message'],
                    'created_at' => $ticket->created_at->addHours($index + 1),
                    'updated_at' => $ticket->created_at->addHours($index + 1),
                ]);
            }
        }
    }
}