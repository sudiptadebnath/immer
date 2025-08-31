@extends('layouts.app')
@section('content')
<div class="container text-center">
    <h3>QR Code :: {{ $user->secretary_name }} ({{ $user->secretary_mobile }})</h3>
    <img src="{{ asset('public/qrs/'.$user->id.'.png') }}" alt="QR Code">
    <div><a class="btn btn-danger" href="{{ route('user.gpass.pdf', $user->id) }}">
        <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
    </a></div>
</div>
@endsection