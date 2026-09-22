<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rate Card - Kingdom Recruitments</title>
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

        /* ===== CONTENT BODY ===== */
        .content-body {
            padding: 30px 40px 100px 40px;
        }

        .document-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 10px 0 30px 0;
            letter-spacing: 0.3px;
        }

        /* ===== TABLE ===== */
        .rate-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }

        .rate-table thead tr {
            background: #b0a050;
        }

        .rate-table th {
            padding: 12px 20px;
            font-size: 13px;
            font-weight: bold;
            color: #fff;
            text-align: center;
            text-transform: capitalize;
            letter-spacing: 0.3px;
            border: 1px solid #9a8a40;
        }

        .rate-table td {
            padding: 12px 20px;
            font-size: 13px;
            text-align: center;
            border: 1px solid #d5d5d5;
            color: #333;
            vertical-align: middle;
        }

        .rate-table tbody tr:nth-child(even) {
            background-color: #fafaf5;
        }

        .rate-table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .rate-table .serial-col {
            width: 10%;
            font-weight: bold;
        }

        .rate-table .type-col {
            width: 35%;
            font-size: 13px;
        }

        .rate-table .venue-col {
            width: 25%;
            font-size: 13px;
        }

        .rate-table .gross-col {
            width: 15%;
            font-weight: 500;
        }

        .rate-table .net-col {
            width: 15%;
            font-weight: 600;
        }

        /* Empty row at bottom of table */
        .rate-table .empty-row td {
            height: 30px;
            background: #f5f0dc;
            border: 1px solid #d5d5d5;
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

        /* ===== GOLD ACCENT LINE ===== */
        .gold-line {
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #b8860b, #d4a843, #b8860b);
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
            @php
                $uniqueCompanies = $rates->pluck('company_name')->unique()->map(function($name) {
                    return $name ?: 'Global Rates';
                });
                $titleSuffix = '';
                if ($uniqueCompanies->count() === 1) {
                    $titleSuffix = ' - ' . $uniqueCompanies->first();
                }
            @endphp
            <div class="document-title">Rate Card{{ $titleSuffix }}</div>

            <table class="rate-table">
                <thead>
                    <tr>
                        <th class="serial-col">Serial</th>
                        <th class="type-col">Staff Type</th>
                        <th class="venue-col">Venue</th>
                        <th class="gross-col">Gross Rate</th>
                        <th class="net-col">Net Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $groupedRates = $rates->groupBy(function($rate) {
                            return $rate->company_name ?: 'Global Rates';
                        });
                        
                        $sortedGroupKeys = $groupedRates->keys()->sort(function($a, $b) {
                            if ($a === 'Global Rates') return 1;
                            if ($b === 'Global Rates') return -1;
                            return strcmp($a, $b);
                        });
                    @endphp

                    @foreach($sortedGroupKeys as $companyName)
                        @php
                            $groupRates = $groupedRates[$companyName];
                            $serial = 1;
                        @endphp
                        
                        <!-- Group Header Row -->
                        <tr class="partner-group-header">
                            <td colspan="5" style="text-align: left; background-color: #f1f5f9; font-weight: bold; color: #0f1f3d; padding: 10px 15px; border-left: 4px solid #b8860b; border-bottom: 2px solid #cbd5e1; font-size: 13px;">
                                {{ $companyName }}
                            </td>
                        </tr>

                        @foreach($groupRates as $rate)
                        <tr>
                            <td class="serial-col">{{ str_pad($serial++, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="type-col" style="text-align: left; padding-left: 15px;">{{ $rate->sub_category }}</td>
                            <td class="venue-col" style="text-align: center;">{{ $rate->venue->name ?? 'N/A' }}</td>
                            <td class="gross-col">£{{ number_format($rate->overtime_rate, 2) }}</td>
                            <td class="net-col" style="font-weight: bold; color: #1a365d;">£{{ number_format($rate->hourly_rate, 2) }}</td>
                        </tr>
                        @endforeach
                    @endforeach
                    <!-- Empty bottom row like the design -->
                    <tr class="empty-row">
                        <td colspan="5"></td>
                    </tr>
                </tbody>
            </table>
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
