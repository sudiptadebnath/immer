<x-gatepass :file="asset('qrs/'.$puja->token.'.png')" 
	:puja="$puja" :pdf="false">
<div class="download-container">
    <a class="btn btn-danger" href="{{ route('puja.gpass.pdf', $puja->token) }}">
		📄 Download PDF
    </a>
	<button type="button" class="btn btn-primary send-sms-btn" onclick="sendSMS('{{ $puja->token }}')">
		💬 Send SMS
	</button>
	
	<div id="sms-result" class="alert success" style="display:none;"></div>
	
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>

function sendSMS(token) {
    $.ajax({
        url: "{{ route('puja.gpass.sms', $puja->token) }}",
        type: "POST",
        data: { _token: "{{ csrf_token() }}" },
        success: function (resp) {
            showAlert(resp.success ? "success" : "danger", resp.msg);
        },
        error: function (xhr) {
            let msg = "❌ Failed to send SMS.";
            showAlert("danger", msg);
        }
    });
}

function showAlert(type, message, timeout = 5000) {
    let box = $("#sms-result");
    box.stop(true, true)
       .removeClass()
       .addClass("alert " + type)
       .text(message)
       .fadeIn(200);
    if (timeout > 0) {
        setTimeout(function() {
            box.fadeOut(400);
        }, timeout);
    }
}

</script>	
</div>
</x-gatepass>
