@extends('layouts.blank')

@section('content')

<div class="PCRegist_sec">
    <div class="container d-flex align-items-center justify-content-center login-container h-100">
        <div class="PCRegistpage_wrap position-relative">
            <div class="watermark_img">
                <img src="{{asset("resources/img/happy-durga.png")}}" class="img-fluid" alt="image" />
            </div>
            <div class="PCRegist_form">
                <div class="PCRegist_header">
                    <div class="left">
                        <div class="logo">
                            <img src="{{asset("resources/img/logo-nkda.png")}}" alt="Logo">
                        </div>
                        <div class="logo_desc">
                            <h2>NEW TOWN KOLKATA DEVELOPMENT AUTHORITY</h2>
                            <p>Administrative Building. Plot No - DG/13, Premises No - 04-3333, Action Area - ID, New Town, Kolkata - 700156</p>
                        </div>
                    </div>
                    <div class="right">
                        <img src="{{asset("resources/img/durga-img.jpg")}}" class="img-fluid" alt="image" />
                    </div>
                </div>

                <div class="thankyou_page">
                    <div class="thankyou_icon"><i class="bi bi-check-circle-fill"></i></div>
                    <h2 class="head">Thank you</h2>
                    <p class="text-center mb-4" style="max-width: 700px; margin: 0 auto;">Thank you for registering for the NKDA Durga Puja Immersion Programme {{ date("Y") }}. Please use the link below to download your QR Code.</p>
                    <div class="d-flex align-items-center justify-content-center">
                        <a class="download_btn" href="{{ route('puja.gpass.pdf', ['token' => $puja->token]) }}">
                            <i class="bi bi-download"></i>
                            <span>QR Code</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="PCRegist_footer">
                <p class="text">Copyright © 2025 New Town Kolkata Development Authority</p>
            </div>
        </div>
    </div>
</div>

@endsection