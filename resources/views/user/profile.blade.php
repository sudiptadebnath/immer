@extends('layouts.app')

@push("styles")
<style>
</style>
@endpush

@section('content')
<div class="container row gap-2 m-0 p-2">
<form id="register" onsubmit="return __register_submt()" novalidate="novalidate">
    <x-register_compo :hiderole="true" :hidestat="true" />
    <div class="text-end">
        <x-button size="" type="submit" title="Save" icon="send" />
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
function loadVals() {

    let id = "{{ getUsrProp('id') }}"; 

    webserv("GET", "{{ url('user/users') }}/" + id, {}, function (d) {
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

        // reset password fields
        $('#password').val('');
        $('#password2').val('');

        // set modal header and open modal
        $('#userModalLabel').text('Edit User');
        $('#userModal').modal('show');
    });

}
$(function() {
    loadVals();
});
</script>
@endpush
