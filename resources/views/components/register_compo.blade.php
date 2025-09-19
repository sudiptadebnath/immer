@props([
    'id' => 'register',
    'cb'=>"register_submt",
    'btns'=> false,
    'hiderole'=> false,
    'hidestat'=> false,
])

@if($btns)
<form id="{{ $id }}" onsubmit="return __{{ $id }}_submt()" novalidate="novalidate">
@endif
    <div class="row g-2">

        {{-- Puja in New Town --}}
        <div class="col-md-12">
            <input type="hidden" name="id" id="id" />
            <div class="d-flex flex-wrap align-items-center">
                <label class="me-3 form-label">Puja in New Town Area ?</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" 
                    name="in_newtown" value="1" title="Puja in New Town Area"> Yes
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio"
                    name="in_newtown" value="0" title="Puja in New Town Area"> No
                </div>
            </div>
            <label id="in_newtown-error" class="error text-danger" for="in_newtown"></label>
        </div>

        {{-- If Yes --}}
        <div id="ifYes" class="d-none col-md-12 row g-2 m-0 p-0">
            <x-select icon="geo-alt" size="6" name="action_area" title="Action Area"
             :value="stoa(setting('ACTION_AREA','I~~II~~III'))" />
            <x-select icon="tags" size="6" name="category" title="Category" 
             :value="stoa(setting('CATEGORY','Housing~~Block'))" />
            <x-select icon="people" name="puja_committee_name" title="Puja Committee" 
             :value="stoa(setting('PUJA_COMMITTEE','Other'))" />
            <div class="mb-2 d-none" id="otherCommitteeBox">
                <input type="text" class="form-control" name="puja_committee_name_other"
                 placeholder="Enter Committee Name">
            </div>
        </div>

        {{-- If No --}}
        <div id="ifNo" class="d-none col-md-12">
            <x-text name="puja_committee_name_text" icon="person" title="Puja Committee Name" />
            <x-textarea name="puja_committee_address" title="Puja Committee Address" />
        </div>

        {{-- Common fields --}}
        <x-text size="8" name="secretary_name" icon="person" title="Secretary Name" />
        <x-number size="4" name="secretary_mobile" icon="telephone" title="Secretary Mobile" />
        <x-text size="8" name="chairman_name" icon="person-circle" title="Chairman/President Name" />
        <x-number size="4" name="chairman_mobile" icon="telephone" title="Chairman/President Mobile" />
        <x-password size="6" name="password" title="Password" />
        <x-password size="6" name="password2" title="Repeat Password" />

        @if(!$hiderole && hasRole("a"))
        <x-select icon="people" name="role" title="Role" :value="roleDict()" />
        @endif
        @if(!$hidestat && hasRole("ao"))
        <x-select icon="check" name="stat" title="Status" :value="statDict()" />
        @endif

        {{-- Immersion --}}
        <x-select size="4" icon="calendar-date" name="proposed_immersion_date" title="Proposed Immersion Date"
         :value="stoa(setting('IMMERSION_DATE','2025-10-13'))" />
        <x-text size="4" typ="time" name="proposed_immersion_time" title="Immersion Time"  icon="!" />
        <x-text size="4" name="vehicle_no" title="Vehicle No (optional)"  icon="truck-front" />

        {{-- Dhunuchi Nach --}}
        <div class="col-md-12">
            <div class="d-flex flex-wrap align-items-center">
                <label class="me-3 form-label">Participating in Dhunuchi Nach Competition ?</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" title="Participating in Dhunuchi Nach"
                    name="dhunuchi" value="1"> Yes
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" title="Participating in Dhunuchi Nach"
                    name="dhunuchi" value="0"> No
                </div>
            </div>
            <label id="dhunuchi-error" class="error text-danger" for="dhunuchi"></label>
        </div>

        <div id="ifDhunuchiYes" class="d-none col-md-12">
            <x-number name="team_members" title="No of Team Members" icon="person-lines-fill" digcount="2" />
        </div>

        @if($btns)
        <x-button type="submit" title="Submit Registration" icon="send" />

        <div class="col-md-12 text-center">
            Already Registered ? <a href="{{ route('login') }}">Sign in</a>
        </div>
        @endif

    </div>
@if($btns)
</form>
@endif

@push('scripts')
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

    // Initialize validation
    function initValidation() {
        if ($("#{{ $id }}").data('validator')) {
            $("#{{ $id }}").data('validator').destroy();
        }

        let rules = {
            in_newtown: { required: isUserRole },
            secretary_name: { required: isUserRole },
            secretary_mobile: { required: true, indianMobile: true },
            chairman_name: { required: isUserRole },
            chairman_mobile: { required: isUserRole, indianMobile: true },
            proposed_immersion_date: { required: isUserRole },
            proposed_immersion_time: { required: isUserRole },
            dhunuchi: { required: isUserRole },
            password: {
                required: isNewRecord,
                minlength: 6
            },
            password2: {
                required: isNewRecord,
                equalTo: "#password"
            },
            action_area: {
                required: function() { return $("input[name='in_newtown']:checked").val() == "1"; }
            },
            category: {
                required: function() { return $("input[name='in_newtown']:checked").val() == "1"; }
            },
            puja_committee_name: {
                required: function() { return $("input[name='in_newtown']:checked").val() == "1"; }
            },
            puja_committee_name_other: {
                required: function() { return $("#otherCommitteeBox").is(":visible"); }
            },
            puja_committee_name_text: {
                required: function() { return $("input[name='in_newtown']:checked").val() == "0"; }
            },
            puja_committee_address: {
                required: function() { return $("input[name='in_newtown']:checked").val() == "0"; }
            },
            team_members: {
                required: function() { return $("input[name='dhunuchi']:checked").val() == "1"; },
                digits: true,
                min: 1,
                max: {{ setting('DHUNUCHI_TEAM',20) }}
            }
        };

        let messages = {
            password2: { equalTo: "Passwords do not match" },
            team_members: "Please enter number of team members (1-{{ setting('DHUNUCHI_TEAM',20) }})"
        };

        $("#{{ $id }}").validate({
            rules,
            messages,
        });
    }

    initValidation();

    // Re-init validation if role changes
    $(document).on("change", "[name='role']", function(){
        initValidation();
    });
});

function isUserRole() {
    return ($("[name='role']").val() || "u") === "u";
}

function isNewRecord() {
    return $("#id").val() === ""; // id is empty => new record
}

// Form submit
function __{{ $id }}_submt() {
    if($("#{{ $id }}").valid()) {
        {{ $cb }}();
    }
    return false;
}
</script>
@endpush
