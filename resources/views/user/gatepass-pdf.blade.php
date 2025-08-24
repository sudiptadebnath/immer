<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>GatePass</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .container { text-align: center; }
        .qr { margin: 20px 0; }
        table { margin: 0 auto; border-collapse: collapse; }
        td { padding: 8px 12px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="container">
        <h2>GatePass</h2>
        <div class="qr">
            <img src="{{ $file }}" width="200" alt="QR Code">
        </div>

        <table>
            <tr>
                <td><strong>User ID</strong></td>
                <td>{{ $user->id }}</td>
            </tr>
            <tr>
                <td><strong>UID</strong></td>
                <td>{{ $user->uid }}</td>
            </tr>
            <tr>
                <td><strong>Email</strong></td>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <td><strong>Generated At</strong></td>
                <td>{{ now()->format('d M Y H:i') }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
