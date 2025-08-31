@extends('layouts.app')
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
        "imp"=>[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16],
        "add"=>"addUser",
        "edit"=>"editUser",
        "actions"=>'
            <a href="'. route('user.gpass', ['id' => '__']) .'" target="_blank" class="actbtn1 btn btn-link text-secondary px-1">
                <i class="bi bi-ticket-perforated"></i>
            </a>
        ',
    ];
    if(hasRole("a")) {
       $opts["delete"] = "delUser";
    }

    $tbldata = [
        [ 'data'=>'action_area', ], 
        [ 'data'=>'category', ], 
        [ 'data'=>'puja_committee_name', ], 
        [ 'data'=>'puja_committee_address', ], 
        [ 'data'=>'secretary_name', ], 
        [ 'data'=>'secretary_mobile', ], 
        [ 'data'=>'chairman_name', ], 
        [ 'data'=>'chairman_mobile', ], 
        [ 'data'=>'proposed_immersion_date', ], 
        [ 'data'=>'vehicle_no', ], 
        [ 'data'=>'team_members', ], 
        [ 'data'=>'*role','className'=>'text-center', ], 
        [ 'data'=>'stat','className'=>'text-center', ], 
        [ 'data'=>'logged_at', ], 
    ];
@endphp
<div class="container m-0 p-2">

<x-table name="userTable" title="Users" :url="route('users.data')" :data=$tbldata :opts=$opts />

<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <form id="register" onsubmit="return __register_submt()" novalidate="novalidate">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white py-1">
          <h5 class="modal-title" id="userModalLabel">Add User</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <div class="row gy-2">
            <x-register_compo :btns="false" cb="saveUser" />
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


function saveUser () {
    const id = $('#id').val();
    const isEdit = id !== "";
    const url = isEdit ? `{{ url('user/users') }}/${id}` : `{{ url('register') }}`;
    const method = isEdit ? 'PUT' : 'POST';
    webserv(method, url, "register", function ok(d) {
        toastr.success(d["msg"]);
        $('#userModal').modal('hide');
        $('#userTable').DataTable().ajax.reload();
    });
}

function addUser() {
    $('#register').find("input[type=text], input[type=number], input[type=password], textarea").val('');
    //$('#register').find("input[type=checkbox], input[type=radio]").prop('checked', false);
    $('#id').val(''); 
    $('#userModalLabel').text("Add User");
    $('.error').text('');
    $('#userModal').modal('show');
}

function editUser(id) {
    webserv("GET",`users/${id}`, {}, function (d) {
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
        $('#stat').val(user.stat);
        $('#role').val(user.role);
        $('#password').val('');
        $('#password2').val('');
        $('#userModalLabel').text('Edit User');
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
