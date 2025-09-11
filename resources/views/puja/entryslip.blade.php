<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>GatePass</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            /*background: none;*/
        }

        .visitor_form {
            position: relative;
            padding: 10px;
            border: 1px solid #585858;
            max-width: 600px;
            margin: auto;
            background-image: url({{asset('resources/img/bg2.jpg') }});
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            z-index: 1;
        }

        .visitor_form::before {
            content: "";
            position: absolute;
            inset: 0;
            background-color: rgba(255, 255, 255, 0.7);
            z-index: -1;
        }


        .visitor_form .topbar {
            display: flex;
            align-items: center;
            padding-bottom: 10px;
            gap: 10px;
            border-bottom: 1px solid #585858;
        }

        .visitor_form .topbar .logo {
            width: 35px;
            height: auto;
        }

        .visitor_form .topbar .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .visitor_form .logo_desc h2 {
            margin: 0 0 3px 0;
            padding: 0;
            font-size: 1em;
            color: #333;
        }

        .visitor_form .logo_desc p {
            margin: 0;
            padding: 0;
            font-size: .7em;
            color: #585858;
        }

        .visitor_form .cardhead {
            text-align: center;
            font-size: 1em;
            color: #ae3707;
            margin: 5px 0 15px;
            text-decoration: underline;
        }

        .visitor_form .info {
            font-size: 0.7em;
            margin-bottom: 10px;
            display: flex;
            align-items: start;
            gap: 10px;
            color: #585858;
            /*text-transform: capitalize;*/
        }
        .info2 {
            font-size: 0.7em;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: black;
        }


        .visitor_form .info.bold {
            font-weight: bold;
            color: #333;
        }

        .visitor_form .info .purpose {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .visitor_form .label {
            font-weight: bold;
            color: #333;
            min-width: 88px;
            max-width: 88px;
        }

        .visitor_form .wraper {
            display: flex;
            justify-content: space-between;
        }

        .visitor_form .imagepic {
            width: 100px;
            height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-left: auto;
            border: dashed 1px #ccc;
            padding: 5px;
        }

        .visitor_form .imagepic img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .visitor_form .bottombar {
            width: 100%;
            height: auto;
            display: flex;
            justify-content: space-between;
        }

        .visitor_form .countersig {
            width: 100%;
            max-width: 150px;
            height: auto;
            margin-left: auto;
            margin-top: auto;
            padding: 0 0 10px 15px;
        }

        .visitor_form .countersig .sigtext {
            font-size: .6em;
            color: #333;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        .visitor_form .countersig .blank {
            width: 100%;
            height: auto;
            border-bottom: 1px solid #ccc;
            padding: 0;
            margin-bottom: 2px;
        }
    </style>
</head>

<body>
    <div class="visitor_form">
        <div class="topbar">
            <div class="logo">
                <img src="{{asset("resources/img/logo-nkda.png")}}" alt="Logo" />
            </div>
            <div class="logo_desc">
                <h2>NEW TOWN KOLKATA DEVELOPMENT AUTHORITY</h2>
                <p>Administrative Building. Plot No - DG/13, Premises No - 04-3333, Action Area - ID, New Town, Kolkata - 700156</p>
            </div>
        </div>
        <h6 class="cardhead">Immersion Pass</h6>
        <div class="wraper">
            <div class="leftpart">
                <div class="info bold">
                    <span class="label">Name:</span>
                    <span>{{ $puja->puja_committee_name }}</span>
                </div>
                @if($puja->action_area)
                <div class="info">
                    <span class="label">Location:</span>
                    <span>{{ $puja->action_area }}, {{ $puja->category }}</span>
                </div>
                @endif
                @if($puja->puja_committee_address)
                <div class="info">
                    <span class="label">Address:</span>
                    <span>{{ $puja->puja_committee_address }}</span>
                </div>
                @endif
                <div class="info">
                    <span class="label">Secretary:</span>
                    <span>{{ $puja->secretary_name }} - <b>{{ $puja->secretary_mobile }}</b></span>
                </div>
                <div class="info">
                    <span class="label">Chairman:</span>
                    <span>{{ $puja->chairman_name }} - <b>{{ $puja->chairman_mobile }}</b></span>
                </div>
                <div class="info">
                    <span class="label">Date of Immersion:</span>
                    <span>
						{{ $puja->proposed_immersion_date ? \Carbon\Carbon::parse($puja->proposed_immersion_date)->format('d M Y') : '' }}
						{{ $puja->proposed_immersion_time ? \Carbon\Carbon::parse($puja->proposed_immersion_time)->format('h:i A') : '' }}
					</span>
                </div>
                @if($repoAtt && $repoAtt->scan_datetime)
                <div class="info">
                    <span class="label">Reported At:</span>
                    <span>{{ \Carbon\Carbon::parse($repoAtt->scan_datetime)->format('d M Y h:i A') }}</span>
                </div>
                @endif
                @if(1>2 && $puja->vehicle_no)
                <div class="info">
                    <span class="label">Vehicle No:</span>
                    <span>{{ $puja->vehicle_no }}</span>
                </div>
                @endif
            </div>
            <div class="rightpart">
                <div class="imagepic">
                    <img src="{{asset("qrs/{$puja->id}.png")}}" alt="QR Code">
                </div>
                <div class="info2">
                    <span>Puja in New Town Area ?</span>
                    <span>{{ $puja->action_area ? "✔️" : "❌" }}</span>
                </div>
            </div>
        </div>
        <!-- <div class="bottombar">
            <div class="info">
                <span class="label">Address:</span>
                <span>Administrative Building. Plot No - DG/13, Premises
                    No - 04-3333, Action Area - ID, New Town, Kolkata - 700156</span>
            </div>
            <div class="countersig">
                <p class="blank"></p>
                <p class="sigtext">Counter Signature</p>
            </div>
        </div> -->
        <!-- <div class="info" style="font-size: 0.6em; margin: 0; padding-top: 10px; border-top: 1px solid #ccc;">
            <span class="label" style="min-width: auto;">Note:</span>
            <span>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has
                been the industry's standard dummy text ever since the 1500s,</span>
        </div> -->
    </div>
</body>

</html>