@extends('layouts.app')

@section('content')
@php
    $opts_action = [
        "rowreorder"=>["view_order",route('conf.updateorder.action')],
        "add"=>"addData_action",
        "edit"=>"editData_action",
        "delete"=>"delData_action",

    ];
    $data_action = [
        [ 'data'=>'name', ], 
        [ 'data'=>'view_order','visible'=>false ], 
    ];
@endphp
<x-table name="table_action" title="Action Areas" :url="route('conf.data.action')" :data=$data_action :opts=$opts_action />
<div class="modal fade" id="modal_action" tabindex="-1" aria-labelledby="modal_actionLabel" aria-hidden="true">
  <div class="modal-dialog">
      <form id="form_action" onsubmit="return form_action_submt(event)" novalidate="novalidate">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white py-1">
          <h5 class="modal-title" id="modal_actionLabel">Add</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <div class="row gy-2">
        <input type="hidden" name="id" id="id" />
        <x-text name="name" icon="info-circle" title="Name" required="true" />
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
function form_action_submt (e) {
    e.preventDefault(); 
    if($("#form_action").valid()) {
        const id = $("#form_action").find('#id').val();
        const isEdit = id !== "";
        const url = isEdit ? `{{ url('user/conf/edit/action') }}/${id}` : `{{ url('user/conf/add/action') }}`;
        const method = isEdit ? 'PUT' : 'POST';
        webserv(method, url, "form_action", function ok(d) {
            toastr.success(d["msg"]);
            $('#modal_action').modal('hide');
            $('#table_action').DataTable().ajax.reload(null, false);
        });
    }
}
function addData_action() {
    $('#form_action').find("input[type=text], input[type=number], input[type=password], textarea").val('');
    $('#form_action').find(".modal-title").text("Add");
    $('#form_action').find('.error').text('');
    $('#modal_action').modal('show');
}
function editData_action(id) {
    webserv("GET", `{{ url('user/conf/get/action') }}/${id}`, {}, function (d) {
        let data = d["data"];
        $('#id').val(data.id);
        $('#name').val(data.name);
        $('#form_action').find(".modal-title").text("Edit");
        $('#form_action').find('.error').text('');
        $('#modal_action').modal('show');
    });    
}
function delData_action(id) {
    myAlert("Are you sure you want to delete this record ?","danger","Yes", function() {
        webserv("DELETE",  `{{ url('user/conf/del/action') }}/${id}`, {}, function (d) {
            toastr.success(d["msg"]);
            $('#table_action').DataTable().ajax.reload(null, false);
        });        
    },"No");
}
</script>
@endpush
