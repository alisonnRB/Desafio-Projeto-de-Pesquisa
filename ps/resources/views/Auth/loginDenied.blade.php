<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pandas - Acesso negado</title>
    @vite('resources/css/login_denied.css')

</head>

<body class="antialiased">
    <div class="login">
        <span class="step">
            <h1 class="title">PANDAS AUTH </br> SERVICE</h1>
        </span>

        <span class="step mid">
            <h2 class="denied">NIVEL DE ACESSO NEGADO</h2>
            <p class="warn">CONFIGURE O SISTEMA DE LOGIN POR OTP NO KEYCLOAK PARA TER NIVEL DE ACESSO SUFICIENTE</p>
        </span>

        <span class="step">
            <button id="login-button">IR PARA O KEYCLOAK</button>
        </span>
    </div>


    @vite('resources/js/login_denied.js')
</body>

</html>