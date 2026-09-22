<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>System Users Export</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { bg-color: #f3f4f6; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        .header { margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; color: #1e293b; }
        .header p { margin: 5px 0 0; color: #64748b; font-size: 12px; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: bold; display: inline-block; }
        .bg-green { background-color: #dcfce7; color: #166534; }
        .bg-blue { background-color: #dbeafe; color: #1e40af; }
        .bg-gray { background-color: #f3f4f6; color: #4b5563; }
    </style>
</head>
<body>
    <div class="header">
        <h1>System Users</h1>
        <p>Generated on {{ date('Y-m-d H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Company</th>
                <th>Joined Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <span class="badge {{ $user->role === 'admin' ? 'bg-blue' : ($user->role === 'partner' ? 'bg-green' : 'bg-gray') }}">
                        {{ $user->role === 'applicant' ? 'Applicant' : ucfirst($user->role) }}
                    </span>
                </td>
                <td>{{ $user->company_name ?? '-' }}</td>
                <td>{{ $user->created_at->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
