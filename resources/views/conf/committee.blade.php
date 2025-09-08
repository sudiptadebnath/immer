@extends('layouts.app')

@section('content')
@php
	$action_area = dbVals("action_areas",["id","name"],"view_order","asc");
	$category = dbVals("puja_categories",["id","name"],"view_order","asc");

    $opts_committee = [
        "rowreorder"=>["view_order",route('conf.updateorder.committee')],
        "add"=>"addData_committee",
        "edit"=>"editData_committee",
        "delete"=>"delData_committee",

    ];
    $data_committee = [
        [ 'data'=>'action_area', 'name'=>'action_areas.name' ], 
        [ 'data'=>'category', 'name'=>'puja_categories.name' ], 
        [ 'data'=>'name'  ], 
        [ 'data'=>'puja_address','th'=>'Address'], 
        [ 'data'=>'view_order','visible'=>false ], 
    ];
@endphp
<x-table name="table_committee" title="Puja Committees" :url="route('conf.data.committee')" :data=$data_committee :opts=$opts_committee />
<div class="modal fade" id="modal_committee" tabindex="-1" aria-labelledby="modal_committeeLabel" aria-hidden="true"
data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog">
      <form id="form_committee" onsubmit="return form_committee_submt(event)" novalidate="novalidate">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white py-1">
          <h5 class="modal-title" id="modal_committeeLabel">Add</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <div class="row gy-2">
			<input type="hidden" name="id" id="id" />
			<x-select icon="geo-alt" size="6" name="action_area" title="Action Area"
			:value="$action_area" required="true" />
			<x-select icon="tags" size="6" name="category" title="Category" 
			:value="$category" required="true" />
			<x-text name="name" icon="info-circle" title="Name" required="true" />
			<x-textarea name="puja_committee_address" icon="house" title="Puja Committee Address" />
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
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $("#form_committee").validate({
        rules: {
            action_area: {
                required: true
            },
            category: {
                required: true
            },
            name: {
                required: true,
                maxlength: 200
            },
            puja_committee_address: {
                maxlength: 300
            }
        },
        messages: {
            action_area: {
                required: "Please select an Action Area."
            },
            category: {
                required: "Please select a Category."
            },
            name: {
                required: "Committee name is required.",
                maxlength: "Committee name may not exceed 200 characters."
            },
            puja_committee_address: {
                maxlength: "Address may not exceed 300 characters."
            }
        },
    });
});


function form_committee_submt (e) {
    e.preventDefault(); 
    if($("#form_committee").valid()) {
        const id = $("#form_committee").find('#id').val();
        const isEdit = id !== "";
        const url = isEdit ? `{{ url('user/conf/edit/committee') }}/${id}` : `{{ url('user/conf/add/committee') }}`;
        const method = isEdit ? 'PUT' : 'POST';
        webserv(method, url, "form_committee", function ok(d) {
            toastr.success(d["msg"]);
            $('#modal_committee').modal('hide');
            $('#table_committee').DataTable().ajax.reload(null, false);
        });
    }
}
function addData_committee() {
    $('#form_committee').find("input[type=text], input[type=number], input[type=password], textarea").val('');
    $('#form_committee').find(".modal-title").text("Add");
    $('#form_committee').find('.error').text('');
    $('#modal_committee').modal('show');
}
function editData_committee(id) {
    webserv("GET", `{{ url('user/conf/get/committee') }}/${id}`, {}, function (d) {
        let data = d["data"];
		
        $('#id').val(data.id);
        $('#name').val(data.name);
        $('#action_area').val(data.action_area_id).trigger('change'); 
        $('#category').val(data.puja_category_id).trigger('change'); 
        $('#puja_committee_address').val(data.puja_address ?? '');

        $('#form_committee').find(".modal-title").text("Edit");
        $('#form_committee').find('.error').text('');
        $('#modal_committee').modal('show');
    });    
}
function delData_committee(id) {
    myAlert("Are you sure you want to delete this record ?","danger","Yes", function() {
        webserv("DELETE",  `{{ url('user/conf/del/committee') }}/${id}`, {}, function (d) {
            toastr.success(d["msg"]);
            $('#table_committee').DataTable().ajax.reload(null, false);
        });        
    },"No");
}
</script>
@endpush
