<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Applicants Export</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        .header { margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; color: #1e293b; }
        .header p { margin: 5px 0 0; color: #64748b; font-size: 12px; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: bold; display: inline-block; }
        .status-hired { background-color: #dcfce7; color: #166534; }
        .status-rejected { background-color: #fee2e2; color: #991b1b; }
        .status-review { background-color: #dbeafe; color: #1e40af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Applicants List</h1>
        <p>Generated on {{ date('Y-m-d H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Role</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Applied Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applicants as $app)
            <tr>
                <td>{{ $app->id }}</td>
                <td>{{ $app->name }}</td>
                <td>{{ $app->role }}</td>
                <td>{{ $app->email }}</td>
                <td>{{ $app->phone }}</td>
                <td>
                    @php
                        $statusClass = match($app->status) {
                            'Hired' => 'status-hired',
                            'Rejected' => 'status-rejected',
                            default => 'status-review'
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ $app->status }}</span>
                </td>
                <td>{{ $app->created_at->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
