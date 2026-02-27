<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- PrimeVue Theme: Lara Light Blue -->
    {{-- <link rel="stylesheet" href="https://unpkg.com/primevue@4.5.4/resources/themes/lara-light-blue/theme.css">
    <link rel="stylesheet" href="https://unpkg.com/primeicons@6.0.0/primeicons.css">
    <link rel="stylesheet" href="https://unpkg.com/primeflex@3.3.0/primeflex.min.css"> --}}
</head>
<body>
    <div id="app"></div>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</body>
</html>
