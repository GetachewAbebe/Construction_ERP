<!DOCTYPE html>
<html lang="en" data-theme="natanem">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Natanem Engineering ERP' }}</title>

    {{-- Corporate Tailwind/daisyUI stylesheet (no Bootstrap on auth pages). --}}
    @vite(['resources/css/mary.css'])
</head>
<body class="min-h-screen bg-base-200 text-base-content antialiased"
      style="background-color:#f0f3f8;">
    {{ $slot }}
</body>
</html>
