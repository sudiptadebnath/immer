<ul class="nav flex-column">

  <!-- Dashboard -->
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">
      <i class="bi bi-house-door me-2"></i>Dashboard
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('user.dashboard_live') ? 'active' : '' }}" href="{{ route('user.dashboard_live') }}" target="_blank">
      <i class="bi bi-record-circle me-2"></i>Dashboard-Live
    </a>
  </li>

  <!-- Settings (Collapsible) -->
  @php
    $settingsActive = in_array(Route::currentRouteName(), [
      'conf.settings',
	  'conf.general',
      'conf.action',
      'conf.category',
      'conf.committee',
      'conf.immerdt',
    ]);
    $repoActive = in_array(Route::currentRouteName(), [
      'repo.istat',
      'repo.regs',
      'repo.immer',
      'repo.dhun',
    ]);
  @endphp

  @if (hasRole('ao'))
  <li class="nav-item">
    <a class="nav-link d-flex justify-content-between align-items-center {{ $settingsActive ? 'active' : 'collapsed' }}"
       data-bs-toggle="collapse" href="#settingsMenu" role="button"
       aria-expanded="{{ $settingsActive ? 'true' : 'false' }}" aria-controls="settingsMenu">
      <span><i class="bi bi-gear me-2"></i>Settings</span>
      <i class="bi bi-chevron-down small transition-arrow"></i>
    </a>
    <ul class="collapse nav flex-column ms-3 {{ $settingsActive ? 'show' : '' }}" id="settingsMenu">
      {{-- <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('conf.settings') ? 'active' : '' }}" href="{{ route('conf.settings') }}">Settings</a>
      </li> --}}
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('conf.general') ? 'active' : '' }}" href="{{ route('conf.general') }}">General</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('conf.action') ? 'active' : '' }}" href="{{ route('conf.action') }}">Action Area</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('conf.category') ? 'active' : '' }}" href="{{ route('conf.category') }}">Category</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('conf.committee') ? 'active' : '' }}" href="{{ route('conf.committee') }}">Puja Committee</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('conf.immerdt') ? 'active' : '' }}" href="{{ route('conf.immerdt') }}">Immersion Date</a>
      </li>
    </ul>
  </li>
  @endif

  @if (hasRole('a'))
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('user.users') ? 'active' : '' }}" href="{{ route('user.users') }}">
      <i class="bi bi-person-lines-fill me-2"></i>Users
    </a>
  </li>
  @endif

  @if (hasRole('ao'))
  <li class="nav-item">
    <a class="nav-link {{ request()->is('user/puja*') ? 'active' : '' }}" href="{{ route('puja.index') }}">
      <i class="bi bi-brightness-high me-2"></i>Pujas
    </a>
  </li>
  @endif

  @if (hasRole('aos'))
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('att.scan') ? 'active' : '' }}" href="{{ route('att.scan') }}">
      <i class="bi bi-qr-code-scan me-2"></i>Scan
    </a>
  </li>
  @endif

  @if (hasRole('u'))
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('user.gpass') ? 'active' : '' }}" href="{{ route('user.gpass', ['id' => getUsrProp('id')]) }}">
      <i class="bi bi-ticket-perforated me-2"></i>GatePass
    </a>
  </li>
  @endif

  @if (hasRole('ao'))
  <li class="nav-item">
    <a class="nav-link d-flex justify-content-between align-items-center {{ $repoActive ? 'active' : 'collapsed' }}"
    data-bs-toggle="collapse" href="#reportsMenu" role="button"
    aria-expanded="{{ $repoActive ? 'true' : 'false' }}" aria-controls="reportsMenu">
      <span><i class="bi bi-clipboard-data me-2"></i>MIS Reports</span>
      <i class="bi bi-chevron-down small transition-arrow"></i>
    </a>
    <ul class="collapse nav flex-column ms-3 {{ $repoActive ? 'show' : '' }}" id="reportsMenu">
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('repo.istat') ? 'active' : '' }}" href="{{ route('repo.istat') }}">Immersion Status</a>
      </li>
      {{-- <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('repo.regs') ? 'active' : '' }}" href="{{ route('repo.regs') }}">Registration List</a>
      </li> --}}
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('repo.immer') ? 'active' : '' }}" href="{{ route('repo.immer') }}">Immersion By Date</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('repo.dhun') ? 'active' : '' }}" href="{{ route('repo.dhun') }}">Dhunuchi Nach By Date</a>
      </li>
    </ul>
  </li>
  @endif


</ul>

