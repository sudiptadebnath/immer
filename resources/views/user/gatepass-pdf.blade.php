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

        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th colspan="2">Puja Committee Details</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Name</strong></td>
                    <td>{{ $user->puja_committee_name }}</td>
                </tr>
                @if($user->action_area)
                <tr>
                    <td><strong>Location</strong></td>
                    <td>{{ $user->action_area }}, {{ $user->category }}</td>
                </tr>
                @endif
                @if($user->puja_committee_address)
                <tr>
                    <td><strong>Address</strong></td>
                    <td>{{ $user->puja_committee_address }}</td>
                </tr>
                @endif
                <tr>
                    <td><strong>Secretary</strong></td>
                    <td>{{ $user->secretary_name }} ({{ $user->secretary_mobile }})</td>
                </tr>
                <tr>
                    <td><strong>Chairman</strong></td>
                    <td>{{ $user->chairman_name }} ({{ $user->chairman_mobile }})</td>
                </tr>
                <tr>
                    <td><strong>Proposed Immersion</strong></td>
                    <td>
                        {{ $user->proposed_immersion_date ? \Carbon\Carbon::parse($user->proposed_immersion_date)->format('d M Y') : '' }}
                        {{ $user->proposed_immersion_time }}
                    </td>
                </tr>
                @if($user->vehicle_no)
                <tr>
                    <td><strong>Vehicle No</strong></td>
                    <td>{{ $user->vehicle_no }}</td>
                </tr>
                @endif
                @if(1>2 && $user->team_members)
                <tr>
                    <td><strong>Team Members</strong></td>
                    <td>{{ $user->team_members }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</body>
</html>
