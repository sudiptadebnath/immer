@extends('layouts.app')

@section('content')
<div class="container d-flex align-items-center justify-content-center login-container h-100">
<div class="col-md-6 col-lg-4">
<x-card icon="person-plus" title="Sign up">
    <form id="register" onsubmit="return register_submt()" novalidate="novalidate">
    <div class="row gy-2">
        <x-text name="name" icon="person" title="Name" required=true />
        <x-text name="mail" icon="envelope" title="Mail" required=true />
        <x-text name="mob" icon="phone" title="Mobile" required=true />
        <x-password name="password" title="Password" required=true />
        <x-password name="password2" title="Repeat Password" required=true />
        <x-button type="submit" title="Sign up" icon="person-plus" />
        <div class="col-md-12">
            Already Have Account ? <a href="{{ url('/') }}">Log in</a>
        </div>
    </div></form>
</x-card>
</div>
</div>
    
@endsection

@section('scripts')
<script>
$(document).ready(function ($) {
    $("#register").validate({
        rules: {
            name: {
                required: true,
                minlength: 3,
                maxlength: 150
            },
            mail: {
                required: true,
                email: true
            },
            mob: {
                required: true,
                minlength: 8,
                maxlength: 20,
                digits: true
            },
            password: {
                required: true,
                minlength: 6
            },
            password2: {
                required: true,
                equalTo: "#password"
            }
        },
        messages: {
            name: {
                required: "Name is required",
                minlength: "Name must be at least 3 characters",
                maxlength: "Name cannot exceed 150 characters"
            },
            mail: {
                required: "Mail is required",
                email: "Enter a valid mail address"
            },
            mob: {
                required: "Mobile number is required",
                minlength: "Mobile must be at least 8 digits",
                maxlength: "Mobile cannot exceed 20 digits",
                digits: "Only numbers are allowed"
            },
            password: {
                required: "Password is required",
                minlength: "Password must be at least 6 characters"
            },
            password2: {
                required: "Please confirm your password",
                equalTo: "Passwords do not match"
            }
        }
    });
});

function register_submt() {
    if($("#register").valid()) {
        webserv("POST","{{ url('/register') }}", "register", 
        function ok(d) { goLnk("{{ url('/user/dashboard') }}"); });
    }
    return false;
}
</script>
@endsection
