@extends('layouts.app')
@section('styles')
<style>
</style>
@endsection
@section('content')

@php
    $opts = [
        "edit"=>"editUser",
    ];
    if(hasRole("a")) {
       $opts["add"] = "addUser";
       $opts["delete"] = "delUser";
    }

    $tbldata = [
        [ 'data'=>'name', ], 
        [ 'data'=>'email', ], 
        [ 'data'=>'role','className'=>'text-center', ], 
        [ 'data'=>'stat','className'=>'text-center', ], 
        [ 'data'=>'logged_at', ], 
    ];
@endphp
<div class="container-fluid m-0 p-2">

<x-table name="userTable" title="Users" :url="route('users.data')" :data=$tbldata :opts=$opts />

<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <form id="adduser" onsubmit="return adduser_submt(event)" novalidate="novalidate">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white py-1">
          <h5 class="modal-title" id="userModalLabel">Add User</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <div class="row gy-2">

        <input type="hidden" name="id" id="id" />
        <x-text name="email" icon="envelope" title="Email" required="true" />
        <x-text name="name" icon="person" title="Name" required="true" />
        <x-number name="phone" icon="telephone" title="Phone" />
        <x-password size="6" name="password" title="Password" required="true" />
        <x-password size="6" name="password2" title="Repeat Password" required="true" />
        <x-select size="6" icon="people" name="role" title="Role" :value="roleDict()" required="true" />
        <x-select size="6" icon="check" name="stat" title="Status" :value="statDict()" required="true" />

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
$(function () {
    $("#adduser").validate({
        rules:{
            phone: {
                required: false,
                minlength: 10,
            },
            password: {
                required: function() { return $("#id").val() == ""; },
                minlength: 6
            },
            password2: {
                required: function() { return $("#id").val() == ""; },
                equalTo: "#password"
            }
        },
        messages: {
            password2: { equalTo: "Passwords do not match" },
        },
    });
});

function adduser_submt (e) {
    e.preventDefault(); 
    if($("#adduser").valid()) {
        const id = $('#id').val();
        const isEdit = id !== "";
        const url = isEdit ? `{{ url('user/users') }}/${id}` : `{{ url('user/users/add') }}`;
        const method = isEdit ? 'PUT' : 'POST';
        webserv(method, url, "adduser", function ok(d) {
            toastr.success(d["msg"]);
            $('#userModal').modal('hide');
            $('#userTable').DataTable().ajax.reload();
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
@endsection
