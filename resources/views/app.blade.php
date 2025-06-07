<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Заголовок страницы, который Inertia может менять динамически --}}
    <title inertia>{{ config('app.name', 'Animatorr') }}</title>

    {{-- CSRF-токен для Axios --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- 🔑  Ziggy: список всех именованных маршрутов Laravel --}}
    @routes

    {{-- Vite-бандл (Vue + Inertia) --}}
    @vite('resources/js/app.js')

    {{-- Inertia — метаданные страницы --}}
    @inertiaHead
</head>
<body class="font-sans antialiased text-gray-900">
    @inertia
</body>
</html>
