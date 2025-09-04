<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ env('APP_TITLE', 'pms') }}</title>
	<link rel="icon" type="image/x-icon" href={{ asset("resources/img/favicon.ico") }}>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

	<!-- Tempus Dominus Styles -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.9.4/dist/css/tempus-dominus.min.css">

	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	
	<link rel="stylesheet" href={{ asset("resources/css/style.css") }}>
    @yield('styles') 
    @stack('styles') 
</head>
	
<body onload="myLoad();">

	<!-- ===================================================================================== -->
	<!-- ============================ LOADER AND ALERT POPUP ================================= -->
	<!-- ===================================================================================== -->
	<div id="loader" class="d-none">
	  <div class="spinner"></div>
	</div>

	<div id="toastBackdrop" class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-25 d-none"></div>
	<div id="myToast" 
	class="toast position-fixed top-50 start-50 translate-middle border-0 shadow-lg rounded-3"
	role="alert" style="z-index: 9999;" data-bs-autohide="false">
		<div class="d-flex align-items-start p-3 bg-light border-start border-4 rounded-3">
			<i id="toastIcon" class="bi me-3 big-toast-icon"></i>
			<div class="flex-grow-1">
			<div class="toast-body"></div>
			<div class="w-100 text-center" id="toastButtons"></div>
			</div>
			<button type="button" class="btn-close ms-2" data-bs-dismiss="toast"></button>
		</div>
	</div>

	
    @include('layouts.partials.topbar')

	<!-- ============================ MAIN LAYOUT ==================================== -->
	<div class="d-flex flex-grow-1">
	
@if (userLogged())
	<nav class="sidebar-wrapper overflow-auto h-100 bg-light border-end p-3 d-none d-sm-block">
		@include('layouts.partials.menu')
	</nav>

	<div class="offcanvas offcanvas-start bg-light" tabindex="-1"
	id="sidebarOffcanvas" data-bs-backdrop="true" aria-labelledby="sidebarLabel">
		<div class="offcanvas-header">
		  <h5 class="offcanvas-title" id="sidebarLabel">{{ env('APP_TITLE', 'pms') }}</h5>
		  <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
		</div>
		<div class="sidebar-wrapper offcanvas-body p-3">
		    @include('layouts.partials.menu')
		</div>
	</div>
@endif

	  <div class="flex-grow-1 d-flex flex-column">
		<main class="flex-grow-1 p-0 bg-white">
		  @yield('content')
		</main>
		@include('layouts.partials.footer')
	  </div>
	</div>

	<!-- ===================================================================================== -->
	<!-- =================================== SCRIPTS ========================================= -->
	<!-- ===================================================================================== -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

	<script> let appsettings = { dtfmt: @json(dtfmt(1)), dttmfmt: @json(dttmfmt(1)) } </script>
	
	<!-- DataTables JS + Bootstrap 5 styling -->
	<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
	<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

	<!-- Responsive extension CSS -->
	<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

	<!-- Responsive extension JS -->
	<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
	<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

	<!-- ROW Order JS -->
	<script src="https://cdn.datatables.net/rowreorder/1.5.0/js/dataTables.rowReorder.js"></script>
	<script src="https://cdn.datatables.net/rowreorder/1.5.0/js/rowReorder.dataTables.js"></script>
	<link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.5.0/css/rowReorder.dataTables.css">

	<!-- DataTables Buttons -->
	<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>

	<!-- File export functionality -->
	<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

	<!-- Dependencies for Excel and PDF -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
	<script src="https://cdn.datatables.net/plug-ins/1.13.6/sorting/datetime-moment.js"></script>

	<!-- Popperjs -->
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
	<!-- Tempus Dominus JavaScript -->
	<script src="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.9.4/dist/js/tempus-dominus.min.js"></script>


	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

	<script src={{asset("resources/js/scripts.js")}}></script>
	@yield("scripts")
	@stack('scripts')
	
</body>
</html>

