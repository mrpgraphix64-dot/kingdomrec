<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    /**
     * Smart predefined responses for common questions.
     */
    private function getResponses(): array
    {
        return [
            // ── Public Site / Job Seekers ──
            'find_jobs' => [
                'message' => "🔍 **Finding Jobs is Easy!**\n\nBrowse our latest openings across SIA Security, Hospitality, Events, Construction and more.\n\n👉 Visit our [Jobs Page](/portal) to search and apply.\n\nYou can filter by category, location, and job type.",
                'resolved' => true,
            ],
            'upload_cv' => [
                'message' => "📄 **Upload Your CV**\n\nTo upload your CV and get matched with opportunities:\n\n1. Go to the [Portal](/portal)\n2. Click **Register** or **Sign In**\n3. Navigate to your **Profile**\n4. Upload your CV in the documents section\n\nOur team will review it and match you with suitable roles!",
                'resolved' => true,
            ],
            'need_staff' => [
                'message' => "👥 **Need Staff for Your Business?**\n\nKingdom Recruitments provides vetted, professional staff for:\n\n• SIA Door Supervisors & Security\n• Hospitality & Events\n• Construction & Facilities\n\n📞 Call us: **+44 20 7946 0958**\n📧 Email: **info@kingdomrecruitments.com**\n\nOr register as a [Partner](/portal) for instant access to our booking system.",
                'resolved' => true,
            ],
            'contact_support' => [
                'message' => "📞 **Contact Kingdom Recruitments**\n\n**Phone:** +44 20 7946 0958\n**Email:** info@kingdomrecruitments.com\n**Address:** Kingdom House, London, UK\n\n🕐 **Office Hours:**\nMon–Fri: 8:00 AM – 6:00 PM\nSat: 9:00 AM – 2:00 PM\n\nOr visit our [Contact Page](/contact) to send a message.",
                'resolved' => true,
            ],
            'apply_jobs' => [
                'message' => "✅ **How to Apply for Jobs**\n\n1. Visit the [Portal](/portal)\n2. Create an account or sign in\n3. Browse available positions\n4. Click **Apply Now** on any role\n5. Upload your CV and complete your profile\n\nOur recruitment team will be in touch!",
                'resolved' => true,
            ],
            'edit_profile' => [
                'message' => "✏️ **Edit Your Profile**\n\n1. Sign in to the [Portal](/portal)\n2. Click on your **Profile** icon\n3. Update your personal details, skills, and documents\n4. Click **Save Changes**\n\nKeeping your profile updated helps us match you with the best roles!",
                'resolved' => true,
            ],

            // ── Partner Portal ──
            'book_staff' => [
                'message' => "📋 **Book Staff — Quick Guide**\n\n1. Go to **Book Staff** in your sidebar\n2. Select the **Event** you need staff for\n3. Choose the **Staff Role** (e.g., SIA Door Supervisor)\n4. Enter **Quantity** and **Hours**\n5. Add any **Special Requirements**\n6. Click **Submit Booking Request**\n\nWe'll confirm within 24 hours! 💪",
                'resolved' => true,
            ],
            'view_quotations' => [
                'message' => "📊 **Your Quotations**\n\nView and manage all your quotations:\n\n1. Go to **Quotations** in your sidebar\n2. See all pending, accepted, and completed quotes\n3. Click **Accept Quotation** to confirm\n\nNeed a custom quote? Contact your Account Manager directly.",
                'resolved' => true,
            ],
            'timesheets_help' => [
                'message' => "⏰ **Timesheets & Payroll**\n\nTrack staff hours and costs:\n\n1. Go to **Timesheets** in your sidebar\n2. View hours worked per staff member\n3. See rates and total amounts\n4. Approve or query individual entries\n\nSummary cards show your **Total Hours** and **Total Payroll Cost** at a glance.",
                'resolved' => true,
            ],
            'rate_card_info' => [
                'message' => "💰 **Rate Card Information**\n\nView current pricing for all staff roles:\n\n1. Go to **Rate Card** in your sidebar\n2. See **Gross Rate** and **Net Rate** for each role\n3. Rates are shown per hour, per day, and overtime\n\nRates are updated quarterly. Contact us for volume discounts!",
                'resolved' => true,
            ],
        ];
    }

    /**
     * Return predefined response for quick actions.
     */
    public function respond(Request $request)
    {
        $request->validate([
            'action' => 'required|string|max:50',
        ]);

        $responses = $this->getResponses();
        $action = $request->input('action');

        if (isset($responses[$action])) {
            return response()->json([
                'success' => true,
                'reply' => $responses[$action]['message'],
                'resolved' => $responses[$action]['resolved'],
            ]);
        }

        return response()->json([
            'success' => false,
            'reply' => "I'm not sure about that. Let me connect you with our support team. Please type your question below and we'll get back to you!",
            'resolved' => false,
        ]);
    }

    /**
     * Store a support message in DB when chatbot can't answer.
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'visitor_name' => 'nullable|string|max:100',
            'visitor_email' => 'nullable|email|max:100',
            'page_url' => 'nullable|string|max:255',
            'conversation_id' => 'nullable|string|max:36',
            'user_type' => 'nullable|string|in:visitor,applicant,partner,admin',
        ]);

        $chatMessage = ChatMessage::create([
            'conversation_id' => $request->input('conversation_id', Str::uuid()->toString()),
            'visitor_name' => $request->input('visitor_name', 'Anonymous Visitor'),
            'visitor_email' => $request->input('visitor_email'),
            'user_id' => auth()->id(),
            'user_type' => $request->input('user_type', 'visitor'),
            'message' => $request->input('message'),
            'page_url' => $request->input('page_url'),
            'status' => 'open',
        ]);

        return response()->json([
            'success' => true,
            'reply' => "✅ Your message has been sent to our support team. We'll reply as soon as possible!\n\nRef: #" . str_pad($chatMessage->id, 5, '0', STR_PAD_LEFT),
            'message_id' => $chatMessage->id,
            'conversation_id' => $chatMessage->conversation_id,
        ]);
    }
}
