@extends('layouts.app')

@php
$action_area = dbVals("action_areas","name","view_order","asc");
$category = dbVals("puja_categories","name","view_order","asc");
$immer_dts = dbVals("puja_immersion_dates",["idate","name"],"idate","asc");
@endphp


@section('styles')
<style>
tr.admin .actbtn1,tr.operator .actbtn1,tr.scanner .actbtn1 {
    display: none;
}
</style>
@endsection
@section('content')

@php
    $opts = [
        //"imp"=>[0,1,2,3,4,5,6,7,8,9,10,11,12,13],
        "add"=> "addPuja",
        "edit"=>"editPuja",
        "actions"=>'
            <button class="actbtn1 btn btn-link text-primary px-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Digital Pass" onclick="getGatepass(__)">
                <i class="bi bi-qr-code-scan"></i>
            </button>
            <button class="actbtn1 btn btn-link text-warning px-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Immersion Pass" onclick="getEntrySlip(__)">
                <i class="bi bi-ticket-perforated"></i>
            </button>
        ',
    ];
    if(hasRole("a")) {
       $opts["delete"] = "delPuja";
    }

    $tbldata = [
        [ 'data'=>'action_area',"th"=>"Action Area", 'render' => 'function (data, type, row) {
            let aa = row.action_area ? row.action_area : "";
            let cat = row.category ? row.category : "";
            if (aa && cat) return "Action Area - "+ aa + "<br>Category - " + cat ;
            else if (aa) return "Action Area - "+ aa;
            else if (cat) return "Category - " + cat;
            return "";
        }', ], 
        [ 'data'=>'category','visible'=>false ], 
        [ 'data'=>'puja_committee_name',"th"=>"Puja Committee", 'render' => 'function (data, type, row) {
            let name = row.puja_committee_name ? row.puja_committee_name : "";
            let add = row.puja_committee_address ? row.puja_committee_address : "";
            if (name && add) return name + "<hr><b>Address - </b>" + add ;
            else if (name) return name;
            else if (add) return "<b>Address - </b>"+add;
            return "";
        }', ], 
        [ 'data'=>'puja_committee_address','visible'=>false ], 
        [ 'data'=>'secretary_name',"th"=>"Secretary", 'render' => 'function (data, type, row) {
            let name = row.secretary_name ? row.secretary_name : "";
            let mobile = row.secretary_mobile ? row.secretary_mobile : "";
            if(mobile==row.verified_mobile) mobile = "<b>"+ mobile + "<b>";
            if (name && mobile) return name + " (" + mobile + ")";
            else if (name) return name;
            else if (mobile) return mobile;
            return "";
        }', ], 
        [ 'data'=>'secretary_mobile','visible'=>false ], 
        [ 'data'=>'chairman_name',"th"=>"Chairman", 'render' => 'function (data, type, row) {
            let name = row.chairman_name ? row.chairman_name : "";
            let mobile = row.chairman_mobile ? row.chairman_mobile : "";
            if(mobile==row.verified_mobile) mobile = "<b>"+ mobile + "<b>";
            if (name && mobile) return name + " (" + mobile + ")";
            else if (name) return name;
            else if (mobile) return mobile;
            return "";
        }', ], 
        [ 'data'=>'chairman_mobile','visible'=>false ], 
        [ 'data'=>'proposed_immersion_date',"th"=>"Immersion Date", 'render' => 'function (data, type, row) {
            let dt = row.proposed_immersion_date ? row.proposed_immersion_date : "";
            let tm = row.proposed_immersion_time ? row.proposed_immersion_time : "";
            let ans="";
            if (dt && tm) ans += dt + "<br>" + tm;
            else if (dt) ans += dt;
            else if (tm) ans += tm;
            let vc = row.no_of_vehicles ? row.no_of_vehicles : "";
            let vns = row.vehicle_no ? row.vehicle_no : "";
            if (vc && vns) ans += "<br>Vehicle - " + vc + " : " + vns;
            else if (vc) ans += "<br>Vehicle - " + vc;
            else if (vns) ans += "<br>Vehicle - " + vns;
            return ans;
        }', ], 
        [ 'data'=>'proposed_immersion_time','visible'=>false ], 
    ];
@endphp
<div class="container-fluid m-0 p-2">

<x-table name="pujaTable" title="Pujas" :url="route('puja.data')" :data=$tbldata :opts=$opts />

<div class="modal fade" id="pujaModal" tabindex="-1" aria-labelledby="pujaModalLabel" aria-hidden="true"
data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg">
      <form id="register" onsubmit="return register_submt(event)" novalidate="novalidate">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white py-1">
          <h5 class="modal-title" id="pujaModalLabel">Add Puja</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <div class="row gy-2">

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
        <div id="ifYes" class="d-none col-md-12 row g-2 m-0 px-2">
            <x-select icon="geo-alt" size="6" name="action_area" title="Action Area"
             :value="$action_area" />
            <x-select icon="tags" size="6" name="category" title="Category" 
             :value="$category" />
            <x-select icon="people" name="puja_committee_name" title="Puja Committee" />
            <div class="mb-2 d-none" id="otherCommitteeBox">
                <input type="text" class="form-control" name="puja_committee_name_other"
                 placeholder="Enter Committee Name" >
            </div>
        </div>

        {{-- If No --}}
        <div id="ifNo" class="d-none col-md-12 d-flex flex-column gap-2">
            <x-text name="puja_committee_name_text" icon="person" title="Puja Committee Name" />
        </div>

        {{-- Common fields --}}
        <x-textarea name="puja_committee_address" icon="house" title="Puja Committee Address" />
        <x-text size="6" name="secretary_name" icon="person" title="Secretary Name" />
        <x-number size="6" name="secretary_mobile" icon="telephone" title="Secretary Mobile" required="true" />
        <x-text size="6" name="chairman_name" icon="person-circle" title="Chairman/President Name" />
        <x-number size="6" name="chairman_mobile" icon="telephone" title="Chairman/President Mobile" />

        {{-- Immersion --}}
        <x-select size="6" icon="calendar-date" name="proposed_immersion_date" title="Proposed Immersion Date"
         :value="$immer_dts" required="true" />
        <x-flattime size="6" name="proposed_immersion_time" title="Immersion Time"  icon="clock">
            Range 16:00 - 23:59
        </x-flattime>
        <x-select size="4" icon="people" name="no_of_vehicles" title="No of Vehicles" :value="['1'=>'1','2'=>'2','3'=>'3']" />
        <x-text size="8" name="vehicle_no" title="Vehicle No(s) (optional)"  icon="truck-front">
            Vehicle No(s) separated by comma
        </x-text>

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

        </div>
        </div>
        <div class="modal-footer py-1">
          <button type="submit" class="btn btn-sm btn-primary">Save</button>
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
      </form>
  </div>
</div>

</div>

@endsection

@section('scripts')

<script>

function getGatepass(id) {
    webserv("GET",`puja/${id}`, {}, function (d) {
        let puja = d["data"];
        let url = "{{ route('puja.gpass', ['token' => '___TOKEN___']) }}"
            .replace("___TOKEN___", puja['token']);
        window.open(url, "_blank");
    });
}

function getEntrySlip(id) {
    webserv("GET", "{{ url('user/puja/has_entryslip') }}/" + id, {}, function (resp) {
        const printUrl = "{{ url('user/puja/entryslip') }}/" + resp.data;
        const w = window.open(printUrl, '_blank');
        w.onload = function() { w.print(); };
    });
}

function loadCommittees(actionArea, category, selected = '') {
    webserv("GET", `{{ route('conf.get.committees') }}`, {action_area: actionArea, category: category}, function (resp) {
        let list = resp.data || [];
        let $ddl = $("#puja_committee_name");
        let found = false;
        $ddl.empty().append('<option value="">Select Puja Committee</option>');
        list.forEach(function (item) {
            if(item.name === selected) found=true;
            let sel = (item.name === selected) ? 'selected' : '';
            $ddl.append(`<option value="${item.name}" ${sel} data-address="${item.puja_address ?? ''}">${item.name}</option>`);
        });
        $ddl.append('<option value="Other">Other</option>');
        if (selected) {
            if(!found) {
                $("#puja_committee_name_text").val(selected);
            } else {
                $("#puja_committee_name_text").val("");
            }
        }        
    });
}

$(function () {

    $("#action_area, #category").change(function () {
        let actionArea = $("#action_area").val();
        let category = $("#category").val();
        loadCommittees(actionArea, category);
    });

    // on select committee → place address
    $("#puja_committee_name").change(function () {
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

    $("#register").validate({
        rules: {
            in_newtown: {
                 required: false,
            },
            action_area: {
                 required: false,
            },
            category: {
                required: false,
            },
            puja_committee_name: {
                required: function() { return $("input[name='in_newtown']:checked").val() == "1"; },
				remote: {
					url: "{{ url('/form_validate') }}",
					type: "get",
					data: {
						name: function() {
							return $("#puja_committee_name").val();
						}
					},
					depends: function(element) {
						return $('#id').val()=="";
					}
				}
			},
            puja_committee_name_other: {
                required: function() { return $("#puja_committee_name").val() === "Other"; },
				remote: {
					url: "{{ url('/form_validate') }}",
					type: "get",
					data: {
						name: function() {
							return $("#puja_committee_name_other").val();
						}
					},
					depends: function(element) {
						return $('#id').val()=="";
					}
				}
            },
            puja_committee_name_text: {
                required: function() { return $("input[name='in_newtown']:checked").val() == "0"; },
				remote: {
					url: "{{ url('/form_validate') }}",
					type: "get",
					data: {
						name: function() {
							return $("#puja_committee_name_text").val();
						}
					},
					depends: function(element) {
						return $('#id').val()=="";
					}
				}
            },
            puja_committee_address: {
                required: true,
            },
            // secretary_name: {
            //     required: true,
            // },
            secretary_mobile: {
                required: true,
                indianMobile: true,
				remote: {
					url: "{{ url('/form_validate') }}",
					type: "get",
					data: {
						name: function() {
							return $("#secretary_mobile").val();
						}
					},
					depends: function(element) {
						return $('#id').val()=="";
					}
				}
            },
            // chairman_name: {
            //     required: true,
            // },
            chairman_mobile: {
                required: false,
                indianMobile: true,
				remote: {
					url: "{{ url('/form_validate') }}",
					type: "get",
					data: {
						name: function() {
							return $("#chairman_mobile").val();
						}
					},
					depends: function(element) {
						return $('#id').val()=="";
					}
				},
				notEqualTo: "#secretary_mobile",
            },
            proposed_immersion_date: {
                required: true,
            },
            proposed_immersion_time: {
                required: false,
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
            // dhunuchi: {
            //     required: true,
            // },
            team_members: {
                required: function() { return $("input[name='dhunuchi']:checked").val() == "1"; },
                digits: true,
                min: 1,
                max: {{ setting('DHUNUCHI_TEAM',20) }}
            }
        },
        messages: {
            // in_newtown: "Please select whether the puja is in New Town area",
            // action_area: "Please select an action area",
            // category: "Please select a category",
			puja_committee_name: {
				required: "Please select a puja committee",
				remote: "❌ This puja committee is already registered",
			},
			puja_committee_name_other: {
				required: "Please enter the committee name",
				remote: "❌ This puja committee is already registered",
			},
			puja_committee_name_text: {
				required: "Please enter the puja committee name",
				remote: "❌ This puja committee is already registered",
			},
            puja_committee_address: "Please enter the committee address",
            secretary_name: "Please enter the secretary's name",
            secretary_mobile: {
                required: "Please enter the secretary's mobile number",
                indianMobile: "Please enter a valid Indian mobile number",
				remote: "❌ This Secretary Mobile is already taken",
            },
            // chairman_name: "Please enter the chairman/president's name",
            chairman_mobile: {
                required: "Please enter the chairman's mobile number",
                indianMobile: "Please enter a valid Indian mobile number",
				remote: "❌ This Chairman Mobile is already taken",
				notEqualTo: "Chairman Mobile cannot be the same as Secretary Mobile",
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
            // dhunuchi: "Please select Yes or No for Dhunuchi Nach participation",
            team_members: "Please enter number of team members (1–{{ setting('DHUNUCHI_TEAM',20) }})"
        }
    });


});

function register_submt (e) {
    e.preventDefault(); // stop default form submission
    if($("#register").valid()) {
        const id = $('#id').val();
        const isEdit = id !== "";
        const url = isEdit ? `{{ url('user/puja/editadmin') }}/${id}` : `{{ url('user/puja/addadmin') }}`;
        const method = isEdit ? 'PUT' : 'POST';
        webserv(method, url, "register", function ok(d) {
            toastr.success(d["msg"]);
            $('#pujaModal').modal('hide');
            $('#pujaTable').DataTable().ajax.reload();
        });
    }
}

function setToday() {
	const today = new Date();
	const yyyy = today.getFullYear();
	const mm = String(today.getMonth() + 1).padStart(2, '0');
	const dd = String(today.getDate()).padStart(2, '0');
	const formattedDate = `${yyyy}-${mm}-${dd}`;
	if ($("#proposed_immersion_date option[value='" + formattedDate + "']").length > 0) {
		$("#proposed_immersion_date").val(formattedDate).trigger('change');
	}	
}

function setCurTime() {
    const now = new Date();
    let hh = now.getHours();
    let mm = now.getMinutes();
    if (hh < 16 || hh > 23) { hh = 16; mm = 0; }
    const formattedTime = String(hh).padStart(2, '0') + ':' + String(mm).padStart(2, '0');
    $("#proposed_immersion_time").val(formattedTime).trigger('change');
}

function addPuja() {
    $("input[name='in_newtown'][value='1']").prop("checked", true).trigger("change");
    $("input[name='dhunuchi'][value='0']").prop("checked", true).trigger("change");
    
    $('#register').find("input[type=text], input[type=number], input[type=password], textarea").val('');
    $('#register').find("select").prop('selectedIndex', 0); // reset all dropdowns
    $('#id').val(''); 
	setToday(); setCurTime();
	//$('#secretary_mobile_otp').prop('required',true);
    $('#pujaModalLabel').text("Add Puja");
    $('.error').text('');
    $('#pujaModal').modal('show');
}

function editPuja(id) {
    webserv("GET",`puja/${id}`, {}, function (d) {
        let puja = d["data"];
        $('#id').val(puja.id);
        $('#action_area').val(puja.action_area);
        $('#category').val(puja.category);
        loadCommittees(puja.action_area, puja.category, puja.puja_committee_name);
        $('#puja_committee_address').val(puja.puja_committee_address);
        $('#secretary_name').val(puja.secretary_name);
        $('#secretary_mobile').val(puja.secretary_mobile);
		//$('#secretary_mobile_otp').prop('required',false);
        $('#chairman_name').val(puja.chairman_name);
        $('#chairman_mobile').val(puja.chairman_mobile);
        $('#proposed_immersion_date').val(puja.proposed_immersion_date);
        $('#proposed_immersion_time').val(puja.proposed_immersion_time);
        $('#no_of_vehicles').val(puja.no_of_vehicles);
        $('#vehicle_no').val(puja.vehicle_no);
        $('#team_members').val(puja.team_members);

        if (puja.action_area) {
            $("input[name='in_newtown'][value='1']").prop("checked", true).trigger("change");
        } else {
            $("input[name='in_newtown'][value='0']").prop("checked", true).trigger("change");
        }
        if (puja.team_members) {
            $("input[name='dhunuchi'][value='1']").prop("checked", true).trigger("change");
        } else {
            $("input[name='dhunuchi'][value='0']").prop("checked", true).trigger("change");
        }
		
		if(!puja.proposed_immersion_date) setToday(); 
		if(!puja.proposed_immersion_time) setCurTime();

        $('#pujaModalLabel').text('Edit Puja');
        $('.error').text('');
        $('#pujaModal').modal('show');
    });    
}

function delPuja(id) {
    myAlert("Are you sure you want to delete this puja ?","danger","Yes", function() {
        webserv("DELETE", `puja/${id}`, {}, function (d) {
            toastr.success(d["msg"]);
            $('#pujaTable').DataTable().ajax.reload();
        });        
    },"No");
}

</script>
@endsection
