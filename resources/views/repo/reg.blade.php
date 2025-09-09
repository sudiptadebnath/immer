@extends('layouts.app')

@section('styles')
<style>
</style>
@endsection
@section('content')

@php
    $opts = [
        //"imp"=>[0,1,2,3,4,5,6,7,8,9,10,11,12,13],
    ];

    $tbldata = [
        [ 'data'=>'proposed_immersion_date',"th"=>"Immersion Date", 'render' => 'function (data, type, row) {
            let dt = row.proposed_immersion_date ? row.proposed_immersion_date : "";
            let tm = row.proposed_immersion_time ? row.proposed_immersion_time : "";
            if (dt && tm) return dt + "<br>" + tm;
            else if (dt) return dt;
            else if (tm) return tm;
            return "";
        }', ], 
        [ 'data'=>'action_area',"th"=>"Action Area", 'render' => 'function (data, type, row) {
            let aa = row.action_area ? row.action_area : "";
            let cat = row.category ? row.category : "";
            if (aa && cat) return "Action Area - "+ aa + "<br>Category - " + cat ;
            else if (aa) return "Action Area - "+ aa;
            else if (cat) return "Category - " + cat;
            return "";
        }', ], 
        [ 'data'=>'puja_committee_name',"th"=>"Puja Committee", 'render' => 'function (data, type, row) {
            let name = row.puja_committee_name ? row.puja_committee_name : "";
            let add = row.puja_committee_address ? row.puja_committee_address : "";
            if (name && add) return name + "<hr><b>Address - </b>" + add ;
            else if (name) return name;
            else if (add) return "<b>Address - </b>"+add;
            return "";
        }', ], 
        [ 'data'=>'secretary_name',"th"=>"Secretary", 'render' => 'function (data, type, row) {
            let name = row.secretary_name ? row.secretary_name : "";
            let mobile = row.secretary_mobile ? row.secretary_mobile : "";
            if (name && mobile) return name + " (" + mobile + ")";
            else if (name) return name;
            else if (mobile) return mobile;
            return "";
        }', ], 
        [ 'data'=>'chairman_name',"th"=>"Chairman", 'render' => 'function (data, type, row) {
            let name = row.chairman_name ? row.chairman_name : "";
            let mobile = row.chairman_mobile ? row.chairman_mobile : "";
            if (name && mobile) return name + " (" + mobile + ")";
            else if (name) return name;
            else if (mobile) return mobile;
            return "";
        }', ], 
    ];
@endphp
<div class="container-fluid m-0 p-2">

<x-table name="pujaTable" title="Registrations" :url="route('repo.regsdata')" :data=$tbldata :opts=$opts />

</div>

@endsection

@section('scripts')

<script>
$(function () {


});
</script>
@endsection
