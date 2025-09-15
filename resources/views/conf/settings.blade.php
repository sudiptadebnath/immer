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
    <x-card title="Settings" icon="gear">
        <form id="settings" onsubmit="return settings_submt()" novalidate="novalidate">
        <div class="row g-2">
        <x-text name="NKDA_MOBS" title="* NKDA Mobile No(s)" required="true" :value="setting('NKDA_MOBS')" />
        {{-- <x-checkbox name="USER_SIGNUP" title="Show SignUp" :checked="(bool)setting('USER_SIGNUP','1') ? 'true' : 'false'" />
        <x-text name="ACTION_AREA" title="* NKDA Action Areas" required="true" :value="setting('ACTION_AREA')" />
        <x-text name="CATEGORY" title="* Puja Categories" required="true" :value="setting('CATEGORY')" />
        <x-textarea name="PUJA_COMMITTEE" title="* Puja Committees" required="true" :value="setting('PUJA_COMMITTEE')" />
        <x-text name="IMMERSION_DATE" title="* Immersion Dates (yyyy-mm-dd)" required="true" :value="setting('IMMERSION_DATE')" /> --}}
        <x-text name="DHUNUCHI_TEAM" title="Max Dhunuchi Team member" required="true" :value="setting('DHUNUCHI_TEAM')" />
        <div><span class="text-danger h5">(*)</span><span class="text-primary"> Values separated by <b>"~~"</b><span></div>
        <div class="col-md-12 text-end">
            <x-button size="" type="submit" icon="save" title="Save" />
            <x-button size="" type="reset" icon="arrow-counterclockwise" title="Reset" style="info" />
        </div>       
        </div>       
        </form>
    </x-card>
</div>
</div>

@php
    $opts_log = [
        "scrollY" => "500px"
    ];
    $data_log = [
        [ 'data'=>'ip', ], 
        [ 'data'=>'user', ], 
        [ 'data'=>'name', ], 
        [ 'data'=>'action', ], 
        [ 'data'=>'reaction', ], 
        [
            'data' => 'context',
            'render' => 'function(data, type, row, meta) {
                data = JSON.stringify(JSON.parse(data), null, 4);
                return "<pre class=\'pre-json\'>" + $("<div>").text(data).html() + "</pre>";
            }'
        ],
        [ 'data'=>'created_at', ], 
    ];
@endphp
<x-table name="table_log" title="Application Logs" :url="route('conf.data.logs')" :data="$data_log" :opts="$opts_log">
    <button class="btn btn-sm btn-danger" onclick="purge()">Purge</button>
</x-table>

@endsection

@push('scripts')
<script>
function purge() {

    var search = table_log.search();

    myAlert("Sure to delete all application log data ?","danger","Yes",function(){
        webserv("DELETE","{{ route('conf.del.logs') }}", { search }, 
        function ok(d) { 
            myAlert(d["msg"]);
            $('#table_log').DataTable().ajax.reload(null, false);
        }, function err(d) {
            myAlert(d["msg"],"danger");
        });
    },"No");
}
$(function () {
    $("#settings").validate({
        rules: {
            NKDA_MOBS: { required: true, minlength: 10 },
            ACTION_AREAS: { required: true, minlength: 1 },
            CATEGORY: { required: true, minlength: 3 },
            PUJA_COMMITTEE: { required: true, minlength: 5 },
            IMMERSION_DATE: {
                required: true,
                pattern: /^(\d{4}-\d{2}-\d{2})(~~\d{4}-\d{2}-\d{2})*$/
            },
            DHUNUCHI_TEAM: {
                required: true,
                digits: true,
                min: 1,
                max: 100
            }
        },
        messages: {
           IMMERSION_DATE: "Enter immersion dates in yyyy-mm-dd format separated by ~~",
           DHUNUCHI_TEAM: "Enter dhunuchi team member 1-100",
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
