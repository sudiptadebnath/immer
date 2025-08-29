@extends('layouts.app')

@section('content')
<div class="container d-flex align-items-center justify-content-center login-container h-100">
<div class="col-md-6 col-lg-4">
<x-card icon="box-arrow-in-right" title="Log in">
    <form id="signin" onsubmit="return signin_submt()" novalidate="novalidate">
    <div class="row gy-2">
        <x-text name="mob" icon="person" title="Mobile No" required=true />
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

$.validator.addMethod("indianMobile", function(value, element) {
  return this.optional(element) || /^[6-9]\d{9}$/.test(value);
}, "Enter a valid mobile number");

$(document).ready(function ($) {
    $("#signin").validate({
      rules: {
        mob: {
          required: true,
          indianMobile: true,
        },
        password: {
          required: true,
          minlength: 4
        }
      },
      messages: {
        mob: {
          required: "Please enter registered mobile no",
          indianMobile: "Enter a valid mobile no"
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