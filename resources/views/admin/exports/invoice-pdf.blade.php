<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice - {{ $invoice->invoice_ref }}</title>
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
            padding: 30px 40px 120px 40px;
        }

        .document-title {
            font-size: 20px;
            font-weight: bold;
            color: #1a365d;
            margin: 0 0 25px 0;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .details-table td {
            vertical-align: top;
        }

        .section-title {
            font-size: 11px;
            font-weight: 800;
            color: #b8860b;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 8px;
            border-bottom: 2px solid #1a365d;
            padding-bottom: 4px;
        }

        .detail-text {
            color: #475569;
            font-size: 12px;
            line-height: 1.6;
        }

        .detail-text strong {
            color: #1a365d;
        }

        /* ===== ITEMS TABLE ===== */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table thead tr {
            background: #1a365d;
        }

        .items-table th {
            padding: 10px 15px;
            font-size: 11px;
            font-weight: bold;
            color: #ffffff;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #1a365d;
        }

        .items-table th.num-col {
            text-align: right;
        }

        .items-table td {
            padding: 10px 15px;
            font-size: 12px;
            border: 1px solid #e2e8f0;
            color: #333;
            vertical-align: middle;
        }

        .items-table td.num-col {
            text-align: right;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #fafaf5;
        }

        .items-table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .item-desc {
            font-weight: bold;
            color: #1a365d;
        }

        /* ===== SUMMARY SECTION ===== */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .summary-table td {
            vertical-align: top;
        }

        .notes-section {
            width: 55%;
            padding-right: 40px;
        }

        .totals-section {
            width: 45%;
        }

        .totals-box {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-box td {
            padding: 6px 10px;
            font-size: 12px;
            color: #475569;
        }

        .totals-box td.label {
            text-align: right;
            font-weight: bold;
            width: 60%;
            color: #1a365d;
        }

        .totals-box td.val {
            text-align: right;
            width: 40%;
            font-weight: 600;
        }

        .totals-box tr.grand-total td {
            font-size: 14px;
            font-weight: 800;
            color: #ffffff;
            background-color: #1a365d;
            border: 1px solid #1a365d;
            padding: 8px 10px;
        }

        .totals-box tr.grand-total td.val {
            color: #d4a843;
        }

        .totals-box tr.balance-due td {
            font-size: 13px;
            font-weight: bold;
            color: #e11d48;
            background-color: #fff1f2;
            border: 1px solid #fecdd3;
            padding: 6px 10px;
        }

        .totals-box tr.balance-due td.val {
            color: #be123c;
        }

        .payment-instructions {
            margin-top: 20px;
            background-color: #fafaf5;
            border: 1px solid #e5e5d8;
            padding: 15px;
            border-radius: 4px;
        }

        .payment-instructions p {
            margin: 4px 0;
            font-size: 11px;
            color: #475569;
        }

        .payment-instructions strong {
            color: #1a365d;
        }

        /* ===== FOOTER ===== */
        .footer-banner {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #1a365d;
            padding: 15px 0;
            text-align: center;
            border-top: 4px solid #b8860b;
            z-index: 9999;
        }

        .footer-banner p {
            margin: 0;
            font-size: 12px;
            color: #ffffff;
            line-height: 1.5;
            font-weight: bold;
        }

        .footer-banner .highlight {
            color: #d4a843;
            font-size: 13px;
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
            <div class="document-title">Invoice - {{ $invoice->invoice_ref }}</div>

            <!-- Details Table -->
            <table class="details-table">
                <tr>
                    <td style="width: 50%; padding-right: 20px;">
                        <div class="section-title">Bill To</div>
                        <div class="detail-text">
                            <strong>{{ $invoice->company_name }}</strong><br>
                            @if($invoice->user && $invoice->user->address)
                                {!! nl2br(e($invoice->user->address)) !!}
                            @else
                                Billing Address Registered On Portal
                            @endif
                            <br>
                            Email: {{ $invoice->user->email ?? 'N/A' }}
                        </div>
                    </td>
                    <td style="width: 50%; padding-left: 20px;">
                        <div class="section-title">Invoice Details</div>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 3px 0; font-size: 12px; color: #475569;"><strong>Invoice Ref:</strong></td>
                                <td style="padding: 3px 0; font-size: 12px; color: #1a365d; text-align: right; font-weight: bold;">{{ $invoice->invoice_ref }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 3px 0; font-size: 12px; color: #475569;"><strong>Date of Issue:</strong></td>
                                <td style="padding: 3px 0; font-size: 12px; color: #1a365d; text-align: right; font-weight: bold;">{{ $invoice->issue_date ? $invoice->issue_date->format('d M, Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 3px 0; font-size: 12px; color: #475569;"><strong>Due Date:</strong></td>
                                <td style="padding: 3px 0; font-size: 12px; color: #1a365d; text-align: right; font-weight: bold;">{{ $invoice->due_date ? $invoice->due_date->format('d M, Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 3px 0; font-size: 12px; color: #475569;"><strong>Payment Terms:</strong></td>
                                <td style="padding: 3px 0; font-size: 12px; color: #1a365d; text-align: right; font-weight: bold;">14 Days Terms</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- Items Table -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="num-col" style="width: 80px;">Slots</th>
                        <th class="num-col" style="width: 100px;">Hours</th>
                        <th class="num-col" style="width: 100px;">Rate</th>
                        <th class="num-col" style="width: 120px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>
                            <span class="item-desc">{{ $item->description }}</span>
                        </td>
                        <td class="num-col">{{ $item->quantity }}</td>
                        <td class="num-col">{{ number_format($item->hours, 2) }} hrs</td>
                        <td class="num-col">£{{ number_format($item->rate, 2) }}</td>
                        <td class="num-col" style="font-weight: bold; color: #1a365d;">£{{ number_format($item->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Summary Table -->
            <table class="summary-table">
                <tr>
                    <td class="notes-section" style="width: 55%;"></td>
                    <td class="totals-section">
                        <table class="totals-box">
                            <tr>
                                <td class="label">Subtotal</td>
                                <td class="val">£{{ number_format($invoice->subtotal, 2) }}</td>
                            </tr>
                            @if($invoice->tax_amount > 0)
                            <tr>
                                <td class="label">VAT ({{ number_format($invoice->tax_rate, 1) }}%)</td>
                                <td class="val">£{{ number_format($invoice->tax_amount, 2) }}</td>
                            </tr>
                            @endif
                            @if($invoice->discount_amount > 0)
                            <tr>
                                <td class="label">Discount</td>
                                <td class="val">-£{{ number_format($invoice->discount_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="grand-total">
                                <td class="label" style="color: #ffffff;">Total Amount</td>
                                <td class="val">£{{ number_format($invoice->total_amount, 2) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer Banner -->
        <div class="footer-banner">
            <p>
                m: <span class="highlight">074 1115 4198</span> &nbsp;&bull;&nbsp; 
                e: <a href="mailto:info@kingdomrecruitments.com">info@kingdomrecruitments.com</a> &nbsp;&bull;&nbsp; 
                w: <a href="https://kingdomrecruitments.com">kingdomrecruitments.com</a>
            </p>
        </div>
    </div>
</body>
</html>
