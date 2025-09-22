@extends('layouts.app')

@section('content')
<div class="container-fluid row gap-2 m-0 p-2">
    <form id="profile" onsubmit="return profile_submt(event)" novalidate="novalidate">
    <div class="row g-2">
        <input type="hidden" name="id" id="id" value="{{ getUsrProp('id') }}" />
        <x-text name="email" icon="envelope" title="Email" required="true" />
        <x-text name="name" icon="person" title="Name" required="true" />
        <x-number name="phone" icon="telephone" title="Phone" />
        <x-password size="6" name="password" title="Password" required="true" />
        <x-password size="6" name="password2" title="Repeat Password" required="true" />
        <x-button type="submit" title="Save" icon="send" />
    </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    $("#profile").validate({
        rules: {
            email: {
                required: true,
                email: true
            },
            name: {
                required: true,
                minlength: 2
            },
            phone: {
                required: false,
                indianMobile: true
            },
            password: {
                required: false,
                strongPassword: true
            },
            password2: {
                required: false,
                equalTo: "#password"
            }
        },
        messages: {
            email: {
                required: "Please enter your email address",
                email: "Please enter a valid email address"
            },
            name: {
                required: "Please enter your name",
                minlength: "Name must be at least 2 characters long"
            },
            phone: {
                indianMobile: "Please enter a valid 10-digit Indian mobile number"
            },
            password: {
                strongPassword: "Password must be at least 6 characters long and include 1 letter, 1 number, and 1 special character"
            },
            password2: {
                equalTo: "Passwords do not match"
            }
        }
    });

    loadVals();
});



function profile_submt(e) {
    e.preventDefault(); // stop default form submission
    if($("#profile").valid()) {
        const id = $('#id').val();
        webserv("PUT", "{{ route('user.update', ['id' => '__id__']) }}".replace('__id__', id), "profile", function ok(d) {
            myAlert(d["msg"],"success","Ok",function() {
                loadVals();
            });
        });
    }
    return false;
}

function loadVals() {
    const id = $('#id').val();
    webserv("GET", "{{ route('user.get', ['id' => '__id__']) }}".replace('__id__', id), {}, function (d) {
        let user = d["data"];
        $('#id').val(user.id);
        $('#name').val(user.name);
        $('#email').val(user.email);
        $('#phone').val(user.phone);
        $('#password').val('');
        $('#password2').val('');
        $('.error').text('');
    });
}
</script>
@endpush
