@props([
    'size' => '12',
    'name' => 'txt',
    'title' => 'Enter content',
    'icon' => 'info-circle',
    'value' => '',
    'required' => false,
])

@php
    $required = filter_var($required, FILTER_VALIDATE_BOOLEAN);
@endphp


@push("styles")
<style>
.otp-input {
    padding: 1px;
    font-size: 16px;
    font-weight: 500;
    letter-spacing: 4px;
	text-align:center;
	max-width: 100px;
	color: blue;
}

</style>
@endpush

@if($size)
<div class="col-md-{{ $size }}">
@endif
    <div class="input-group">
        <span class="input-group-text">
            @if(!empty($icon))
                <i class="bi bi-{{ $icon }}"></i>
            @else
                {{ ucfirst($title) }}
            @endif
        </span>
        <input 
            type="text"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            class="form-control"
            placeholder="{{ ucfirst($title) }}"
            title="{{ ucfirst($title) }}"
            inputmode="numeric" maxlength="10" pattern="^\d{1,10}$"
            oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)"
            @if($required) required @endif
        >
        <button type="button" 
            class="btn btn-outline-primary"
            id="{{ $name }}_sendotp"
			title="Get OTP">
			<i class="bi bi-shield-lock"></i>
        </button>
        <input 
            type="text"
            id="{{ $name }}_otp"
            name="{{ $name }}_otp"
            class="form-control otp-input"
            placeholder="OTP"
            maxlength="6"
            inputmode="numeric"
            oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,6)"
            @if($required) required @endif
        >
        {{ $slot }}
    </div>
    <label class="error mobotp" for="{{ $name }}"></label>
    <label class="error mobotp" for="{{ $name }}_otp"></label>
@if($size)
</div>
@endif


@push('scripts')
<script>
$(function() {
    let otpClickCount_{{ $name }} = 0;
    let otpTimer_{{ $name }} = null;
	
    $("#{{ $name }}_sendotp").on("click", function() {
		let errorLabel  = $("label.error[for='{{ $name }}']");
		if (errorLabel.text().trim().length > 0) return;
		
        let mobileInput = $("#{{ $name }}");
        let sendBtn = $(this);
        let otpInput = $("#{{ $name }}_otp");

        let mobileVal = mobileInput.val();

        // validate mobile
        if (!/^[6-9]\d{9}$/.test(mobileVal)) {
            myAlert("Enter valid 10 digit mobile number","danger");
            return;
        }

        otpClickCount_{{ $name }}++;
        if (otpClickCount_{{ $name }} > 3) {
            sendBtn.prop("disabled", true).html("<i class='bi bi-shield-lock'></i>");
            return;
        }

        // disable mobile field after first attempt
        if (otpClickCount_{{ $name }} === 1) {
            mobileInput.prop("readonly", true);
        }
		
		sendBtn.prop("disabled", true).html("<i class='bi bi-hourglass-split'></i>");
		webserv("POST", "{{ url('/send_otp') }}", 
			{ nm: "{{ $name }}" ,mobile: mobileVal },
			function ok(d) {
				myAlert(d["msg"], "success");
				if (otpClickCount_{{ $name }} < 3) {
					setTimeout(() => sendBtn.prop("disabled", false).html("<i class='bi bi-shield-lock'></i>"), 5000);
				}
				otpInput.focus();
			},
			function err(d) {
				myAlert(d["msg"], "danger");
				if (otpClickCount_{{ $name }} < 3) {
					setTimeout(() => sendBtn.prop("disabled", false).html("<i class='bi bi-shield-lock'></i>"), 5000);
				}
				otpInput.focus();
			}
		);
    });
});
</script>
@endpush

