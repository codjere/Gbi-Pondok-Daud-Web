<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'GBI Pondok Daud')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">

    <style type="text/tailwindcss">
        @theme {
            --font-sans: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            --color-cream: #FAF7F2;
            --color-forest: #1B4332;
            --color-forest-mid: #2D6A4F;
            --color-gold: #C9952A;
            --color-gold-soft: #F5E6C8;
            --color-warm: #E07A5F;
            --color-warm-soft: #FDF0ED;
        }
    </style>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .pattern-dots {
            background-image: radial-gradient(circle, rgba(201,149,42,0.15) 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-cream text-stone-800 antialiased overflow-x-hidden">
    @yield('content')
    @stack('scripts')
</body>
</html>
