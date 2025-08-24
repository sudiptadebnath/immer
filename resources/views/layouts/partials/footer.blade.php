<footer class="border-top py-2 text-muted small container-fluid d-flex flex-column flex-md-row justify-content-center justify-content-md-between text-center text-md-start gap-1 px-4">
  <div>© {{ date("Y") }} {{ env('APP_TITLE', 'pms') }} | {{ env('APP_DESC', 'pms desc') }}</div>
  <div>
	Developed by 
	<a href="{{ env('APP_DEV_URL', '#') }}" target="_blank" class="text-decoration-none">{{ env('APP_DEV', 'altis') }} </a>
  </div>
</footer>
