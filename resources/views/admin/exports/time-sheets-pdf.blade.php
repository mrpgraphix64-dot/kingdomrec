<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Time Sheets Export</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        .header { margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; color: #1e293b; }
        .header p { margin: 5px 0 0; color: #64748b; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Time Sheets / Staff Quotations</h1>
        <p>Generated on {{ date('Y-m-d H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Ref</th>
                <th>Client</th>
                <th>Role</th>
                <th>Staff Count</th>
                <th>End Date</th>
                <th>Hours</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($timeSheets as $item)
            <tr>
                <td>{{ $item->quotation_ref ?? '-' }}</td>
                <td>{{ $item->client }}</td>
                <td>
                    {{ $item->category }}
                    @if($item->sub_category) <br><small>{{ $item->sub_category }}</small> @endif
                </td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->end_date ? \Carbon\Carbon::parse($item->end_date)->format('M d, Y') : '-' }}</td>
                <td>{{ $item->shift_hours ?? '-' }}</td>
                <td>{{ $item->amount }}</td>
                <td>{{ $item->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
