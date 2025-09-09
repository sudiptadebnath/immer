@extends('layouts.blank')

@section('content')
Thank you for registering for the NKDA Durga Puja Immersion Programme {{ date("Y") }}. Please use the link below to download your QR Code
<br>
<a href="{{ route('puja.gpass.pdf', ['id' => $puja->id]) }}">QR Code</a>
@endsection