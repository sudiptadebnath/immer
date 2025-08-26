@extends('layouts.app')

@section('content')
<div class="container d-flex align-items-center justify-content-center login-container h-100">
<x-col md="6" lg="4">
<x-card icon="box-arrow-in-right" title="Log in">
    <form id="signin" onsubmit="return signin_submt()" novalidate="novalidate">
    <div class="row gy-2">
        <x-text name="email" icon="person" title="Userid/Mail" required=true />
        <x-password name="password" icon="key" title="Password" required=true />
        <div class="col-md-12">
            <a href = "{{ url('/forgot') }}" >Forget Password ?</a>
        </div>
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary btn-sm w-100">
                <i class="bi bi-box-arrow-in-right"></i> Log in
            </button>
        </div>
        <div class="col-md-12">
            <a href = "{{ url('/register') }}" class="btn btn-outline-primary btn-sm w-100">
                <i class="bi bi-person-plus"></i> Sign up
            </a>
        </div>
    </div></form>
</x-card>
</x-col>
</div>
    
@endsection

@section('scripts')
<script> 

$.validator.addMethod("emailOrUid", function(value, element) {
  const isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
  const isUserID = /^[a-zA-Z0-9@._-]+$/.test(value);
  return this.optional(element) || isEmail || isUserID;
});

$(document).ready(function ($) {
    $("#signin").validate({
      rules: {
        email: {
          required: true,
          emailOrUid: true,
        },
        password: {
          required: true,
          minlength: 4
        }
      },
      messages: {
        email: {
          required: "Please enter your userid/email",
          emailOrUid: "Enter a valid email or userid"
        },
        password: {
          required: "Please enter your password",
          minlength: "Password must be at least 4 characters"
        }
      }
    });
});

function signin_submt() {
    if($("#signin").valid()) {
        webserv("POST","{{ url('/login') }}", "signin", 
        function ok(d) { goLnk("{{ url('/user/dashboard') }}"); });
    }
    return false;
}

</script>
@endsection