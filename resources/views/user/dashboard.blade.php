@extends('layouts.app')

@push("styles")
<style>
    .statcard {
        color: #fff !important;
        border: none !important;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .statcard .card-body {
        padding: 10px 12px;
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
</style>
@endpush

@section('content')
<div class="dashboard_sec">
    <div class="container-fluid m-0 p-4">
        <div id="today" class="h4 mb-3 border-bottom p-3">25-sep-2026</div>
        <div id="stats-cards" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5">
            <div class="col mb-3">
                <div class="statcard card primary">
                    <div class="card-body">
                        <p class="nm">Queued</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 id="cnt1" class="cnt">0</h4>
                            <div class="card-icon d-flex align-items-center justify-content-center">
                                <i class="bi bi-clipboard2-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col mb-3">
                <div class="statcard card warning">
                    <div class="card-body">
                        <p class="nm">Reported</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 id="cnt2" class="cnt">0</h4>
                            <div class="card-icon d-flex align-items-center justify-content-center">
                                <i class="bi bi-clipboard2-check"></i>
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
                            <h4 id="cnt3" class="cnt">0</h4>
                            <div class="card-icon d-flex align-items-center justify-content-center">
                                <i class="bi bi-clipboard2-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col mb-3">
                <div class="statcard card secondary">
                    <div class="card-body">
                        <p class="nm">Today's Total</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 id="cnt4" class="cnt">0</h4>
                            <div class="card-icon d-flex align-items-center justify-content-center">
                                <i class="bi bi-clipboard2-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col mb-3">
                <div class="statcard card primary">
                    <div class="card-body">
                        <p class="nm">All Total</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 id="cnt5" class="cnt">0</h4>
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
@endsection

@push('scripts')
<script>
    function loadStats() {
        webserv("GET", "{{ route('att.scanstat') }}", {}, function ok(resp) {
            if (!resp) return;
            if (resp.data) {
                $("#cnt1").html(resp.data[0]);
                $("#cnt2").html(resp.data[1]);
                $("#cnt3").html(resp.data[2]);
                $("#cnt4").html(resp.data[3]);
                $("#cnt5").html(resp.data[4]);
            }
            if (resp.dt) $("#today").html(resp.dt);

        }, function fail() {});

    }
    $(function() {
        loadStats();
        setInterval(loadStats, 10000);
    });
</script>
@endpush