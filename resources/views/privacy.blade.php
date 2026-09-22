@extends('layouts.app')

@section('title', 'Privacy Policy - Kingdom Recruitments')

@section('content')
<div class="bg-slate-50 min-h-screen py-20 font-inter pt-44">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 bg-white p-10 sm:p-14 rounded-3xl shadow-lg border border-slate-100">
        <h1 class="text-4xl font-extrabold text-slate-900 mb-6 tracking-tight">Privacy Policy</h1>
        <p class="text-sm text-slate-500 mb-10 font-semibold tracking-wider uppercase">Last Updated: {{ date('F d, Y') }}</p>

        <div class="prose prose-slate max-w-none prose-headings:font-bold prose-headings:text-slate-900 prose-a:text-kingdom-red prose-a:no-underline hover:prose-a:underline">
            <p class="text-lg text-slate-600 font-medium leading-relaxed mb-8">At Kingdom Recruitments, we take your privacy seriously. This Privacy Policy outlines how we collect, use, disclose, and safeguard your information when you visit our website or use our services.</p>

            <h2 class="text-2xl mt-10 mb-4 border-b border-slate-100 pb-2">1. Information We Collect</h2>
            <p>We may collect information about you in a variety of ways, including:</p>
            <ul class="list-disc pl-5 space-y-2 mb-6">
                <li><strong>Personal Data:</strong> Personally identifiable information, such as your name, email address, telephone number, and demographic information that you voluntarily give to us when registering.</li>
                <li><strong>Professional Data:</strong> Resumes, employment history, qualifications, and other information relevant to job applications.</li>
                <li><strong>Derivative Data:</strong> Information our servers automatically collect when you access the site, such as your IP address, browser type, operating system, and access times.</li>
            </ul>

            <h2 class="text-2xl mt-10 mb-4 border-b border-slate-100 pb-2">2. Use of Your Information</h2>
            <p>Having accurate information about you permits us to provide you with a smooth, efficient, and customized experience. Specifically, we may use information collected about you to:</p>
            <ul class="list-disc pl-5 space-y-2 mb-6">
                <li>Create and manage your account.</li>
                <li>Process your job applications or staffing requests.</li>
                <li>Email you regarding your account, job matches, or staffing updates.</li>
                <li>Improve our website and services to better serve you.</li>
            </ul>

            <h2 class="text-2xl mt-10 mb-4 border-b border-slate-100 pb-2">3. Disclosure of Your Information</h2>
            <p>We may share information we have collected about you in certain situations. Your information may be disclosed as follows:</p>
            <ul class="list-disc pl-5 space-y-2 mb-6">
                <li><strong>To Partners/Employers:</strong> If you are an applicant, we will share your profile and CV with potential employers (Partners) to facilitate job placements.</li>
                <li><strong>By Law or to Protect Rights:</strong> If we believe the release of information about you is necessary to respond to legal process.</li>
            </ul>

            <h2 class="text-2xl mt-10 mb-4 border-b border-slate-100 pb-2">4. Security of Your Information</h2>
            <p>We use administrative, technical, and physical security measures to help protect your personal information. While we have taken reasonable steps to secure the personal information you provide to us, please be aware that despite our efforts, no security measures are perfect or impenetrable.</p>

            <h2 class="text-2xl mt-10 mb-4 border-b border-slate-100 pb-2">5. Contact Us</h2>
            <p>If you have questions or comments about this Privacy Policy, please contact us at:</p>
            <div class="bg-slate-50 p-6 rounded-xl border border-slate-100 mt-4">
                <p class="font-bold text-slate-900 mb-1">Kingdom Recruitments</p>
                <p class="text-slate-600 mb-1">Email: privacy@kingdomrecruitments.com</p>
                <p class="text-slate-600 mb-1">Phone: +44 123 456 7890</p>
            </div>
        </div>
    </div>
</div>
@endsection
