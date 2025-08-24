@extends('layouts.app')

@section('content')

<div class="container mt-3">
<div class="row g-2">
    <x-select size="4" icon="door-closed" name="post" title="Gate" :value="postDict()" sel="1" />
    <x-select size="4" icon="box-arrow-in-right" name="typ" title="Type" :value="attDict()" sel="in" />

    <div class="col-md-4">
        <button id="toggle-scan" class="btn btn-primary mb-3" onclick="toggleScan()">
            <i class="bi bi-qr-code-scan me-2"></i><span>Start</span>
        </button>
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

function validate() {
    const post = $('#post').val();
    const typ  = $('#typ').val();
    if (!post || !typ) {
        myAlert("Please select Post and Type before scanning.", "danger");
        return false;
    }
    return true;
}

function toggleScan() {
    const btn = $('#toggle-scan span'); // span holds Start/Stop text

    if (!isScanning) {
        if (!validate()) return;

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
    const post = $('#post').val();
    const typ  = $('#typ').val();
    webserv("POST", "{{ route('user.attendance') }}", { 
        token: decodedText, post, typ 
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

</script>
@endpush
