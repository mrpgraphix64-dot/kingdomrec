@extends('layouts.app')

@section('title', 'Terms of Service - Kingdom Recruitments')

@section('content')
<div class="bg-slate-50 min-h-screen py-20 font-inter pt-44">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 bg-white p-10 sm:p-14 rounded-3xl shadow-lg border border-slate-100">
        <h1 class="text-4xl font-extrabold text-slate-900 mb-6 tracking-tight">Terms of Service</h1>
        <p class="text-sm text-slate-500 mb-10 font-semibold tracking-wider uppercase">Last Updated: {{ date('F d, Y') }}</p>

        <div class="prose prose-slate max-w-none prose-headings:font-bold prose-headings:text-slate-900 prose-a:text-kingdom-red prose-a:no-underline hover:prose-a:underline">
            <p class="text-lg text-slate-600 font-medium leading-relaxed mb-8">Welcome to Kingdom Recruitments. These Terms of Service govern your use of our website and services. By accessing or using our platform, you agree to be bound by these terms.</p>

            <h2 class="text-2xl mt-10 mb-4 border-b border-slate-100 pb-2">1. Agreement to Terms</h2>
            <p>By using our Services, you agree to be bound by these Terms. If you don't agree to be bound by these Terms, do not use the Services.</p>

            <h2 class="text-2xl mt-10 mb-4 border-b border-slate-100 pb-2">2. User Accounts</h2>
            <p>When you create an account with us, you must provide accurate, complete, and current information. Failure to do so constitutes a breach of the Terms, which may result in immediate termination of your account on our Service.</p>
            <p>You are fully responsible for safeguarding the password that you use to access the Service and for any activities or actions under your password.</p>

            <h2 class="text-2xl mt-10 mb-4 border-b border-slate-100 pb-2">3. Services Provided</h2>
            <p>Kingdom Recruitments provides a platform connecting job seekers (Applicants) with employers (Partners). We strive to provide the best matches but do not guarantee employment or specific staffing outcomes.</p>

            <h2 class="text-2xl mt-10 mb-4 border-b border-slate-100 pb-2">4. User Conduct</h2>
            <p>You agree not to use the platform to:</p>
            <ul class="list-disc pl-5 space-y-2 mb-6">
                <li>Violate any local, national, or international law or regulation.</li>
                <li>Submit false, misleading, or deceptive information.</li>
                <li>Infringe upon the intellectual property rights of others.</li>
                <li>Harass, abuse, or harm another person.</li>
            </ul>

            <h2 class="text-2xl mt-10 mb-4 border-b border-slate-100 pb-2">5. Limitation of Liability</h2>
            <p>In no event shall Kingdom Recruitments, its directors, employees, or agents, be liable for any indirect, incidental, special, consequential or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from your access to or use of or inability to access or use the Service.</p>

            <h2 class="text-2xl mt-10 mb-4 border-b border-slate-100 pb-2">6. Changes to Terms</h2>
            <p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. We will provide notice of any significant changes. By continuing to access or use our Service after those revisions become effective, you agree to be bound by the revised terms.</p>

            <h2 class="text-2xl mt-10 mb-4 border-b border-slate-100 pb-2">7. Contact Us</h2>
            <p>If you have any questions about these Terms, please contact us:</p>
            <div class="bg-slate-50 p-6 rounded-xl border border-slate-100 mt-4">
                <p class="font-bold text-slate-900 mb-1">Kingdom Recruitments</p>
                <p class="text-slate-600 mb-1">Email: legal@kingdomrecruitments.com</p>
                <p class="text-slate-600 mb-1">Contact Page: <a href="{{ route('contact') }}">Contact Us</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
