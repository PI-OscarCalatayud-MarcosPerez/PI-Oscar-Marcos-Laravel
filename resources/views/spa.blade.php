<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOKeys</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('img/icono.png') }}" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Sincronización de sesión Laravel -> Vue --}}
    @if(auth()->check())
        <script>
            localStorage.setItem('isAuthenticated', 'true');
        </script>
    @else
        <script>
            localStorage.removeItem('isAuthenticated');
        </script>
    @endif

    @vite('frontend/src/main.js')
</head>
<body>
    <div id="app"></div>
</body>
</html>
