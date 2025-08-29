@extends('layouts.app')

@section('content')
<div class="container d-flex align-items-center justify-content-center login-container h-100">
<div class="col-md-8 col-lg-6">
<x-card icon="person-plus" title="Puja Committee Registration">
    <form id="register" onsubmit="return register_submt()" novalidate="novalidate">
    <div class="row g-2">

        {{-- Puja in New Town --}}
        <div class="col-md-12">
            <div class="d-flex flex-wrap align-items-center">
                <label class="me-3 form-label">Puja in New Town Area ?</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" 
                    name="in_newtown" value="1" title="Puja in New Town Area" required> Yes
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio"
                    name="in_newtown" value="0" title="Puja in New Town Area" required> No
                </div>
            </div>
            <label id="in_newtown-error" class="error text-danger" for="in_newtown"></label>
        </div>

        {{-- If Yes --}}
        <div id="ifYes" class="d-none col-md-12 row g-2 m-0 p-0">
            <x-select size="6" name="action_area" title="Action Area"
             :value="['I'=>'I','II'=>'II','III'=>'III']" required=true />
            <x-select size="6" name="category" title="Category" 
             :value="['Housing'=>'Housing','Block'=>'Block']" required=true />
            <x-select name="puja_committee_name" title="Puja Committee" 
             :value="['Other'=>'Other']" required=true />
            <div class="mb-2 d-none" id="otherCommitteeBox">
                <input type="text" class="form-control" name="puja_committee_name_other"
                 placeholder="Enter Committee Name">
            </div>
        </div>

        {{-- If No --}}
        <div id="ifNo" class="d-none col-md-12">
            <x-text name="puja_committee_name_text" icon="people" title="Puja Committee Name" required=true />
            <x-textarea name="puja_committee_address" title="Puja Committee Address" required=true />
        </div>

        {{-- Common fields --}}
        <x-text size="8" name="secretary_name" title="Secretary Name" required=true />
        <x-number size="4" name="secretary_mobile" title="Secretary Mobile" required=true />
        <x-text size="8" name="chairman_name" title="Chairman/President Name" required=true />
        <x-number size="4" name="chairman_mobile" title="Chairman/President Mobile" required=true />
        <x-password size="6" name="password" title="Password" required=true />
        <x-password size="6" name="password2" title="Repeat Password" required=true />

        {{-- Immersion --}}
        <x-select size="4" name="proposed_immersion_date" title="Proposed Immersion Date"
         :value="['2025-10-12'=>'12 Oct','2025-10-13'=>'13 Oct']" required=true />
        <x-text size="4" name="proposed_immersion_time" title="Immersion Time" required=true />
        <x-text size="4" name="vehicle_no" title="Vehicle No (optional)" />

        {{-- Dhunuchi Nach --}}
        <div class="col-md-12">
            <div class="d-flex flex-wrap align-items-center">
                <label class="me-3 form-label">Participating in Dhunuchi Nach Competition ?</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" title="Participating in Dhunuchi Nach"
                    name="dhunuchi" value="1" required> Yes
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" title="Participating in Dhunuchi Nach"
                    name="dhunuchi" value="0" required> No
                </div>
            </div>
            <label id="dhunuchi-error" class="error text-danger" for="dhunuchi"></label>
        </div>

        <div id="ifDhunuchiYes" class="d-none col-md-12">
            <x-select name="team_members" title="No of Team Members" :value="range(1,20)" />
        </div>

        <x-button type="submit" title="Submit Registration" icon="send" />

        <div class="col-md-12 text-center">
            Already Registered ? <a href="{{ url('/') }}">Sign in</a>
        </div>

    </div>
    </form>
</x-card>
</div>
</div>
@endsection

@section('scripts')
<script>
$(function () {
    // Toggle Yes/No section
    $("input[name='in_newtown']").change(function(){
        if($(this).val() === "1") {
            $("#ifYes").removeClass("d-none");
            $("#ifNo").addClass("d-none");
        } else {
            $("#ifNo").removeClass("d-none");
            $("#ifYes").addClass("d-none");
        }
    });

    // Committee "Other" option
    $("#puja_committee_name").change(function(){
        if($(this).val() === "Other") {
            $("#otherCommitteeBox").removeClass("d-none");
        } else {
            $("#otherCommitteeBox").addClass("d-none");
        }
    });

    // Dhunuchi Nach
    $("input[name='dhunuchi']").change(function(){
        if($(this).val() === "1") {
            $("#ifDhunuchiYes").removeClass("d-none");
        } else {
            $("#ifDhunuchiYes").addClass("d-none");
        }
    });

    // Validation
    $.validator.messages.required = function(param, input) {
        let label = $("label[for='" + input.id + "']").text().replace("?", "").trim();
        if (!label) {
            label = input.name.replace(/_/g, " "); // fallback: use input name
        }
        return label + " is required";
    };
    $("#register").validate({
        rules: {
            secretary_mobile: {
                required: true,
                indianMobile: true
            },
            chairman_mobile: {
                required: true,
                indianMobile: true
            },
            password: {
                required: true,
                minlength: 6
            },
            password2: {
                required: true,
                equalTo: "#password"
            }
        },
        messages: {
            secretary_mobile: { required: "Secretary mobile is required" },
            chairman_mobile: { required: "Chairman mobile is required" },
            password: {
                required: "Password is required",
                minlength: "Password must be at least 6 characters"
            },
            password2: {
                required: "Please confirm your password",
                equalTo: "Passwords do not match"
            }
        }
    });
});

// custom rule for Indian mobile
$.validator.addMethod("indianMobile", function(value, element) {
    return this.optional(element) || /^[6-9]\d{9}$/.test(value);
}, "Enter a valid mobile number");

function register_submt() {
    if($("#register").valid()) {
        webserv("POST","{{ url('/register') }}","register",
        function ok(d){ goLnk("{{ url('/user/dashboard') }}"); });
    }
    return false;
}
</script>
@endsection
