<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class OidcAuthController extends Controller
{
    private $clientData = [];
    public function handleCallback(Request $request)
    {
        $this->clientData();

        $code = $request->get('code');
        if (!$code) {
            return response()->json(['error' => 'Código de autorização não encontrado.'], 400);
        }

        $tokens = $this->tokenRequest($request->get('code'));

        // Obtemos as infos do usuário
        $userInfo = $this->getUserInfos($tokens);

        if ($userInfo['acr'] != 2) {
            return redirect('/denied')->with('error', 'negado');
        }

        // Cria ou atualiza usuário no banco
        $user = $this->BdUserUpdateOrCreate($userInfo);

        // Faz login do usuário
        Auth::login($user);

        return redirect('/home'); // ou onde quiser
    }

    private function ClientData()
    {
        $client = \App\Models\OidcClient::where('client_name', config("oidc.client_name"))->first();

        $this->clientData = [
            'grant_type' => json_decode($client->grant_types)[0],
            'client_id' => $client->client_id,
            'client_secret' => $client->client_secret,
        ];

    }

    private function tokenRequest($code)
    {
        $response = Http::asForm()->post(config('oidc.keycloak_url') . '/realms/' . config('oidc.realm') . '/protocol/openid-connect/token', [
            'grant_type' => $this->clientData["grant_type"],
            'client_id' => $this->clientData["client_id"],
            'client_secret' => $this->clientData["client_secret"],
            'code' => $code,
            'redirect_uri' => "http://localhost:8081/callback",
            'scope' => 'openid'
        ]);

        if (!$response->successful()) {
            abort(500, 'Erro ao trocar o código pelo token: ' . $response->body());
        }

        return $response->json();
    }

    private function getUserInfos($tokens)
    {
        $tokenParts = explode('.', $tokens['access_token']);
        $payload = base64_decode($tokenParts[1]);

        // Retorne o conteúdo do payload
        return json_decode($payload, true);
    }

    private function BdUserUpdateOrCreate($userInfo)
    {
        $user = User::updateOrCreate(
            ['email' => $userInfo['email']],
            [
                'name' => $userInfo['name'],
                'preferred_username' => $userInfo['preferred_username'],
                'password' => "default",
                'acr' => $userInfo['acr'],
            ]
        );

        return $user;
    }

}