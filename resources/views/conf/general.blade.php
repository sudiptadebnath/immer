@extends('layouts.app')

@push("styles")
<style>
.pre-json {
    max-width: 400px;
    white-space: pre-wrap;
    word-wrap: break-word;
    overflow: auto;
}    
</style>
@endpush

@section('content')
<div class="container-fluid m-0 p-3">
<div class="row g-2">
    <x-card title="General" icon="gear">
        <form id="settings" onsubmit="return settings_submt()" novalidate="novalidate">
        <div class="row g-2">
        <x-text name="NKDA_MOBS" title="NKDA Mobile No(s)" required="true" :value="setting('NKDA_MOBS')" />
		<span class="text-danger">Separated by comma (,)</span>
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
$(function () {
    $("#settings").validate({
        rules: {
            NKDA_MOBS: { required: true, minlength: 10 },
        }
    });
});
function settings_submt(e) {
    if($("#settings").valid()) {
        webserv("POST","{{ route('conf.save_settings') }}", "settings", 
        function ok(d) { 
            myAlert(d["msg"]);
        }, function err(d) {
            myAlert(d["msg"],"danger");
        });
    }
    return false;
}

</script>
@endpush
