@extends('layouts.app')

@section('content')

<div class="container mt-3">
<div class="row g-2 justify-content-center">
    {{-- <x-select size="4" icon="box-arrow-in-right" name="typ" title="Type" :value="attDict()" sel="queue" /> --}}

    <div class="col-md-2">
        <x-button name="toggle-scan" icon="qr-code-scan" size="" title="QR" onclick="toggleScan()" />
        <x-button name="toggle-scan-otp" icon="key" size="" style="info" title="Mobile" onclick="showMarkByMob()" />
    </div>

    <!-- OTP Input -->
    <div id="mark-by-mob" class="col-md-12 d-flex justify-content-center d-none">
        <x-number size="6" name="mobile" title="Mobile Number" icon="telephone">
            <x-button size="" icon="send" title="Go" style="warning" onclick="markByMob()" />
        </x-number>
    </div>

    <div class="col-md-12 d-flex flex-column align-items-center justify-content-center">
        <div id="qr-reader" style="width:100%; max-width:500px; display:none;"></div>
        <div id="qr-result" class="alert alert-primary mt-2 w-100 d-none"></div>
    </div>
</div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
let html5QrCode;
let isScanning = false;

function showMarkByMob() {
    $('#mark-by-mob').removeClass('d-none');
}

// function toggle_scan_otp() {
//     const val = $('#typ').val();
//     if (val === 'queue') {
//         $('#toggle-scan-otp').removeClass('d-none');
//     } else {
//         $('#toggle-scan-otp').addClass('d-none');
//         $('#mark-by-mob').addClass('d-none');
//     }
// }

// $(function() {
//     $('#typ').change(toggle_scan_otp);
//     toggle_scan_otp();
// });

// function validate() {
//     const typ  = $('#typ').val();
//     if (!typ) {
//         myAlert("Please select Type before proceed.", "danger");
//         return false;
//     }
//     return true;
// }

function toggleScan() {
    $('#mark-by-mob').addClass('d-none');
    const btn = $('#toggle-scan span'); // span holds Start/Stop text

    if (!isScanning) {
        //if (!validate()) return;

        btn.text("Stop");
        $('#qr-result').html('');
        $('#qr-result').hide();
        $('#qr-reader').show();

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("qr-reader");
        }

        html5QrCode.start(
            { facingMode: "environment" }, // back camera
            { fps: 10, qrbox: 250 },
            onScanSuccess
        ).then(() => {
            isScanning = true;
        }).catch(err => {
            $('#qr-result').html("Camera error: " + err);
            btn.text("Start");
            $('#qr-result').hide();
            $('#qr-reader').hide();
        });

    } else {
        stopScan();
    }
}

function stopScan() {
    if (html5QrCode && isScanning) {
        html5QrCode.stop().then(() => {
            isScanning = false;
            $('#toggle-scan span').text("Start");
            $('#qr-result').hide();
            $('#qr-reader').hide();
        }).catch(err => {
            console.error("Stop failed", err);
        });
    }
}

function onScanSuccess(decodedText, decodedResult) {
    stopScan();
    const typ  = $('#typ').val();
    webserv("POST", "{{ route('att.mark_by_qr') }}", { 
        token: decodedText
    }, function ok(resp) {
        $('#qr-result')
        .removeClass('d-none alert-danger alert-success alert-primary')
        .addClass('alert-success')
        .html(resp.msg)
        .show();
    }, function fail(resp) {
        $('#qr-result')
        .removeClass('d-none alert-danger alert-success alert-primary')
        .addClass('alert-danger')
        .html(resp.msg)
        .show();
    });
}

function markByMob() {
    const mobile = $('#mobile').val();
    if (!mobile || !/^[6-9]\d{9}$/.test(mobile)) {
        myAlert("Please enter a valid mobile number.", "danger");
        return;
    }
    webserv("POST", "{{ route('att.mark_by_mob') }}", 
    { mobile: $('#mobile').val() }, 
    function ok(resp) {
        $('#qr-result')
            .removeClass('d-none alert-danger alert-success alert-primary')
            .addClass('alert-success')
            .html(resp.msg)
            .show();
    }, function fail(resp) {
        $('#qr-result')
            .removeClass('d-none alert-danger alert-success alert-primary')
            .addClass('alert-danger')
            .html(resp.msg)
            .show();
    });
}

</script>
@endpush
