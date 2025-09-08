@extends('layouts.app')

@php
	$immer_dts = dbVals("puja_immersion_dates",["idate","name"],"idate","asc");
@endphp

@section('styles')
<style>
</style>
@endsection
@section('content')

<div class="container-fluid m-0 p-3">
    <h3 class="d-flex flex-wrap gap-1 border-1 border-bottom pb-2">
        Immersion Status: 
        <x-select size="3" icon="calendar-date" name="immersion_date" title="Date"
	    :value="$immer_dts" required="true" />	
    </h3>
    <div class="row gap-3 m-0">
        <div class="statcard card primary col-12 col-md-6">
            <div class="card-body">
                <p class="nm">Registered</p>
                <div class="d-flex align-items-center justify-content-between">
                    <h4 id="cnt1" class="cnt">-</h4>
                    <button class="btn btn-sm btn-info" onclick="showRecsFor('0')">
                        <i class="bi bi-clipboard2-check"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="statcard card primary col-12 col-md-6">
            <div class="card-body">
                <p class="nm">Immeresed</p>
                <div class="d-flex align-items-center justify-content-between">
                    <h4 id="cnt2" class="cnt">-</h4>
                    <button class="btn btn-sm btn-info" onclick="showRecsFor('1')">
                        <i class="bi bi-clipboard2-check"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>    
</div>

<div class="modal fade" id="commModal1" tabindex="-1" aria-labelledby="commModal1Label" aria-hidden="true"
data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white py-1">
          <h5 class="modal-title" id="commModal1Label">Registered Committies</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

@php
    $opts1 = [
        "ajaxdata"=>"getajaxdata1",
        "plain"=>true,
    ];

    $tbldata1 = [
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
        [ 'data'=>'proposed_immersion_date',"th"=>"Immersion Date", 'render' => 'function (data, type, row) {
            let dt = row.proposed_immersion_date ? row.proposed_immersion_date : "";
            let tm = row.proposed_immersion_time ? row.proposed_immersion_time : "";
            if (dt && tm) return dt + "<br>" + tm;
            else if (dt) return dt;
            else if (tm) return tm;
            return "";
        }', ], 
        [ 'data'=>'proposed_immersion_time','visible'=>false ], 
    ];
@endphp

<x-table name="commTable1" title="Pujas" :url="route('att.getcomm_bydt')" :data=$tbldata1 :opts=$opts1 />

        </div>
      </div>
  </div>
</div>


<div class="modal fade" id="commModal2" tabindex="-1" aria-labelledby="commModal2Label" aria-hidden="true"
data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white py-1">
          <h5 class="modal-title" id="commModal2Label">Committies Done Immeresion</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

@php
    $opts2 = [
        "ajaxdata"=>"getajaxdata2",
        "plain"=>true,
    ];

    $tbldata2 = [
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
        [ 'data'=>'proposed_immersion_date',"th"=>"Immersion Date", 'render' => 'function (data, type, row) {
            let dt = row.proposed_immersion_date ? row.proposed_immersion_date : "";
            let tm = row.proposed_immersion_time ? row.proposed_immersion_time : "";
            if (dt && tm) return dt + "<br>" + tm;
            else if (dt) return dt;
            else if (tm) return tm;
            return "";
        }', ], 
        [ 'data'=>'proposed_immersion_time','visible'=>false ], 
    ];
@endphp

<x-table name="commTable2" title="Pujas" :url="route('att.getcomm_bydt')" :data=$tbldata2 :opts=$opts2 />

        </div>
      </div>
  </div>
</div>




@endsection

@section('scripts')

<script>

function getajaxdata1(d){
    d.dt = $("#immersion_date").val();
    d.typ = "0";
}

function getajaxdata2(d){
    d.dt = $("#immersion_date").val();
    d.typ = "1";
}

function showRecsFor(t) {
    if(t=="0") {
        $('#commTable1').DataTable().ajax.reload();
        $('#commModal1').modal('show');
    }
    else if(t=="1") {
        $('#commTable2').DataTable().ajax.reload();
        $('#commModal2').modal('show');
    }
}

$(function () {
    $('#commModal1').on('shown.bs.modal', function () {
        $('#commTable1').DataTable().columns.adjust().responsive.recalc();
    });
    $('#commModal2').on('shown.bs.modal', function () {
        $('#commTable2').DataTable().columns.adjust().responsive.recalc();
    });

    $("#immersion_date").on("change", function () {
        let dt = $(this).val();
        if (!dt) return;
        webserv("GET", "{{ route('att.scanstat_bydt') }}", { date: dt }, function (resp) {
            $("#cnt1").text(resp.registered ?? 0);
            $("#cnt2").text(resp.immersed ?? 0);
        }, function (err) {
            toastr.error(err.msg || "Failed to fetch stats");
            $("#cnt1, #cnt2").text("-");
        });
    });

});
</script>
@endsection
