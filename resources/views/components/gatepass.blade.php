@props([
"file",
"puja",
"pdf",
])
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>GatePass</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            @if($pdf) margin: 0 auto;
            padding: 0;
            @else margin: 20;
            padding: 20;
            @endif
        }

        .card {
            @if($pdf) padding: 5px;
            margin: 0 auto;
            @else max-width: 500px;
            padding: 5px;
            margin: 0 auto;
            box-shadow: 3px 3px 8px rgba(0, 0, 0, 0.3);
            @endif border: 1px solid #000000a4;
            width: 100%;
            box-sizing: border-box;
        }

        .Registration_card {
            position: relative;
            /* padding: 10px;
            border: 1px solid #585858;
            max-width: 600px;
            margin: 0 auto; */
            background-image: url(/public/resources/img/bg2.jpg);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            z-index: 1;
        }

        .Registration_card .head {
            text-align: center;
            margin: 5px 0 10px 0;
        }

        .Registration_card::before {
            content: "";
            position: absolute;
            inset: 0;
            background-color: rgba(255, 255, 255, 0.7);
            z-index: -1;
        }

        h2 {
            text-align: center;
            margin: 5px 0 10px 0;
        }

        .content {
            display: table;
            width: 100%;
        }

        .qr-cell,
        .details-cell {
            display: table-cell;
            vertical-align: top;
        }

        .qr-cell {
            width: 35%;
            text-align: center;
            /* padding-right: 2px; */
        }

        .qr-cell img {
            max-width: 100%;
            height: auto;
            object-fit: contain;
        }

        .details-cell {
            width: 65%;
            padding-left: 5px;
        }

        table.details {
            width: 100%;
            border-collapse: collapse;
        }

        table.details th,
        table.details td {
            border: 1px solid #000000a4;
            font-size: 11px;
            text-align: left;
        }

        table.details th {
            background: #fff;
            text-align: center;
            padding: 4px 5px;
        }

        table.details td {
            padding: 2px 5px;
        }

        .download-container {
            text-align: center;
            margin-top: 20px;
        }

        .btn-danger {
            display: inline-block;
            padding: 8px 16px;
            background: #dc3545;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-family: DejaVu Sans, sans-serif;
        }

        .btn-danger:hover {
            background: #bb2d3b;
        }
    </style>
</head>

<body>
    <div class="card Registration_card">
        <h4 class="head">Digital Pass for immersion of Durga Idol at NKDA Bisarjan Ghat</h4>
        <div class="content">
            <!-- QR Code -->
            <div class="qr-cell">
                <img src="{{ $file }}" width="150" alt="QR Code">
            </div>

            <!-- Details Table -->
            <div class="details-cell">
                <table class="details">
                    <thead>
                        <tr>
                            <th colspan="2">Puja Committee Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Name</strong></td>
                            <td>{{ $puja->puja_committee_name }}</td>
                        </tr>
                        @if($puja->action_area)
                        <tr>
                            <td><strong>Location</strong></td>
                            <td>{{ $puja->action_area }}, {{ $puja->category }}</td>
                        </tr>
                        @endif
                        @if($puja->puja_committee_address)
                        <tr>
                            <td><strong>Address</strong></td>
                            <td>{{ $puja->puja_committee_address }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>Secretary</strong></td>
                            <td>{{ $puja->secretary_name }} - <b>{{ $puja->secretary_mobile }}</b></td>
                        </tr>
                        <tr>
                            <td><strong>Chairman</strong></td>
                            <td>{{ $puja->chairman_name }} - <b>{{ $puja->chairman_mobile }}</b></td>
                        </tr>
                        <tr>
                            <td><strong>Date of Immersion</strong></td>
                            <td>
                                {{ $puja->proposed_immersion_date ? \Carbon\Carbon::parse($puja->proposed_immersion_date)->format('d M Y') : '' }}
                                {{ $puja->proposed_immersion_time ? \Carbon\Carbon::parse($puja->proposed_immersion_time)->format('h:i A') : '' }}
                            </td>
                        </tr>
                        @if(1>2 && $puja->vehicle_no)
                        <tr>
                            <td><strong>Vehicle No</strong></td>
                            <td>{{ $puja->vehicle_no }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{ $slot }}
</body>

</html>