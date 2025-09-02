<ul class="nav flex-column">

  <!-- Dashboard -->
  <li class="nav-item">
    <a class="nav-link" href="{{ route('user.dashboard') }}"><i class="bi bi-house-door me-2"></i>Dashboard</a>
  </li>

  <!-- Settings (Always open) -->
@if (hasRole('ao'))
  <li class="nav-item">
    <span class="nav-link"><i class="bi bi-gear me-2"></i>Settings</span>
    <ul class="nav flex-column ms-4">
      <li class="nav-item"><a class="nav-link" href="{{ route('conf.action') }}">Action Area</a></li>
      <li class="nav-item"><a class="nav-link" href="{{ route('conf.category') }}">Category</a></li>
      <li class="nav-item"><a class="nav-link" href="{{ route('conf.committee') }}">Puja Committee</a></li>
      <li class="nav-item"><a class="nav-link" href="{{ route('conf.immerdt') }}">Immersion Date</a></li>
    </ul>
  </li>
@endif

@if (hasRole('a'))
  <li class="nav-item">
    <a class="nav-link" href="{{ route('user.users') }}"><i class="bi bi-person-lines-fill me-2"></i>Users</a>
  </li>
@endif
@if (hasRole('o'))
  <li class="nav-item">
    <a class="nav-link" href="{{ url('user/puja') }}"><i class="bi bi-brightness-high me-2"></i>Pujas</a>
  </li>
@endif

@if (hasRole('aos'))
  <li class="nav-item">
    <a class="nav-link" href="{{ route('att.scan') }}"><i class="bi bi-qr-code-scan me-2"></i>Scan</a>
  </li>
@endif

@if (hasRole('u'))
  <li class="nav-item">
    <a class="nav-link" href="{{ route('user.gpass', ['id' => getUsrProp('id')]) }}">
    <i class="bi bi-ticket-perforated me-2"></i>GatePass</a>
  </li>
@endif

</ul>