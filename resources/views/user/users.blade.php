@extends('layouts.app')
@section('styles')
<style>

</style>
@endsection
@section('content')

@php
    $opts = [
        "imp"=>[0,1,2,3,4,5,6],
        "add"=>"addUser",
        "edit"=>"editUser",
        "delete"=>"delUser",
        "actions"=>'
            <a href="'. route('user.gpass', ['id' => '__']) .'" target="_blank" class="btn btn-link text-secondary px-1">
                <i class="bi bi-ticket-perforated"></i>
            </a>
        ',
    ];
    $tbldata = [
        [ 'data'=>'uid', ], 
        [ 'data'=>'name', ], 
        [ 'data'=>'...address', ], 
        [ 'data'=>'email', ], 
        [ 'data'=>'role','className'=>'text-center', ], 
        [ 'data'=>'stat','className'=>'text-center', ], 
        [ 'data'=>'logged_at', ], 
    ];
@endphp
<x-table name="userTable" title="Users" :url="route('users.data')" :data=$tbldata :opts=$opts />

<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="userForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="userModalLabel">Add User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <div class="row gy-2">
            <input type="hidden" id="id" name="id" />
            <x-text icon="person" name="uid" title="UserID" required=true />
            <x-password size="6" name="password" title="Password" required=true />
            <x-password size="6" name="password2" title="Repeat Password" required=true />
            <x-text icon="person" name="name" title="Name" required=true />
            <x-textarea icon="clipboard-data" name="address" title="Address" required=true />
            <x-text  size="6" icon="envelope" name="email" title="Mail" required=true />
            <x-number  size="6" icon="phone" name="mob" title="Mobile" required=true />
            <x-select  size="6" icon="people" name="role" title="Role" :value="roleDict()" />
            <x-select  size="6" icon="check" name="stat" title="Status" :value="statDict()" />
        </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection

@section('scripts')

<script>

$(document).ready(function () {
    
    $("#userForm").validate({
        rules: {
            uid: {
                required: true,
                minlength: 4,
                maxlength: 20,
                pattern: /^[a-zA-Z0-9._-]+$/
            },
            email: {
                required: true,
                email: true
            },
            password: {
                required: function () {
                    return $('#id').val() === "";
                },
                minlength: 6
            },
            password2: {
                required: function () {
                    return $('#password').val();
                },
                equalTo: "#password"
            },
            name: {
                required: true,
                minlength: 10, // optional, at least 2 chars
                maxlength: 150 // optional, at least 2 chars
            },
            phone: {
                required: true,
                digits: true,        // only digits allowed
                minlength: 10, // optional, at least 2 chars
                maxlength: 10, // optional, at least 2 chars
            },
            address: {
                required: true,
                minlength: 10, // optional, at least 2 chars
                maxlength: 200 // optional, at least 2 chars
            },
            stat: {
                required: true,
            },
        },
        messages: {
            uid: {
                required: "User ID is required",
                minlength: "User ID must be at least 4 characters",
                maxlength: "User ID cannot exceed 20 characters",
                pattern: "Only letters, numbers, dots, underscores, and hyphens are allowed"
            },
            email: {
                required: "Email is required",
                email: "Enter a valid email"
            },
            password: {
                required: "Password is required",
                minlength: "Password must be at least 6 characters"
            },
            password2: {
                required: "Please confirm your password",
                equalTo: "Passwords do not match"
            },
            name: {
                required: "Please enter name",
                minlength: "Please enter name at least 10 char",
                maxlength: "Please enter name at most 150 char"
            },
            phone: {
                required: "Please enter mobile",
                digits: "Please enter mobile digits only",
                minlength: "Please enter mobile exact 10 char",
                maxlength: "Please enter mobile exact 10 char"
            },
            address: {
                required: "Please enter address",
                minlength: "Please enter address at least 10 char",
                maxlength: "Please enter address at most 200 char"
            },
            role: {
                required: "Please select role",
            },
            stat: {
                required: "Please select status",
            },
        }
    });
    $('#userForm').on('submit', function (e) {
        e.preventDefault();
        if (!$(this).valid()) return; 

        const id = $('#id').val();
        const isEdit = id !== "";

        // Prepare URL and method
        const url = isEdit ? `users/${id}` : `/register`;
        const method = isEdit ? 'PUT' : 'POST';

        // Now call webserv
        webserv(method, url, "userForm", function ok(d) {
            toastr.success(d["msg"]);
            $('#userModal').modal('hide');
            $('#userTable').DataTable().ajax.reload();
        });
    });

});

function addUser() {
    $('#userForm')[0].reset();
    $('#id').val(''); 
    $('#userModalLabel').text("Add User");
    $('.error').text('');
    $('#userModal').modal('show');
}

function editUser(id) {
    webserv("GET",`users/${id}`, {}, function (d) {
        let user = d["data"];
        $('#id').val(user.id);
        $('#uid').val(user.uid);
        $('#name').val(user.name);
        $('#address').val(user.address);
        $('#email').val(user.email);
        $('#mob').val(user.mob);
        $('#role').val(user.role);
        $('#stat').val(user.stat);
        $('#password').val('');
        $('#password2').val('');
        $('#userModalLabel').text('Edit User');
        $('#userModal').modal('show');
    });    
}

function delUser(id) {
    myAlert("Are you sure you want to delete this user ?","primary","Yes", function() {
        webserv("DELETE", `users/${id}`, {}, function (d) {
            toastr.success(d["msg"]);
            $('#userTable').DataTable().ajax.reload();
        });        
    },"No");
}

</script>
@endsection
