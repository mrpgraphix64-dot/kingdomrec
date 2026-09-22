<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Applicant Profile - {{ $applicant->name }}</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background: #fff;
            line-height: 1.5;
        }

        .page-wrapper {
            position: relative;
            width: 100%;
            min-height: 100%;
        }

        /* ===== HEADER BANNER ===== */
        .header-banner {
            width: 100%;
            height: 120px;
            background: #ffffff;
            position: relative;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-left {
            width: 50%;
            padding: 20px 40px;
            background: #ffffff;
            vertical-align: middle;
        }

        .header-right {
            width: 50%;
            padding: 20px 40px;
            background: #b8860b;
            text-align: right;
            vertical-align: middle;
        }

        .address-text {
            margin: 0;
            font-size: 12px;
            color: #ffffff;
            line-height: 1.5;
            font-weight: bold;
        }

        .logo-icon {
            display: inline-block;
            width: 50px;
            height: 50px;
            border-radius: 25px;
            border: 3px solid #1a365d;
            background: #1a365d;
            text-align: center;
            line-height: 50px;
            color: #d4a843;
            font-size: 22px;
            font-weight: bold;
            vertical-align: middle;
            margin-right: 10px;
        }

        .logo-text {
            display: inline-block;
            vertical-align: middle;
            padding-top: 2px;
            text-align: left;
        }

        .logo-text .brand-name {
            font-size: 26px;
            font-weight: bold;
            color: #1a365d;
            letter-spacing: -0.5px;
            line-height: 1.1;
        }

        .logo-text .brand-sub {
            font-size: 16px;
            color: #1a365d;
            font-weight: 300;
            letter-spacing: 1px;
        }

        /* ===== GOLD ACCENT LINE ===== */
        .gold-line {
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #b8860b, #d4a843, #b8860b);
        }

        /* ===== CONTENT BODY ===== */
        .content-body {
            padding: 30px 40px 100px 40px;
        }

        .profile-header {
            margin-bottom: 25px;
            border-bottom: 2px solid #b8860b;
            padding-bottom: 15px;
        }

        .profile-title {
            font-size: 24px;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 5px;
        }

        .profile-subtitle {
            font-size: 13px;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: bold;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #b8860b;
            text-transform: uppercase;
            border-bottom: 1px solid #E2E8F0;
            padding-bottom: 5px;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }

        .grid {
            width: 100%;
            border-collapse: collapse;
        }

        .grid td {
            vertical-align: top;
            padding: 6px 0;
        }

        .label {
            width: 150px;
            font-weight: bold;
            color: #64748B;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .value {
            color: #1a365d;
            font-weight: 500;
            font-size: 13px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            background-color: #f5f0dc;
            color: #b8860b;
            border: 1px solid rgba(184, 134, 11, 0.2);
        }

        /* ===== FOOTER ===== */
        .footer-banner {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #1a365d;
            padding: 20px 0;
            text-align: center;
            border-top: 4px solid #b8860b;
        }

        .footer-banner p {
            margin: 0;
            font-size: 14px;
            color: #ffffff;
            line-height: 1.6;
            font-weight: bold;
        }

        .footer-banner .highlight {
            color: #d4a843;
            font-size: 16px;
        }

        .footer-banner a {
            color: #ffffff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <!-- Header Banner -->
        <div class="header-banner">
            <table class="header-table">
                <tr>
                    <td class="header-left">
                        @php
                            $logoPath = public_path('images/kr_logo.png');
                            $logoBase64 = '';
                            if (file_exists($logoPath)) {
                                $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
                            }
                        @endphp
                        @if($logoBase64)
                            <img src="{{ $logoBase64 }}" alt="Kingdom Recruitments" style="height: 60px;">
                        @else
                            <div class="logo-icon">K</div>
                            <div class="logo-text">
                                <div class="brand-name">Kingdom</div>
                                <div class="brand-sub">Recruitments</div>
                            </div>
                        @endif
                    </td>
                    <td class="header-right">
                        <div class="address-text">
                            Greatorex Business Center<br>
                            Suite No: 101 (First floor)<br>
                            8-10 Greatorex Street<br>
                            London E1 5NF
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="gold-line"></div>

        <!-- Content Body -->
        <div class="content-body">
            <div class="profile-header">
                <div class="profile-title">{{ $applicant->name }}</div>
                <div class="profile-subtitle">Applicant Profile • ID {{ $applicant->id }}</div>
            </div>

            <div class="section">
                <div class="section-title">Application Details</div>
                <table class="grid">
                    <tr>
                        <td class="label">Applying For</td>
                        <td class="value">{{ $applicant->role === 'applicant' ? 'Applicant' : ucfirst($applicant->role) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Current Status</td>
                        <td class="value">
                            <span class="status-badge">{{ $applicant->status }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Applied On</td>
                        <td class="value">{{ $applicant->created_at->format('F d, Y') }}</td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <div class="section-title">Contact Information</div>
                <table class="grid">
                    <tr>
                        <td class="label">Email Address</td>
                        <td class="value">{{ $applicant->email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Phone Number</td>
                        <td class="value">{{ $applicant->phone ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Location</td>
                        <td class="value">{{ $applicant->location ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>

            @if($applicant->sub_category)
            <div class="section">
                <div class="section-title">Specialization</div>
                <div style="font-weight: bold; color: #1a365d; font-size: 13px;">{{ $applicant->sub_category }}</div>
            </div>
            @endif

            @if($applicant->experiences && $applicant->experiences->count() > 0)
            <div class="section">
                <div class="section-title">Work Experience</div>
                @foreach($applicant->experiences as $exp)
                <div style="margin-bottom: 15px;">
                    <div style="font-weight: bold; color: #1a365d; font-size: 14px;">{{ $exp->job_title }}</div>
                    <div style="color: #64748B; font-size: 12px; margin-bottom: 4px; font-weight: bold;">
                        {{ $exp->company }} • 
                        {{ $exp->start_date ? \Carbon\Carbon::parse($exp->start_date)->format('M Y') : 'N/A' }} - 
                        {{ $exp->is_current ? 'Present' : ($exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('M Y') : 'N/A') }}
                    </div>
                    @if($exp->description)
                    <div style="font-size: 12px; color: #475569; white-space: pre-line; margin-top: 3px;">{{ $exp->description }}</div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            @if($applicant->educations && $applicant->educations->count() > 0)
            <div class="section">
                <div class="section-title">Education</div>
                @foreach($applicant->educations as $edu)
                <div style="margin-bottom: 15px;">
                    <div style="font-weight: bold; color: #1a365d; font-size: 14px;">{{ $edu->degree }}</div>
                    <div style="color: #64748B; font-size: 12px; margin-bottom: 4px; font-weight: bold;">
                        {{ $edu->institution }} • 
                        {{ $edu->start_date ? \Carbon\Carbon::parse($edu->start_date)->format('Y') : 'N/A' }} - 
                        {{ $edu->is_current ? 'Present' : ($edu->end_date ? \Carbon\Carbon::parse($edu->end_date)->format('Y') : 'N/A') }}
                    </div>
                    @if($edu->description)
                    <div style="font-size: 12px; color: #475569; white-space: pre-line; margin-top: 3px;">{{ $edu->description }}</div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Footer Banner -->
        <div class="footer-banner">
            <p>
                m: <span class="highlight">074 1115 4198</span>
            </p>
            <p>
                e: <a href="mailto:info@kingdomrecruitments.com">info@kingdomrecruitments.com</a> w: <a href="https://kingdomrecruitments.com">kingdomrecruitments.com</a>
            </p>
        </div>
    </div>
</body>
</html>
