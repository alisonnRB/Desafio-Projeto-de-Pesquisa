<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login com Keycloak</title>
    @vite('resources/css/login.css')

</head>

<body class="antialiased">
    <p id="denied">Nilel de garantia insuficiente</p>
    <button id="login-button">Faça Login</button>

    @vite('resources/js/login.js')
</body>

</html>