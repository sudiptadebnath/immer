@extends('layouts.app')

@section('content')
<div class="container d-flex align-items-center justify-content-center login-container h-100">
<div class="col-md-6 col-lg-4">
<x-card icon="box-arrow-in-right" title="Log in">
    <form id="signin" onsubmit="return signin_submt()" novalidate="novalidate">
    <div class="row gy-2">
        <x-text name="email" icon="person" title="Userid/Mail" required=true />
        <x-password name="password" icon="key" title="Password" required=true />
        <x-button type="submit" title="Log in" icon="box-arrow-in-right" />
        @if((bool)setting('USER_SIGNUP','1'))
        <x-button type="link" title="Sign up" icon="person-plus" outline=true href="{{ url('/register') }}" />
        @endif
    </div></form>
</x-card>
</div>
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