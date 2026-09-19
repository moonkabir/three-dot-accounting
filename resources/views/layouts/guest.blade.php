<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ThreeDot Accounting') }} — ERP Suite</title>
    <meta name="description" content="ThreeDot Accounting ERP — Secure login to your enterprise resource planning dashboard.">

    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-deep:      #080812;
            --bg-card:      rgba(255,255,255,0.04);
            --border:       rgba(255,255,255,0.08);
            --accent:       #6366f1;
            --accent-light: #818cf8;
            --accent-glow:  rgba(99,102,241,0.35);
            --text-primary: #f1f5f9;
            --text-muted:   #94a3b8;
            --error:        #f87171;
            --success:      #34d399;
        }

        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: var(--bg-deep);
            color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
        }
    </style>
</head>
<body>
    {{ $slot }}
</body>
</html>
