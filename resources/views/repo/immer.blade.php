@extends('layouts.app')

@section('styles')
<style>
</style>
@endsection
@section('content')

@php
	$immer_dts = dbVals("puja_immersion_dates",["idate","name"],"idate","asc");
    $opts = [
        "imp"=>[0,2,3,5,7,9],
		"ajaxdata"=>"getajaxdata",
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
        [ 'data'=>'proposed_immersion_time','visible'=>false ], 
        [ 'data'=>'team_members' ], 		
        [ 'data'=>'action_area',"th"=>"Action Area", 'render' => 'function (data, type, row) {
            let aa = row.action_area ? row.action_area : "";
            let cat = row.category ? row.category : "";
            if (aa && cat) return "Action Area - "+ aa + "<br>Category - " + cat ;
            else if (aa) return "Action Area - "+ aa;
            else if (cat) return "Category - " + cat;
            return "";
        }', ], 
        [ 'data'=>'category','visible'=>false ], 
        [ 'data'=>'puja_committee_name',"th"=>"Puja Committee", 'render' => 'function (data, type, row) {
            let name = row.puja_committee_name ? row.puja_committee_name : "";
            let add = row.puja_committee_address ? row.puja_committee_address : "";
            if (name && add) return name + "<hr><b>Address - </b>" + add ;
            else if (name) return name;
            else if (add) return "<b>Address - </b>"+add;
            return "";
        }', ], 
        [ 'data'=>'puja_committee_address','visible'=>false ], 
        [ 'data'=>'secretary_name',"th"=>"Secretary", 'render' => 'function (data, type, row) {
            let name = row.secretary_name ? row.secretary_name : "";
            let mobile = row.secretary_mobile ? row.secretary_mobile : "";
            if (name && mobile) return name + " (" + mobile + ")";
            else if (name) return name;
            else if (mobile) return mobile;
            return "";
        }', ], 
        [ 'data'=>'secretary_mobile','visible'=>false ], 
        [ 'data'=>'chairman_name',"th"=>"Chairman", 'render' => 'function (data, type, row) {
            let name = row.chairman_name ? row.chairman_name : "";
            let mobile = row.chairman_mobile ? row.chairman_mobile : "";
            if (name && mobile) return name + " (" + mobile + ")";
            else if (name) return name;
            else if (mobile) return mobile;
            return "";
        }', ], 
        [ 'data'=>'chairman_mobile','visible'=>false ], 
		[ 'data'=>'attendance',"th"=>"Status",'orderData'=>[12],'searchable' => false, 'render' => 'function (data, type, row) {
			if (!data || data.length === 0) 
				return "<span class=\'text-muted\'>No Records</span>";

			let html = "<ul class=\'list-unstyled mb-0\'>";
			data.forEach(function(item, i) {
				let badgeClass = (item.typ === "Reported" ? "success" : "primary"); 
				let badgeText = (item.typ === "Reported" ? "Immersion Done" : "Queued"); 
				// highlight first (latest) record
				let latest = (i === 0) ? "text-sm" : "text-muted";
				html += "<li class=\'" + latest + "\'>" +
						  "<div class=\'badge bg-" + badgeClass + " me-1\'>" + badgeText + "</div>" +
						  "<div style=\'font-size:10px;\'>" + item.time + "</div>" +
						"</li>";
			});
			html += "</ul>";
			return html;
		}','visible'=>true, ],
		[ 'data'=>'latest_attendance_typ', 'visible'=>false ],
    ];
@endphp
<div class="container-fluid m-0 p-2">

<x-table name="pujaTable" title="Immersion By Date" :url="route('repo.immerdata')" :data=$tbldata :opts=$opts>
	<x-select size="2 col-12" icon="calendar-date" name="immersion_date" title="Date"
	 :value="$immer_dts" required="true" />	
    <x-select size="2 col-12" icon="geo" name="is_newtown" title="Area"
	 :value="['nt'=>'Newtown','ont'=>'Outside Newtown']" />	
	<x-select size="2 col-12" icon="list" name="immersion_stat" title="Immersion Status"
	 :value="['queue'=>'Queued','in'=>'Immersion Done','natt'=>'Not Attended','ndone'=>'Immersion Not Done - Total']" required="true" />	
	<x-select size="2 col-12" icon="info-circle" name="dhunuchi_stat" title="Dhunuchi"
	 :value="['1'=>'Dhunuchi','0'=>'No Dhunuchi']" required="true" />	
</x-table>

</div>

@endsection

@section('scripts')
<script>
function getajaxdata(d) {
	d.dt = $("#immersion_date").val();
	d.typ = $("#is_newtown").val();
	d.istat = $("#immersion_stat").val();
	d.dstat = $("#dhunuchi_stat").val();
	//console.log(d.dt);
}
$(document).ready(function ($) {
	$('#immersion_date, #is_newtown, #immersion_stat, #dhunuchi_stat').change(function () {
		$("#pujaTable").DataTable().ajax.reload();
	});
});
</script>
@endsection
