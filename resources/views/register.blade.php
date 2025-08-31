@extends('layouts.app')

@section('content')
<div class="container d-flex align-items-center justify-content-center login-container h-100">
<div class="col-md-10 col-lg-8 p-2">
<x-card icon="person-plus" title="Puja Committee Registration">
    <x-register_compo cb="register_submt" :btns="true" />
</x-card>
</div>
</div>
@endsection

@section('scripts')
<script>
function register_submt() {
    webserv("POST","{{ url('/register') }}","register",
    function ok(d){ goLnk("{{ url('/user/dashboard') }}"); });
}
</script>
@endsection
