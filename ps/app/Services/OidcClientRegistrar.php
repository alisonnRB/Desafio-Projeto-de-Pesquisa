<?php

namespace App\Services;

use App\Models\OidcClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OidcClientRegistrar
{
    public function __construct()
    {
        $this->registerClientIfNeeded();
    }

    private function registerClientIfNeeded()
    {
        // Verifica se o cliente já foi registrado
        $clientExists = OidcClient::where('client_id', env('KEYCLOAK_CLIENT_ID'))->exists();

        if ($clientExists) {
            return; // Cliente já existe, nada precisa ser feito
        }

        // Registra o cliente no Keycloak
        $keycloakUrl = env('KEYCLOAK_URL', 'http://localhost:8080');
        $realm = env('KEYCLOAK_REALM', 'baita-realm');
        $clientId = env('KEYCLOAK_CLIENT_ID', 'registrador');
        $clientSecret = env('KEYCLOAK_CLIENT_SECRET', 'secretKey');

        // Obtém o token de acesso
        $tokenResponse = Http::asForm()->post("{$keycloakUrl}/realms/{$realm}/protocol/openid-connect/token", [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);

        if (!$tokenResponse->successful()) {
            Log::error("Erro ao obter token: " . $tokenResponse->body());
            throw new \Exception("Erro ao obter token: " . $tokenResponse->body());
        }

        $accessToken = $tokenResponse->json()['access_token'];

        // Monta os dados do novo cliente
        $clientData = [
            "client_name" => "novo-client",
            "redirect_uris" => ["http://localhost:8081/callback"],
            "grant_types" => ["authorization_code"],
            "response_types" => ["code"],
            "token_endpoint_auth_method" => "none",
        ];

        // Registra o cliente no Keycloak
        $registrationResponse = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$keycloakUrl}/realms/{$realm}/clients-registrations/openid-connect", $clientData);

        if ($registrationResponse->status() === 201) {
            // Salva as informações do cliente no banco de dados
            OidcClient::create([
                'client_id' => $registrationResponse->json()['client_id'],
                'client_name' => $registrationResponse->json()['client_name'],
                'registration_access_token' => $registrationResponse->json()['registration_access_token'],
                'registration_client_uri' => $registrationResponse->json()['registration_client_uri'],
                'redirect_uris' => json_encode($registrationResponse->json()['redirect_uris'] ?? []),
                'token_endpoint_auth_method' => $registrationResponse->json()['token_endpoint_auth_method'] ?? null,
                'grant_types' => json_encode($registrationResponse->json()['grant_types'] ?? []),
                'response_types' => json_encode($registrationResponse->json()['response_types'] ?? []),
                'scopes' => json_encode($registrationResponse->json()['scopes'] ?? []),
            ]);
        } else {
            Log::error("Erro ao registrar client: " . $registrationResponse->body());
            throw new \Exception("Erro ao registrar client: " . $registrationResponse->body());
        }
    }
}
