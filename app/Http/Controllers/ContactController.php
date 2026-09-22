<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'role' => 'nullable|string|max:100',
            'message' => 'required|string|max:2000',
            'consent' => 'accepted',
        ], [
            'consent.accepted' => 'You must agree to the Privacy Policy to proceed.',
        ]);

        $formattedMessage = "Role: " . ($request->input('role') ?? 'Visitor') . "\nPhone: " . $request->input('phone') . "\n\nMessage:\n" . $request->input('message');

        ChatMessage::create([
            'conversation_id' => 'contact-inquiry-' . uniqid(),
            'visitor_name' => $request->input('name'),
            'visitor_email' => $request->input('email'),
            'message' => $formattedMessage,
            'user_type' => 'visitor',
            'status' => 'open',
        ]);

        return back()->with('success', 'Thank you for your message! Our team will get back to you shortly.');
    }
}

