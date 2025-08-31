@extends('layouts.app')

@section('content')
<div class="container row gap-2 m-0 p-2">
    <form id="profile" onsubmit="return profile_submt()" novalidate="novalidate">

    <div class="row g-2">
        <input type="hidden" name="id" id="id" value="{{ getUsrProp('id') }}" />

    @if(hasRole("u"))    
        {{-- Puja in New Town --}}
        <div class="col-md-12">
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
        <x-password size="6" name="password" title="Password" />
        <x-password size="6" name="password2" title="Repeat Password" />

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

    @else
        <x-text size="8" name="secretary_name" icon="person" title="Name" required="true" />
        <x-number size="4" name="secretary_mobile" icon="telephone" title="Mobile" required="true" />
        <x-password size="6" name="password" title="Password" />
        <x-password size="6" name="password2" title="Repeat Password" />
    @endif

        <x-button type="submit" title="Save" icon="send" />
    </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(function () {
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

    @if(hasRole("u"))    
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
    $("#register").validate({
        rules:{
            password: {
                required: false,
                minlength: 6
            },
            password2: {
                required: false,
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

    @else

    $("#register").validate({
        rules:{
            password: {
                required: false,
                minlength: 6
            },
            password2: {
                required: false,
                equalTo: "#password"
            },
        },
        messages: {
            password2: { equalTo: "Passwords do not match" },
        },
    });
    

    @endif    
    loadVals();
});

function profile_submt() {
    if($("#profile").valid()) {
        const id = $('#id').val();
        const url =  `{{ url('user/users'.(hasRole("u") ? '' : '/min')) }}/${id}`;
        webserv("PUT", url, "profile", function ok(d) {
            myAlert(d["msg"],"success","Ok",function() {
                loadVals();
            });
        });
    }
    return false;
}

function loadVals() {
    const id = $('#id').val();
    webserv("GET", "{{ url('user/users') }}/" + id, {}, function (d) {
        let user = d["data"];

        $('#id').val(user.id);
        $('#action_area').val(user.action_area);
        $('#category').val(user.category);
        $('#puja_committee_name').val(user.puja_committee_name);
        $('#puja_committee_address').val(user.puja_committee_address);
        $('#secretary_name').val(user.secretary_name);
        $('#secretary_mobile').val(user.secretary_mobile);
        $('#chairman_name').val(user.chairman_name);
        $('#chairman_mobile').val(user.chairman_mobile);
        $('#proposed_immersion_date').val(user.proposed_immersion_date);
        $('#proposed_immersion_time').val(user.proposed_immersion_time);
        $('#vehicle_no').val(user.vehicle_no);
        $('#team_members').val(user.team_members);

        // reset password fields
        $('#password').val('');
        $('#password2').val('');

        // set modal header and open modal
        $('#userModalLabel').text('Edit User');
        $('#userModal').modal('show');

        if(user.action_area) {
            $("input[name='in_newtown'][value='1']")
                .prop("checked", true)     // mark as checked
                .trigger("change");        // fire change event
        } else {
            $("input[name='in_newtown'][value='0']")
                .prop("checked", true)     // mark as checked
                .trigger("change");        // fire change event
        }

        if(user.team_members) { 
            $("input[name='dhunuchi'][value='1']")
                .prop("checked", true)     // mark as checked
                .trigger("change");        // fire change event
        } else {
            $("input[name='dhunuchi'][value='0']")
                .prop("checked", true)     // mark as checked
                .trigger("change");        // fire change event
        }
    });

}
</script>
@endpush
