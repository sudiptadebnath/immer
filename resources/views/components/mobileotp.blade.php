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
            class="verify_otp_btn btn badge btn-outline-primary"
            id="{{ $name }}_sendotp" title="Verify Your Number">Verify Number
        </button>
        {{ $slot }}
    </div>
    <label class="error mobotp" for="{{ $name }}"></label>
    <label class="error mobotp" for="{{ $name }}_otp"></label>
@if($size)
</div>
@endif


<div id="{{ $name }}_sendotp_modal" class="otpverification_modal">
    <div class="otpverification_body">
        <h2>Verify Your OTP</h2>
        <p id="{{ $name }}_sendotp_msg">Enter the 6-digit OTP sent to your Phone No - ******8858</p>
        <form>
            <div id="{{ $name }}_sendotp_input" class="otp_inputs">
                <input type="text" maxlength="1">
                <input type="text" maxlength="1">
                <input type="text" maxlength="1">
                <input type="text" maxlength="1">
                <input type="text" maxlength="1">
                <input type="text" maxlength="1">
            </div>
            <button id="{{ $name }}_sendotp_verify" type="button">Verify OTP</button>
        </form>
        <p class="resend mb-0">
            Didn’t receive the code? <a href="javascript:void(0)" onclick="{{ $name }}_resend()">Resend OTP</a>
        </p>
    </div>
</div>





@push('scripts')
<script>

// Attach handler to ALL modals
document.querySelectorAll(".otpverification_modal").forEach(function(modal) {
    modal.addEventListener("click", function(e) {
        if (e.target === modal) {
            modal.classList.remove("open");
        }
    });

    // Prevent clicks inside modal body from bubbling
    modal.querySelector(".otpverification_body").addEventListener("click", function(e) {
        e.stopPropagation();
    });
});

function {{ $name }}_resend(){
	let mobileInput = $("#{{ $name }}");
	let mobileVal = mobileInput.val();
	webserv("POST", "{{ url('/send_otp') }}", 
		{ nm: "{{ $name }}" ,mobile: mobileVal },
		function ok(d) {
			$("#{{ $name }}_sendotp_msg").html(d["msg"]);
		},
		function err(d) {
			myAlert(d["msg"], "danger");
		}
	);	
}


document.getElementById("{{ $name }}_sendotp").addEventListener("click", function() {
	setTimeout(function () {
		let errorLabel  = $("label.error[for='{{ $name }}']");
		if (errorLabel.text().trim().length > 0) return;

		let mobileInput = $("#{{ $name }}");
		let mobileVal = mobileInput.val();
		if (!/^[6-9]\d{9}$/.test(mobileVal)) {
			myAlert("Enter valid 10 digit mobile number","danger");
			return;
		}

		webserv("POST", "{{ url('/send_otp') }}", 
			{ nm: "{{ $name }}" ,mobile: mobileVal },
			function ok(d) {
				//myAlert(d["msg"], "success");
				$("#{{ $name }}_sendotp_msg").html(d["msg"]);
				document.getElementById("{{ $name }}_sendotp_modal").classList.add("open");
			},
			function err(d) {
				myAlert(d["msg"], "danger");
			}
		);
	}, 200); 
});

// Close modal when clicking on modal background
document.getElementById("{{ $name }}_sendotp_verify").addEventListener("click", function() {
	let mobileInput = $("#{{ $name }}");
	let mobileVal = mobileInput.val();
	
    let otp = "";
    $("#{{ $name }}_sendotp_input input").each(function () {
        otp += $(this).val();
    });
	
    if (otp.length != 6) {
		myAlert("Enter OTP","danger");
		return;
	}
	
	webserv("POST", "{{ url('/verify_otp') }}", 
		{ nm: "{{ $name }}" ,mobile: mobileVal, otp  },
		function ok(d) {
			myAlert(d["msg"], "success");
			document.getElementById("{{ $name }}_sendotp_modal").classList.remove("open");
			$(".verify_otp_btn").prop("disabled",true);
			$("#{{ $name }}").prop("readonly",true);
			$("#{{ $name }}_sendotp").replaceWith(
            '<span class="btn badge btn-outline-primary d-flex align-items-center justify-content-center gap-1"><i class="bi bi-check-circle-fill"></i> Verified</span>'
            );
            //Remove other verify buttons
            $(".verify_otp_btn").not("#{{ $name }}_sendotp").remove();
		},
		function err(d) {
			myAlert(d["msg"], "danger");
		}
	);
});

const {{ $name }}_inputs = $("#{{ $name }}_sendotp_input input");

{{ $name }}_inputs.each(function(index) {
    $(this).on("input", function() {
        if ($(this).val().length === 1 && index < {{ $name }}_inputs.length - 1) {
            {{ $name }}_inputs.eq(index + 1).focus();
        }
    });

    $(this).on("keydown", function(e) {
        if (e.key === "Backspace" && $(this).val() === "" && index > 0) {
            {{ $name }}_inputs.eq(index - 1).focus();
        }
    });
});
/*
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
*/
</script>
@endpush

