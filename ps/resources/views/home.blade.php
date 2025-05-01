<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Home - Pandas</title>
    @vite('resources/css/home.css')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

</head>

<body class="antialiased">
    <div class="home">
        <header class="pandas-head">
            <img src="/images/pandas_logo.svg" alt="logo do site pandas" class="panda">
            <h1 class="title">BEM VINDO AO PANDAS</h1>
        </header>

        <div class="box-info">
            <img src="/images/profile.svg" alt="">

            <div class="atribute-box">
            </div>
        </div>

        <button class="logout" id="logout">
            LOGOUT
        </button>

    </div>

    @vite('resources/js/home.js')
</body>

</html>