@extends('layouts.blank')

@php
$action_area = dbVals("action_areas","name","view_order","asc");
$category = dbVals("puja_categories","name","view_order","asc");
$immer_dts = dbVals("puja_immersion_dates",["idate","name"],"idate","asc");
@endphp

@section('content')
<div class="PCRegist_sec">
    <div class="container d-flex align-items-center justify-content-center login-container h-100">
        <div class="PCRegistpage_wrap position-relative">
            <div class="watermark_img">
                <img src="{{asset('resources/img/happy-durga.png')}}" class="img-fluid" alt="image" loading="eager" />
            </div>
            <div class="PCRegist_form">

                <div class="PCRegist_header">
                    <div class="left">
                        <div class="logo">
                            <img src="{{asset('resources/img/logo-nkda.png')}}" alt="Logo" loading="eager">
                        </div>
                        <div class="logo_desc">
                            <h2>NEW TOWN KOLKATA DEVELOPMENT AUTHORITY</h2>
                            <p>Administrative Building. Plot No - DG/13, Premises No - 04-3333, Action Area - ID, New Town, Kolkata - 700156</p>
                        </div>
                    </div>
                    <div class="right">
                        <img src="{{asset('resources/img/durga-img.jpg')}}" class="img-fluid" alt="image" loading="lazy" />
                    </div>

                </div>

                <x-card icon="person-plus" title="Puja Committee Registration for Immersion">

                    <form id="register" onsubmit="return register_submt(event)" novalidate="novalidate">

                        <div class="row g-2">

                            {{-- Puja in New Town --}}
                            <div class="col-md-12">
                                <input type="hidden" name="id" id="id" />
                                <div class="d-flex flex-wrap align-items-center">
                                    <label class="me-3 form-label">Puja in New Town Area ?</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" required="true"
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
                                    :value="$action_area" required="true" />
                                <x-select icon="tags" size="6" name="category" title="Category"
                                    :value="$category" required="true" />
                                <x-select icon="people" name="puja_committee_name" title="Puja Committee" required="true" />
                                <div class="mb-2 d-none" id="otherCommitteeBox">
                                    <input type="text" class="form-control" name="puja_committee_name_other"
                                        placeholder="Enter Committee Name" required="true">
                                </div>
                            </div>

                            {{-- If No --}}
                            <div id="ifNo" class="d-none col-md-12 d-flex flex-column gap-2">
                                <x-text name="puja_committee_name_text" icon="person" title="Puja Committee Name" />
                            </div>

                            {{-- Common fields --}}
                            <x-textarea name="puja_committee_address" icon="house" title="Puja Committee Address" />
                            <x-text size="6" name="secretary_name" icon="person" title="Secretary Name" required="true" />
                            <x-mobileotp size="6" name="secretary_mobile" icon="telephone" title="Secretary Mobile" required="true" />
                            <x-text size="6" name="chairman_name" icon="person-circle" title="Chairman/President Name" required="true" />
                            <x-mobileotp size="6" name="chairman_mobile" icon="telephone" title="Chairman/President Mobile" required="true" />

                            {{-- Immersion --}}
                            <x-select size="6" icon="calendar-date" name="proposed_immersion_date" title="Proposed Immersion Date" :value="$immer_dts" required="true" />
                            <x-flattime size="6" id="proposed_immersion_time" name="proposed_immersion_time" title="Immersion Time" icon="clock" required="true">
                                Range 16:00 - 23:59
                            </x-flattime>
                            <x-select size="4" icon="people" name="no_of_vehicles" title="No of Vehicles" :value="['1'=>'1','2'=>'2','3'=>'3']" />
                            <x-text size="8" name="vehicle_no" title="Vehicle No(s) (optional)" icon="truck-front">
                                Vehicle No(s) separated by comma
                            </x-text>

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

                        </div>
                    </form>
                </x-card>
            </div>
            <div class="PCRegist_footer">
                <p class="text">Copyright © 2025 New Town Kolkata Development Authority</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function() {

        $("#action_area, #category").change(function() {
            let actionArea = $("#action_area").val();
            let category = $("#category").val();
            webserv("GET", `{{ route('conf.get.committees') }}`, {
                action_area: actionArea,
                category: category
            }, function(resp) {
                let list = resp.data || [];
                let $ddl = $("#puja_committee_name");
                $ddl.empty().append('<option value="">Select Puja Committee</option>');
                list.forEach(function(item) {
                    $ddl.append(`<option value="${item.name}" data-address="${item.puja_address ?? ''}">${item.name}</option>`);
                });
                $ddl.append('<option value="Other">Other</option>');
            });
        });

        // on select committee → place address
        $("#puja_committee_name").change(function() {
            let $sel = $(this).find("option:selected");
            let addr = $sel.data("address") || "";
            if ($(this).val() !== "Other") {
                $("#puja_committee_address").val(addr);
                $("#otherCommitteeBox").addClass("d-none");
            } else {
                $("#otherCommitteeBox").removeClass("d-none");
                $("#puja_committee_address").val("");
            }
        });

        // Toggle Yes/No section
        $("input[name='in_newtown']").change(function() {
            if ($(this).val() === "1") {
                $("#ifYes").removeClass("d-none");
                $("#ifNo").addClass("d-none");
            } else {
                $("#ifNo").removeClass("d-none");
                $("#ifYes").addClass("d-none");
            }
        });

        // Committee "Other" option
        $("#puja_committee_name").change(function() {
            if ($(this).val() === "Other") {
                $("#otherCommitteeBox").removeClass("d-none");
            } else {
                $("#otherCommitteeBox").addClass("d-none");
            }
        });

        // Dhunuchi Nach
        $("input[name='dhunuchi']").change(function() {
            if ($(this).val() === "1") {
                $("#ifDhunuchiYes").removeClass("d-none");
            } else {
                $("#ifDhunuchiYes").addClass("d-none");
            }
        });

        $("input[name='in_newtown'][value='1']").prop("checked", true).trigger("change");
        $("input[name='dhunuchi'][value='0']").prop("checked", true).trigger("change");

        $("#register").validate({
            rules: {
                in_newtown: {
                    required: true,
                },
                action_area: {
                    required: function() {
                        return $("input[name='in_newtown']:checked").val() == "1";
                    }
                },
                category: {
                    required: function() {
                        return $("input[name='in_newtown']:checked").val() == "1";
                    }
                },
                puja_committee_name: {
                    required: function() {
                        return $("input[name='in_newtown']:checked").val() == "1";
                    },
					remote: "{{ url('/form_validate') }}"
                },
                puja_committee_name_other: {
                    required: function() {
                        return $("#puja_committee_name").val() === "Other";
                    },
					remote: "{{ url('/form_validate') }}"
                },
                puja_committee_name_text: {
                    required: function() {
                        return $("input[name='in_newtown']:checked").val() == "0";
                    },
					remote: "{{ url('/form_validate') }}"
                },
                puja_committee_address: {
                    required: true,
                },
                secretary_name: {
                    required: true,
                },
                secretary_mobile: {
                    required: true,
                    indianMobile: true,
					remote: "{{ url('/form_validate') }}"
                },
                chairman_name: {
                    required: true,
                },
                chairman_mobile: {
                    required: true,
                    indianMobile: true,
					remote: "{{ url('/form_validate') }}"
                },
                proposed_immersion_date: {
                    required: true,
                },
                proposed_immersion_time: {
                    required: true,
                    timeRange: true,
                },
                no_of_vehicles: {
                    required: false
                },
                vehicle_no: {
                    required: false,
                    vehicleCountMatch: true,
                    vehicleNoFormat: true,
                    vehicleNoUnique: true,
                },
                dhunuchi: {
                    required: true,
                },
                team_members: {
                    required: function() {
                        return $("input[name='dhunuchi']:checked").val() == "1";
                    },
                    digits: true,
                    min: 1,
                    max: {{ setting('DHUNUCHI_TEAM', 20) }}
                }
            },
            messages: {
                in_newtown: "Please select whether the puja is in New Town area",
                action_area: "Please select an action area",
                category: "Please select a category",
                puja_committee_name: "Please select a puja committee",
                puja_committee_name_other: "Please enter the committee name",
                puja_committee_name_text: "Please enter the puja committee name",
                puja_committee_address: "Please enter the committee address",
                secretary_name: "Please enter the secretary's name",
                secretary_mobile: {
                    required: "Please enter the secretary's mobile number",
                    indianMobile: "Please enter a valid Indian mobile number"
                },
                chairman_name: "Please enter the chairman/president's name",
                chairman_mobile: {
                    required: "Please enter the chairman's mobile number",
                    indianMobile: "Please enter a valid Indian mobile number"
                },
                proposed_immersion_date: "Please select a proposed immersion date",
                proposed_immersion_time: {
                    required: "Please select a proposed immersion time",
                    timeRange: "Please select the Immersion time in between 4PM to 11:59PM"
                },
                vehicle_no: {
                    vehicleCountMatch: "Vehicle numbers count must match selected number",
                    vehicleNoFormat: "Each vehicle number must be valid (e.g. WB12AB1234)",
                    vehicleNoUnique: "Duplicate vehicle numbers are not allowed",
                },
                dhunuchi: "Please select Yes or No for Dhunuchi Nach participation",
                team_members: "Please enter number of team members (1–{{ setting('DHUNUCHI_TEAM',20) }})"
            }
        });

    });

    function register_submt(e) {
        e.preventDefault(); // stop default form submission
        if ($("#register").valid()) {
            webserv("POST", "{{ url('/register') }}", "register",
                function ok(d) {
                    myAlert(d["msg"], "success", "Ok", function() {
                        window.location.href = "{{ route('puja.thanks', ['token' => '___ID___']) }}".replace("___ID___", d.data);
                    });
                });
        }
        return false;
    }
</script>
@endsection