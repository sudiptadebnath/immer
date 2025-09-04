@extends('layouts.app')

@section('content')
@php
    $opts_immerdt = [
        "add"=>"addData_immerdt",
        "edit"=>"editData_immerdt",
        "delete"=>"delData_immerdt",

    ];
    $data_immerdt = [
        [ 'data'=>'name', ], 
        [ 'data'=>'idate', "th"=>"Date" ], 
    ];
@endphp
<x-table name="table_immerdt" title="Immertion Dates" :url="route('conf.data.immerdt')" :data=$data_immerdt :opts=$opts_immerdt />
<div class="modal fade" id="modal_immerdt" tabindex="-1" aria-labelledby="modal_immerdtLabel" aria-hidden="true"
data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog">
      <form id="form_immerdt" onsubmit="return form_immerdt_submt(event)" novalidate="novalidate">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white py-1">
          <h5 class="modal-title" id="modal_immerdtLabel">Add</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <div class="row gy-2">
        <input type="hidden" name="id" id="id" />
        <x-datetime name="idate" icon="info-circle" title="Date" required="true" />
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
function form_immerdt_submt (e) {
    e.preventDefault(); 
    if($("#form_immerdt").valid()) {
        const id = $("#form_immerdt").find('#id').val();
        const isEdit = id !== "";
        const url = isEdit ? `{{ url('user/conf/edit/immerdt') }}/${id}` : `{{ url('user/conf/add/immerdt') }}`;
        const method = isEdit ? 'PUT' : 'POST';
        webserv(method, url, "form_immerdt", function ok(d) {
            toastr.success(d["msg"]);
            $('#modal_immerdt').modal('hide');
            $('#table_immerdt').DataTable().ajax.reload(null, false);
        });
    }
}
function addData_immerdt() {
    $('#form_immerdt').find("input[type=text], input[type=number], input[type=password], textarea").val('');
    $('#form_immerdt').find(".modal-title").text("Add");
    $('#form_immerdt').find('.error').text('');
    $('#modal_immerdt').modal('show');
}
function editData_immerdt(id) {
    webserv("GET", `{{ url('user/conf/get/immerdt') }}/${id}`, {}, function (d) {
        let data = d["data"];
        $('#id').val(data.id);
        $('#name').val(data.name);
        set_dtp_idate(data.idate);
        $('#form_immerdt').find(".modal-title").text("Edit");
        $('#form_immerdt').find('.error').text('');
        $('#modal_immerdt').modal('show');
    });    
}
function delData_immerdt(id) {
    myAlert("Are you sure you want to delete this record ?","danger","Yes", function() {
        webserv("DELETE",  `{{ url('user/conf/del/immerdt') }}/${id}`, {}, function (d) {
            toastr.success(d["msg"]);
            $('#table_immerdt').DataTable().ajax.reload(null, false);
        });        
    },"No");
}
</script>
@endpush
