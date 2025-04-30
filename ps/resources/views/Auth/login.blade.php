<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pandas - Service</title>
    @vite('resources/css/login.css')

</head>

<body>
    <div class="login">

        <div class="logo-box">
            <h1 class="title">PANDAS AUTH </br> SERVICE</h1>
            <img src="/images/pandas_logo.svg" alt="logo do site pandas" class="panda">
        </div>

        <button id="login-button">ENTRAR COM KEYCLOAK</button>

        <p>PANDAS É UM SERVIÇO FICTICIO CRIADO PARA </br> USAR E TESTAR O SISTEMA DE LOGIN OIDC</p>

    </div>

    @vite('resources/js/login.js')
</body>

</html>