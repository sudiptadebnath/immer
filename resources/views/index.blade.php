@extends('layouts.app')

@section('content')
<div class="container d-flex align-items-center justify-content-center login-container h-100">
<div class="col-md-6 col-lg-4">
<x-card icon="box-arrow-in-right" title="Log in">
    <form id="signin" onsubmit="return signin_submt()" novalidate="novalidate">
    <div class="row gy-2">
        <x-text name="email" icon="person" title="Enter Email" required=true />
        <x-password name="password" icon="key" title="Password" required=true />
        <x-button type="submit" title="Log in" icon="box-arrow-in-right" />
    </div></form>
</x-card>
</div>
</div>
@endsection

@section('scripts')
<script> 

$(document).ready(function ($) {
    $("#signin").validate({
      rules: {
        email: {
          required: true,
          email: true,
        },
        password: {
          required: true,
          //strongPassword: true,
          minlength: 2,
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