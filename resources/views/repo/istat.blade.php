@extends('layouts.app')

@php
	$immer_dts = dbVals("puja_immersion_dates",["idate","name"],"idate","asc");
@endphp

@section('styles')
<style>
.pujas-items {
  max-height: 40vh;
  overflow: auto;
  padding-right: 8px;
  font-size: 0.95rem;
  line-height: 1.45;
  word-wrap: break-word;
}
</style>
@endsection
@section('content')

<div class="container-fluid m-0 p-3">
    <h3 class="d-flex flex-wrap gap-1 border-1 border-bottom pb-2">
        Immersion Status: 
        <x-select size="3" icon="calendar-date" name="immersion_date" title="Date"
	    :value="$immer_dts" required="true" />	
    </h3>
    <div class="d-flex flex-wrap gap-3 mb-3">
        <div class="statcard card primary flex-fill">
            <div class="card-body">
                <p class="nm">Registered</p>
                <div class="d-flex align-items-center justify-content-between">
                    <h4 id="cnt1" class="cnt">-</h4>
                    <button class="btn btn-sm btn-info" onclick="showRecs(1)">
                        <i class="bi bi-clipboard2-check"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="statcard card primary flex-fill">
            <div class="card-body">
                <p class="nm">Immeresed</p>
                <div class="d-flex align-items-center justify-content-between">
                    <h4 id="cnt2" class="cnt">-</h4>
                    <button class="btn btn-sm btn-info" onclick="showRecs(2)">
                        <i class="bi bi-clipboard2-check"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>    
</div>

<div class="modal fade" id="commModal2" tabindex="-1" aria-labelledby="commModal2Label" aria-hidden="true"
data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white py-1">
          <h5 class="modal-title" id="commModal2Label">Pujas</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <div id="pujasItems" class="pujas-items"></div>
        </div>
      </div>
  </div>
</div>
@endsection

@section('scripts')

<script>
function showRecs(typ) {
    let dt = $("#immersion_date").val();
    if (!dt) return;

    webserv("GET", "{{ route('att.getcomm_bydt') }}", { date: dt, typ }, function (resp) {
        $("#cnt1").text(resp.registered ?? 0);
        $("#cnt2").text(resp.immersed ?? 0);

        let pujas = resp.data || [];
        let html = "";

        pujas.forEach(puja => {
            html += `
                <div class="mb-3">
                    <strong>Name:</strong> ${puja.puja_committee_name ?? '-'}<br>
                    ${(puja.action_area || puja.category) ? `<strong>Location:</strong> ${puja.action_area ?? ''}${puja.category ? ', ' + puja.category : ''}<br>` : ''}
                    ${puja.puja_committee_address ? `<strong>Address:</strong> ${puja.puja_committee_address}<br>` : ''}
                    <strong>Secretary:</strong> ${puja.secretary_name ?? ''} (${puja.secretary_mobile ?? '-'})<br>
                    <strong>Chairman:</strong> ${puja.chairman_name ?? ''} (${puja.chairman_mobile ?? '-'})<br>
                    <strong>Proposed Immersion:</strong> ${puja.proposed_immersion_date ?? ''} ${puja.proposed_immersion_time ?? ''}<br>
                    ${puja.immersion_time ? `<strong>Reported At:</strong> ${puja.immersion_time}<br>` : ''}
                    ${puja.vehicle_no ? `<strong>Vehicle No:</strong> ${puja.vehicle_no}<br>` : ''}
                </div>
                <hr>
            `;
        });

        $("#pujasItems").html(html || "<p class='text-muted'>No records found.</p>");
        $("#commModal2Label").text((typ==1 ? "Registered" : "Immersed")+" Pujas");
        $("#commModal2").modal("show");
    }, function (err) {
        toastr.error(err.msg || "Failed to fetch stats");
        $("#cnt1, #cnt2").text("-");
        $("#pujasItems").html("<p class='text-danger'>Error loading data</p>");
    });
}


$(function () {
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
