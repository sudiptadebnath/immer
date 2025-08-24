@extends('layouts.app')

@push("styles")
<style>
.statcard {
    color: #fff !important;
    border: none !important;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
.statcard.success {
    background: linear-gradient(135deg, #1e7e34, #28a745);
}
.statcard.danger {
    background: linear-gradient(135deg, #842029, #dc3545);
}
.statcard.warning {
    background: linear-gradient(135deg, #997404, #ffc107);
    color: #212529 !important; /* darker text for contrast */
}
.statcard.primary {
    background: linear-gradient(135deg, #1d3c78, #0d6efd);
}
.statcard.secondary {
    background: linear-gradient(135deg, #5a287d, #6f42c1);
}
.statcard .nm {
    font-size: 1rem;
    color: #c0bfbf;
    margin: 0px;
    border-bottom: 1px solid rgb(249, 217, 217);
}
.statcard .cnt {
    font-size: 3rem;
    font-weight: normal; 
    color:white;
    margin:0px;
}
</style>
@endpush

@section('content')
<div class="container mt-4">
    <h4 class="d-flex mb-4">
        <span>Gate Status : &nbsp;</span>
        <x-select size="4" icon="door-closed" name="post" title="Gate" :value="postDict()" sel="1" />
    </h4>

    <div id="stats-cards" class="row g-3"></div>

</div>
@endsection

@push('scripts')
<script>

function loadStats() {
    let gateId = $("#post").val();
    if (!gateId) return;
    webserv("GET", "{{ route('user.scanstat') }}", { gate_id: gateId }, function ok(resp) {
        let html = "";
        if (resp && resp.data) {
            resp.data.forEach(stat => {
                html += `
                    <div class="col-md-3 col-xs-4">
                        <div class="statcard card ${stat.color}">
                            <div class="card-body">
                                <p class="nm">${stat.name}</p>
                                <h4 class="cnt">${stat.count}</h4>
                            </div>
                        </div>
                    </div>
                `;
            });
        }
        $("#stats-cards").html(html);
    }, function fail() {});

}    
$(function () {
    loadStats();
    $('#post').on('change', loadStats);
    //setInterval(loadStats, 10000);
});
</script>
@endpush
