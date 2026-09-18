<!DOCTYPE html>
<html lang="en" id="html-root" data-theme="natanem">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Sign in · Natanem Engineering ERP' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/mary.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', 'Public Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
    </style>
    <script>
        (function () {
            var saved = localStorage.getItem('erp-theme');
            var isDark = (saved === 'natanem-dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches));
            var theme = isDark ? 'natanem-dark' : 'natanem';
            document.getElementById('html-root').setAttribute('data-theme', theme);
            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>
<body class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 antialiased selection:bg-blue-900 selection:text-white">
    {{ $slot }}
</body>
</html>
