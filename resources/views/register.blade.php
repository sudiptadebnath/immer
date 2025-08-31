@extends('layouts.app')

@section('content')
<div class="container d-flex align-items-center justify-content-center login-container h-100">
<div class="col-md-10 col-lg-8 p-2">
<x-card icon="person-plus" title="Puja Committee Registration">

    <form id="register" onsubmit="return register_submt()" novalidate="novalidate">

    <div class="row g-2">

        {{-- Puja in New Town --}}
        <div class="col-md-12">
            <input type="hidden" name="id" id="id" />
            <div class="d-flex flex-wrap align-items-center">
                <label class="me-3 form-label">Puja in New Town Area ?</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio"  required="true"
                    name="in_newtown" value="1" title="Puja in New Town Area"> Yes
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" required="true"
                    name="in_newtown" value="0" title="Puja in New Town Area"> No
                </div>
            </div>
            <label id="in_newtown-error" class="error text-danger" for="in_newtown"></label>
        </div>

        {{-- If Yes --}}
        <div id="ifYes" class="d-none col-md-12 row g-2 m-0 p-0">
            <x-select icon="geo-alt" size="6" name="action_area" title="Action Area"
             :value="stoa(setting('ACTION_AREA','I~~II~~III'))" required="true" />
            <x-select icon="tags" size="6" name="category" title="Category" 
             :value="stoa(setting('CATEGORY','Housing~~Block'))" required="true" />
            <x-select icon="people" name="puja_committee_name" title="Puja Committee" 
             :value="stoa(setting('PUJA_COMMITTEE','Other'))" required="true" />
            <div class="mb-2 d-none" id="otherCommitteeBox">
                <input type="text" class="form-control" name="puja_committee_name_other"
                 placeholder="Enter Committee Name" required="true" >
            </div>
        </div>

        {{-- If No --}}
        <div id="ifNo" class="d-none col-md-12 d-flex flex-column gap-2">
            <x-text name="puja_committee_name_text" icon="person" title="Puja Committee Name" />
            <x-textarea name="puja_committee_address" icon="house" title="Puja Committee Address" />
        </div>

        {{-- Common fields --}}
        <x-text size="8" name="secretary_name" icon="person" title="Secretary Name" required="true" />
        <x-number size="4" name="secretary_mobile" icon="telephone" title="Secretary Mobile" required="true" />
        <x-text size="8" name="chairman_name" icon="person-circle" title="Chairman/President Name" required="true" />
        <x-number size="4" name="chairman_mobile" icon="telephone" title="Chairman/President Mobile" required="true" />
        <x-password size="6" name="password" title="Password" required="true" />
        <x-password size="6" name="password2" title="Repeat Password" required="true" />

        {{-- Immersion --}}
        <x-select size="4" icon="calendar-date" name="proposed_immersion_date" title="Proposed Immersion Date"
         :value="stoa(setting('IMMERSION_DATE','2025-10-13'))" required="true" />
        <x-text size="4" typ="time" name="proposed_immersion_time" title="Immersion Time"  icon="!" required="true" />
        <x-text size="4" name="vehicle_no" title="Vehicle No (optional)"  icon="truck-front" />

        {{-- Dhunuchi Nach --}}
        <div class="col-md-12">
            <div class="d-flex flex-wrap align-items-center">
                <label class="me-3 form-label">Participating in Dhunuchi Nach Competition ?</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" title="Participating in Dhunuchi Nach"
                    name="dhunuchi" value="1" required="true"> Yes
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" title="Participating in Dhunuchi Nach"
                    name="dhunuchi" value="0" required="true"> No
                </div>
            </div>
            <label id="dhunuchi-error" class="error text-danger" for="dhunuchi"></label>
        </div>

        <div id="ifDhunuchiYes" class="d-none col-md-12">
            <x-number name="team_members" title="No of Team Members" icon="person-lines-fill" digcount="2" required="true" />
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

    // Validation messages override
    $.validator.messages.required = function(param, input) {
        let label = $("label[for='" + input.id + "']").text().replace("?", "").trim();
        if (!label) {
            label = input.name.replace(/_/g, " "); // fallback: use input name
        }
        return label + " is required";
    };

    // Custom rule for Indian mobile
    $.validator.addMethod("indianMobile", function(value, element) {
        return this.optional(element) || /^[6-9]\d{9}$/.test(value);
    }, "Enter a valid mobile number");

    $("#register").validate({
        rules:{
            password: {
                required: true,
                minlength: 6
            },
            password2: {
                required: true,
                equalTo: "#password"
            },
            team_members: {
                required: function() { return $("input[name='dhunuchi']:checked").val() == "1"; },
                digits: true,
                min: 1,
                max: {{ setting('DHUNUCHI_TEAM',20) }}
            }
        },
        messages: {
            password2: { equalTo: "Passwords do not match" },
            team_members: "Please enter number of team members (1-{{ setting('DHUNUCHI_TEAM',20) }})"
        },
    });
});

function register_submt() {
    if($("#register").valid()) {
        webserv("POST","{{ url('/register') }}","register",
        function ok(d){ goLnk("{{ url('/user/dashboard') }}"); });
    }
    return false;
}
</script>
@endsection
