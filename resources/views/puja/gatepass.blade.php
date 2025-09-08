@extends('layouts.blank')
{{-- @extends('layouts.app') --}}

@section('content')
<div class="container col-12 col-md-8 col-lg-6 m-3 p-3 border rounded-5 shadow">
<x-gatepass :file="asset('/qrs/'.$puja->id.'.png')" :puja="$puja" />
<div class="text-center mt-3"><a class="btn btn-danger" href="{{ route('puja.gpass.pdf', $puja->id) }}">
	<i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
</a></div>
</div>
@endsection