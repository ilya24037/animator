<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Заголовок, который меняется через @inertiaHead --}}
    <title inertia>{{ config('app.name', 'Animatorr') }}</title>

    {{-- 🔑  CSRF-токен — Axios забирает его из этого meta-тега  --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Vite + Inertia --}}
    @vite('resources/js/app.js')
    @inertiaHead
</head>
<body class="font-sans antialiased text-gray-900">
    @inertia
</body>
</html>
