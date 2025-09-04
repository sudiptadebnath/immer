@extends('layouts.app')
@section('content')
<div class="container text-center">
    <h3>QR Code :: {{ $puja->secretary_name }} ({{ $puja->secretary_mobile }})</h3>
    <img src="{{ asset('/qrs/'.$puja->id.'.png') }}" alt="QR Code">
    <div><a class="btn btn-danger" href="{{ route('puja.gpass.pdf', $puja->id) }}">
        <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
    </a></div>
</div>
@endsection