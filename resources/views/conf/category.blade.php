@extends('layouts.app')

@section('content')
@php
    $opts_category = [
        "rowreorder"=>["view_order",route('conf.updateorder.category')],
        "add"=>"addData_category",
        "edit"=>"editData_category",
        "delete"=>"delData_category",

    ];
    $data_category = [
        [ 'data'=>'name', ], 
        [ 'data'=>'view_order','visible'=>false ], 
    ];
@endphp
<x-table name="table_category" title="Categories" :url="route('conf.data.category')" :data=$data_category :opts=$opts_category />
<div class="modal fade" id="modal_category" tabindex="-1" aria-labelledby="modal_categoryLabel" aria-hidden="true"
data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog">
      <form id="form_category" onsubmit="return form_category_submt(event)" novalidate="novalidate">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white py-1">
          <h5 class="modal-title" id="modal_categoryLabel">Add</h5>
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
function form_category_submt (e) {
    e.preventDefault(); 
    if($("#form_category").valid()) {
        const id = $("#form_category").find('#id').val();
        const isEdit = id !== "";
        const url = isEdit 
            ? "{{ route('conf.edit.category', ['id' => '__id__']) }}".replace('__id__', id)
            : "{{ route('conf.add.category') }}";
        const method = isEdit ? 'PUT' : 'POST';
        webserv(method, url, "form_category", function ok(d) {
            toastr.success(d["msg"]);
            $('#modal_category').modal('hide');
            $('#table_category').DataTable().ajax.reload(null, false);
        });
    }
}
function addData_category() {
    $('#form_category').find("input[type=text], input[type=number], input[type=password], textarea").val('');
    $('#form_category').find(".modal-title").text("Add");
    $('#form_category').find('.error').text('');
    $('#modal_category').modal('show');
}
function editData_category(id) {
    webserv("GET", "{{ route('conf.get.category', ['id' => '__id__']) }}".replace('__id__', id), {}, function (d) {
        let data = d["data"];
        $('#id').val(data.id);
        $('#name').val(data.name);
        $('#form_category').find(".modal-title").text("Edit");
        $('#form_category').find('.error').text('');
        $('#modal_category').modal('show');
    });    
}
function delData_category(id) {
    myAlert("Are you sure you want to delete this record ?","danger","Yes", function() {
        webserv("DELETE", "{{ route('conf.del.category', ['id' => '__id__']) }}".replace('__id__', id), {}, function (d) {
            toastr.success(d["msg"]);
            $('#table_category').DataTable().ajax.reload(null, false);
        });        
    },"No");
}
</script>
@endpush
