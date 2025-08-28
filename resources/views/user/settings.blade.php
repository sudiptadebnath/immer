@extends('layouts.app')

@section('content')
<div class="container m-3">
<div class="row g-2">
    <x-card title="Settings" icon="gear">
        <form id="settings" onsubmit="return settings_submt()" novalidate="novalidate">
        <div class="row g-2">
        <x-checkbox name="USER_SIGNUP" title="Show SignUp" :checked="(bool)setting('USER_SIGNUP','1') ? 'true' : 'false'" size="6" />
        <div class="col-md-12 text-end">
            <x-button size="" type="submit" icon="save" title="Save" />
            <x-button size="" type="reset" icon="arrow-counterclockwise" title="Reset" style="info" />
        </div>       
        </div>       
        </form>
    </x-card>
</div>
</div>
@endsection

@push('scripts')
<script>
function settings_submt() {
    if($("#settings").valid()) {
        webserv("POST","{{ route('user.save_settings') }}", "settings", 
        function ok(d) { 
            myAlert(d["msg"]);
        }, function ok(d) {
            myAlert(d["msg"],"danger");
        });
    }
    return false;
}

</script>
@endpush
