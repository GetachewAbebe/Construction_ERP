<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Natanem Engineering')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- Fonts --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/css/erp-premium.css', 'resources/js/erp-premium.js'])

  {{-- ApexCharts --}}
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

  @stack('head')
</head>
<body class="bg-light">

<x-layouts.global-components />

  <x-layouts.sidebar />

<main class="{{ Auth::check() ? 'main-content' : 'min-vh-100' }}">
  <div class="{{ Auth::check() ? 'container-fluid' : '' }}">
      @yield('content')
  </div>

  @guest
    <footer class="footer-gradient text-white mt-auto py-2 fixed-bottom">
      <div class="container d-flex flex-column align-items-center justify-content-center text-center small">
        <div>© {{ date('Y') }} Natanem Engineering</div>
      </div>
    </footer>
  @endguest
  </div>


  {{-- JS: enable submenu click on mobile --}}
  <script>
    // 1. Blade-dependent Command Palette Links
    window.paletteLinks = [
        { name: 'Dashboard Overview', url: '{{ route("home") }}', icon: 'bi-speedometer2' },
        { name: 'Human Resource Hub', url: '{{ route("hr.dashboard") }}', icon: 'bi-people' },
        { name: 'Inventory Manager', url: '{{ route("inventory.dashboard") }}', icon: 'bi-boxes' },
        { name: 'Finance Center', url: '{{ route("finance.dashboard") }}', icon: 'bi-currency-dollar' },
        { name: 'Admin Console', url: '{{ route("admin.dashboard") }}', icon: 'bi-shield-lock' },
        { name: 'New Employee Onboarding', url: '{{ route("hr.employees.create") }}', icon: 'bi-person-plus' },
        { name: 'System Users', url: '{{ route("admin.users.index") }}', icon: 'bi-person-gear' },
        { name: 'Audit Trails (System Logs)', url: '{{ route("admin.activity-logs") }}', icon: 'bi-journal-text' },
        { name: 'Trash & Data Recovery', url: '{{ route("admin.trash.index") }}', icon: 'bi-trash-fill' }
    ];

    // 2. Session Flash Notifications
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('status'))
            window.showToast("{{ session('status') }}", 'success', 'Success');
        @endif
        @if(session('error'))
            window.showToast("{{ session('error') }}", 'danger', 'Error Occurred');
        @endif
        @if(session('success'))
            window.showToast("{{ session('success') }}", 'success', 'Success');
        @endif
    });
  </script>

  @stack('scripts')
</body>
</html>
