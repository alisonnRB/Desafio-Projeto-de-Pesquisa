<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\OidcClient;

class OidcClientRegister extends Command
{
    protected $signature = 'registrar:oidc-client';
    protected $description = 'Registra o OIDC client no Keycloak';

    public function handle()
    {
        // Verifica se o cliente já foi registrado
        $clientExists = OidcClient::where('client_id', env('KEYCLOAK_CLIENT_ID'))->exists();

        if ($clientExists) {
            $this->info("Cliente OIDC já registrado.");
            return;
        }

        $keycloakUrl = env('KEYCLOAK_URL', 'http://localhost:8080');
        $realm = env('KEYCLOAK_REALM', 'baita-realm');
        $clientId = env('KEYCLOAK_CLIENT_ID', 'registrador');
        $clientSecret = env('KEYCLOAK_CLIENT_SECRET', 'secretKey');

        // Obtém o token
        $tokenResponse = Http::asForm()->post("{$keycloakUrl}/realms/{$realm}/protocol/openid-connect/token", [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);

        if (!$tokenResponse->successful()) {
            $this->error("Erro ao obter token: " . $tokenResponse->body());
            Log::error("Erro ao obter token: " . $tokenResponse->body());
            return;
        }

        $accessToken = $tokenResponse->json()['access_token'];

        $clientData = [
            "client_name" => "novo-client",
            "redirect_uris" => ["http://localhost:8081/callback"],
            "grant_types" => ["authorization_code"],
            "response_types" => ["code"],
            "token_endpoint_auth_method" => "none",
        ];

        $registrationResponse = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$keycloakUrl}/realms/{$realm}/clients-registrations/openid-connect", $clientData);

        if ($registrationResponse->status() === 201) {
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

            $this->info("Cliente OIDC registrado com sucesso!");
        } else {
            $this->error("Erro ao registrar client: " . $registrationResponse->body());
            Log::error("Erro ao registrar client: " . $registrationResponse->body());
        }
    }
}
