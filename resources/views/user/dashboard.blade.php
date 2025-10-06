@extends($live ? 'layouts.blankwithheader' : 'layouts.app')
@php
use Carbon\Carbon;
@endphp
@push("styles")
<style>
    .statcard {
        color: #fff !important;
        border: none !important;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        height: 100%;
    }

    .statcard .card-body {
        padding: 4px 12px 10px;
        height: 100%;
    }

    .statcard.success {
        /* background: linear-gradient(135deg, #1e7e34, #28a745); */
        background: transparent;
        border-top: 5px solid #1e7e34 !important;
    }

    .statcard.danger {
        /* background: linear-gradient(135deg, #842029, #dc3545); */
        background: transparent;
        border-top: 5px solid #842029 !important;
    }

    .statcard.warning {
        /* background: linear-gradient(135deg, #997404, #ffc107); */
        background: transparent;
        border-top: 5px solid #997404 !important;
    }

    .statcard.primary {
        /* background: linear-gradient(135deg, #1d3c78, #0d6efd); */
        background: transparent;
        border-top: 5px solid #1d3c78 !important;
    }

    .statcard.secondary {
        /* background: linear-gradient(135deg, #5a287d, #6f42c1); */
        background: transparent;
        border-top: 5px solid #5a287d !important;
    }

    .statcard.info {
        /* background: linear-gradient(135deg, #17a2b8,rgb(166, 237, 248)); */
        background: transparent;
        border-top: 5px solid #17a2b8 !important;
    }

    .statcard .nm {
        font-size: 1rem;
        font-weight: 600;
        color: #212529;
        margin: 0px 0px 8px 0px;
        /* border-bottom: 1px solid rgb(249, 217, 217); */
    }

    .statcard .cnt {
        font-size: 2rem;
        font-weight: normal;
        color: #212529;
        margin: 0px;
    }

    .statcard .cntMulti {
        font-size: .8rem;
        color: #212529;
        font-weight: normal;
    }

    .statcard .cntMulti div {
        padding: 0px;
        margin: 0px;
        display: flex;
        align-items: center;
    }

    .statcard .cntMulti .live {
        padding: 0px;
        margin: 0px;
        font-size: 12px;
        font-weight: bold;
        color: #df8903;
    }

    .blinking {
        font-size: 8px;
        margin-right: 5px;
        animation: blink 1s ease-in-out infinite;
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 0;
        }

        50% {
            opacity: 1;
        }
    }

    .statcard .cntMulti .text {
        margin-right: 5px;
    }


    .statcard .cntMulti .done {
        padding: 0px;
        margin: 0px;
        font-size: 12px;
        font-weight: bold;
        color: green;
    }
    .statcard .cntMulti .done i {
        font-size: 12px;
        margin-right: 3px;
    }

    .statcard .card-icon {
        font-size: 24px;
        line-height: 0;
        width: 40px;
        height: 40px;
        flex-shrink: 0;
        flex-grow: 0;
        border-radius: 5px;
    }

    .statcard.success .card-icon {
        background: rgb(198, 253, 211);
        color: #1e7e34;
    }

    .statcard.danger .card-icon {
        background: rgb(243, 198, 202);
        color: #842029;
    }

    .statcard.warning .card-icon {
        background: rgb(238, 229, 202);
        color: #997404;
    }

    .statcard.primary .card-icon {
        background: rgb(204, 220, 250);
        color: #1d3c78;
    }

    .statcard.secondary .card-icon {
        background: rgb(238, 218, 252);
        color: #5a287d;
    }

    .statcard.info .card-icon {
        background: rgb(206, 248, 255);
        color: #17a2b8;
    }

    #last_pujas{
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        border-radius: 8px;
        border-top: 5px solid #997404 !important;
    }

    #last_pujas table {        
       
    }

    #last_pujas table thead th {
        background: rgb(56 139 69 / 50%);
	}
	
	#last_pujas .table tbody td {
        /* background: rgb(224 245 248 / 50%); */
        background: transparent;
	}

    #last_pujas .pujacommittee{
        font-size: 85%;
    }
</style>
@endpush


@php
[$start, $end] = getStEnDt();
@endphp


@section('content')
<div class="dashboard_sec">
    <div class="container-fluid m-0 p-4">
        <div class="h5 mb-1 p-2">
            <i class="bi bi-calendar-check"></i>
            <span id="today">{{ $start->format('d-M-Y') }}</span>
        </div>
        <div id="stats-cards" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-6">
            <div class="col mb-3">
                <div class="statcard card warning">
                    <div class="card-body">
                        <p class="nm">Queued</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 id="cnt1" class="cnt">0</h4>
                            <div class="card-icon d-flex align-items-center justify-content-center">
                                <i class="bi bi-person-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col mb-3">
                <div class="statcard card danger">
                    <div class="card-body">
                        <p class="nm">Immersion Completed</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 id="cnt2" class="cnt">0</h4>
                            <div class="card-icon d-flex align-items-center justify-content-center">
                                <i class="bi bi-card-checklist"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col mb-3">
                <div class="statcard card success">
                    <div class="card-body">
                        <p class="nm">Dhunuchi Count</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="cntMulti">
                                <div class="live">
                                    <i class="bi bi-circle-fill blinking"></i>
                                    <span class="text">Live :</span>
                                    <span id="cnt7">0</span>
                                </div>

                                <div class="done">
                                    <i class="bi bi-check2-circle"></i>
                                    <span class="text">Done :</span>
                                    <span id="cnt8">0</span>
                                </div>
                            </div>
                            <div class="card-icon d-flex align-items-center justify-content-center" @if(!$live) style="cursor:pointer;" onclick="showMemb();" @endif>
                                <i class="bi bi-card-checklist"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="col mb-3">
                <div class="statcard card success">
                    <div class="card-body">
                        <p class="nm">Immersion Completed</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 id="cnt3" class="cnt">0</h4>
                            <div class="card-icon d-flex align-items-center justify-content-center">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="col mb-3">
                <div class="statcard card primary">
                    <div class="card-body">
                        <p class="nm">Today's Total</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 id="cnt4" class="cnt">0</h4>
                            <div class="card-icon d-flex align-items-center justify-content-center">
                                <i class="bi bi-list-ol"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col mb-3">
                <div class="statcard card secondary">
                    <div class="card-body">
                        <p class="nm">Average Queue time</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 id="cnt5" class="cnt">0</h4>
                            <div class="card-icon d-flex align-items-center justify-content-center">
                                <i class="bi bi-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col mb-3">
                <div class="statcard card info">
                    <div class="card-body">
                        <p class="nm">All Total</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 id="cnt6" class="cnt">0</h4>
                            <div class="card-icon d-flex align-items-center justify-content-center">
                                <i class="bi bi-clipboard2-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(!$live)
<div class="modal fade" id="commModal2" tabindex="-1" aria-labelledby="commModal2Label" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-1">
                <h5 class="modal-title" id="commModal2Label">Dhunuchi Pending</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="pujasItems" class="pujas-items"></div>
            </div>
        </div>
    </div>
</div>
@endif


<div id="last_pujas_container" class="dashboard_sec">
    <div class="container-fluid m-0 p-4">
        <div class="h5 mb-1 p-2">
            <i class="bi bi-people"></i>
            <span id="today">Last 5 Pujas for Immersion</span>
        </div>
        <div id="last_pujas" class=""></div>
    </div>
</div>



@if(!$live && $datewiseCounts && $datewiseCounts->count() > 0)
<div class="dashboard_sec">
    <div class="container-fluid m-0 p-4">
        <div class="h5 mb-3 border-bottom p-2">
            <i class="bi bi-graph-up"></i>
            <span id="today">Registration Details</span>
        </div>
        <div id="stats-cards" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
            @php
            $cardColors = ["primary", "success", "info", "danger", "info", "warning"];
            @endphp
            @foreach($datewiseCounts as $sl=>$item)
            @php
            $colorClass = $cardColors[$sl % count($cardColors)];
            @endphp
            <div class="col mb-3">
                <div class="statcard card {{ $colorClass }}">
                    <div class="card-body">
                        <p class="nm">{{ Carbon::parse($item->proposed_immersion_date)->format('d-M-Y') }}</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 id="cnt1" class="cnt">{{ $item->total }}</h4>
                            <div class="card-icon d-flex align-items-center justify-content-center">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    @if(!$live)
	function showMemb() {
		webserv("GET", "{{ route('att.getcomm_bydt') }}", { typ: "3" }, function(resp) {
			let pujas = resp.data || [];
			let html = "";

			if (pujas.length > 0) {
				html += `
				<div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
					<table class="table table-bordered table-sm mb-0">
						<thead class="table-light">
							<tr>
								<th>Puja Committee</th>
								<th>#Dhunuchi Participants</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
				`;

				pujas.forEach(puja => {
					html += `
						<tr>
							<td>${puja.puja_committee_name ?? '-'}</td>
							<td>${puja.team_members ?? 0}</td>
							<td>
								<button class="btn btn-sm btn-success" onclick="markDone(${puja.id})">
									Done
								</button>
							</td>
						</tr>
					`;
				});

				html += `</tbody></table></div>`;
			} else {
				html = "<p class='text-muted'>No records found.</p>";
			}

			$("#pujasItems").html(html);
			$("#commModal2").modal("show");

		}, function(err) {
			toastr.error(err.msg || "Failed to fetch stats");
			$("#pujasItems").html("<p class='text-danger'>Error loading data</p>");
		});
	}
    function markDone(id) {
        webserv("POST", "{{ route('att.dhunuchi_done') }}", {
            id
        }, function(resp) {
            toastr.success(resp.msg || "Done");
            loadStats();
        });
    }
    @endif

    function loadStats() {
        webserv("GET", "{{ route('att.scanstat') }}", {}, function ok(resp) {
            if (!resp) return;
            if (resp.data) {
                $("#cnt1").html(resp.data[0]);
                $("#cnt2").html(resp.data[1]);
                //$("#cnt3").html(resp.data[2]);
                $("#cnt4").html(resp.data[3]);
                $("#cnt5").html(resp.data[4]);
                $("#cnt6").html(resp.data[5]);
                $("#cnt7").html(resp.data[7] - resp.data[8]);
                $("#cnt8").html(resp.data[8]);
                //$("#cnt8").html(resp.data[7]);
            }
            if (resp.dt) $("#today").html(resp.dt);

			let pujas = resp.last_pujas || [];
			let html = "";

			if (pujas.length > 0) {
				html += `
				<div class="">
					<table class="table table-bordered table-sm mb-0">						
						<tbody>
				`;

				pujas.forEach(puja => {
					html += `
						<tr>
							<td>
								<b>${puja.puja_committee_name ?? '-'}</b>
							</td>
                            <td>								
                                <div class="pujacommittee">${puja.puja_committee_address ?? ''}</div>
							</td>							
						</tr>
					`;
				});

				html += `</tbody></table></div>`;
				//$("#last_pujas_container").show();
			} else {
				//$("#last_pujas_container").hide();
				html = "<p class='text-muted'>-</p>";
			}

			$("#last_pujas").html(html);

        }, null, false);

    }
    $(function() {
        loadStats();
        setInterval(loadStats, 10000);
    });
</script>
@endpush