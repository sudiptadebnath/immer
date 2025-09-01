@extends('layouts.app')

@section('content')
@php
    $opts = [
        "add"=>"addData",
        "edit"=>"editData",
        "delete"=>"delData",
    ];
    $tbldata = [
        [ 'data'=>'name', ], 
    ];
@endphp
<x-table name="datatable" title="Action Areas" :url="route('conf.action_data')" :data=$tbldata :opts=$opts />

<div class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <form id="dataform" onsubmit="return dataform_submt(event)" novalidate="novalidate">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white py-1">
          <h5 class="modal-title" id="dataModalLabel">Add Action Area</h5>
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
</div>

@endsection

@push('scripts')
<script>

function dataform_submt (e) {
    e.preventDefault(); 
    if($("#dataform").valid()) {
        const id = $('#id').val();
        const isEdit = id !== "";
        const url = isEdit ? `{{ url('user/conf/action/edit') }}/${id}` : `{{ url('user/conf/action/add') }}`;
        const method = isEdit ? 'PUT' : 'POST';
        webserv(method, url, "dataform", function ok(d) {
            toastr.success(d["msg"]);
            $('#dataModal').modal('hide');
            $('#datatable').DataTable().ajax.reload();
        });
    }
}

function addUser() {
    $('#adduser').find("input[type=text], input[type=number], input[type=password], textarea").val('');
    $('#id').val(''); 
    $('#userModalLabel').text("Add User");
    $('.error').text('');
    $('#userModal').modal('show');
}

function editUser(id) {
    webserv("GET",`users/${id}`, {}, function (d) {
        let user = d["data"];
        $('#id').val(user.id);
        $('#name').val(user.name);
        $('#email').val(user.email);
        $('#phone').val(user.phone);
        $('#stat').val(user.stat);
        $('#role').val(user.role);
        $('#password').val('');
        $('#password2').val('');
        $('#userModalLabel').text('Edit User');
        $('.error').text('');
        $('#userModal').modal('show');
    });    
}

function delUser(id) {
    myAlert("Are you sure you want to delete this user ?","danger","Yes", function() {
        webserv("DELETE", `users/${id}`, {}, function (d) {
            toastr.success(d["msg"]);
            $('#userTable').DataTable().ajax.reload();
        });        
    },"No");
}

</script>
@endpush
