<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\TicketMessage;

class TicketController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Create the ticket
        $ticket = Ticket::create([
            'subject' => $validated['subject'],
            'status' => 'open',
            'user_id' => auth()->id(),
        ]);

        // Create the first message
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'message' => $validated['message'],
            'is_customer_message' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket created successfully',
            'ticket' => $ticket->load('messages'),
        ]);
    }

    public function mytickets(Request $request)
    {
        if (!Auth::check()) {
            abort(401, 'Unauthorized access');
        }

        $query = Ticket::with(['user'])
        ->where('user_id', Auth::user()->id);
    
        $tickets = $query->paginate(10)->withQueryString();

        foreach ($tickets as $ticket) {
            // Get package count
            $ticket->message_count = TicketMessage::where('ticket_id', $ticket->id)->count();

        }

        return view('customers.tickets.support-tickets', [
            'tickets' => $tickets,
        ]);
    }

    public function ticketchat($ticketID)
    {
        if (!Auth::check()) {
            abort(401, 'Unauthorized access');
        }

        $ticket = Ticket::findOrFail($ticketID);

        if (Auth::user()->id !== $ticket->user_id) {
            abort(403, 'You are not authorized to access this invoice');
        }

        $messages = TicketMessage::where('ticket_id', $ticketID)
        ->orderBy('created_at', 'asc')
        ->get();

        return view('customers.tickets.ticket-chat', [
            'ticket' => $ticket,
            'messages' => $messages,
        ]);
    }

    public function newmessage(Request $request, $ticketID)
    {
        // Validate the request
        $validated = $request->validate([
            'message' => 'required|string',
        ]);
    
        $ticket = Ticket::findOrFail($ticketID);
    
        // Create the message
        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'message' => $validated['message'],
            'is_customer_message' => true,
        ]);
    
        // Load the created message with its relationships if needed
        $message->load(['ticket']);
    
        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $message
        ]);
    }
}
