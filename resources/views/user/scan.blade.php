@extends('layouts.app')

@section('content')

<div class="d-flex flex-column justify-content-center align-items-center">
    <div class="row mb-2">
        <x-select size="6" icon="people" name="post" title="Post" :value="postDict()" />
        <x-select size="6" icon="check" name="typ" title="Type" :value="attDict()" />
    </div>

    <button id="start-scan" class="btn btn-primary mb-3" onclick="startScan()">
        <i class="bi bi-qr-code-scan me-2"></i>Start Scan
    </button>
    <button id="restart-scan" class="btn btn-success mb-3" style="display:none;" onclick="restartScan()">
        <i class="bi bi-arrow-repeat me-2"></i>Scan Next
    </button>

    <div id="qr-reader" style="width:100%; max-width:500px; display:none;"></div>
    <div id="qr-result" class="mt-2"></div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
let html5QrcodeScanner;

function validate() {
    const post = $('#post').val();
    const typ  = $('#typ').val();

    if (!post || !typ) {
        myAlert("Please select Post and Type before scanning.", "danger");
        return false;
    }
    return true;
}

function startScan() {
    if(!validate()) return;
    document.getElementById('qr-reader').style.display = 'block';
    document.getElementById('start-scan').style.display = 'none';
    document.getElementById('restart-scan').style.display = 'none';
    $('#qr-result').html('');

    if (!html5QrcodeScanner) {
        html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: 250 });
    }

    html5QrcodeScanner.render(onScanSuccess);
}

function restartScan() {
    if(!validate()) return;
    document.getElementById('qr-reader').style.display = 'block';
    document.getElementById('restart-scan').style.display = 'none';
    $('#qr-result').html('');

    if (html5QrcodeScanner) {
        html5QrcodeScanner.render(onScanSuccess);
    }
}

function onScanSuccess(decodedText, decodedResult) {
    $('#qr-result').html(decodedText);

    // Stop scanner immediately
    html5QrcodeScanner.clear().then(() => {
        console.log("Scanner stopped after success.");
        document.getElementById('qr-reader').style.display = 'none';
        document.getElementById('restart-scan').style.display = 'inline-block';
    }).catch(err => console.error(err));

    // Send scanned token to server using your existing webserv function
    const post = $('#post').val();
    const typ  = $('#typ').val();

    webserv("POST", "{{ route('user.attendance') }}", { 
        token: decodedText, post, typ 
    }, function ok(resp) {
        myAlert(resp.msg,"success");
    }, function fail(resp) {
        myAlert(resp.msg,"danger");
    });
}

</script>
@endpush
