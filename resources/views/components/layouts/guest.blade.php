<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1a56db">
    <title>{{ $title ?? 'Login' }} — AgenDamay SMKN 2 Indramayu</title>
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getBase64() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100" style="background: linear-gradient(135deg, #1a56db 0%, #0d47a1 50%, #1e3a5f 100%);">
    <main class="container px-3" style="max-width: 420px;">
        {{ $slot }}
    </main>
    @livewireScripts
</body>
</html>
