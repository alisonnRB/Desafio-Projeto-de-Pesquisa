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

    private $keycloakUrl;
    private $realm;
    private $clientId;
    private $clientSecret;
    private $clientData;

    public function __construct()
    {
        parent::__construct();

        $this->keycloakUrl = config('oidc.keycloak_url');
        $this->realm = config('oidc.realm');
        $this->clientId = config('oidc.client_id');
        $this->clientSecret = config('oidc.client_secret');
        $this->clientData = [
            "client_name" => config("oidc.client_name"),
            "redirect_uris" => ["http://localhost:8081/callback"],
            "grant_types" => ["authorization_code"],
            "response_types" => ["code"],
            "token_endpoint_auth_method" => "client_secret_post",
        ];
    }

    public function handle()
    {

        // Verifica se o cliente já foi registrado
        if ($this->bdRecordsverify()) {
            $this->info("Cliente OIDC já registrado.");
            return;
        }

        // busca o token de acesso keycloak
        $accessToken = $this->getAccessTokens();
        if (!$accessToken) {
            return;
        }

        // Registra o client da aplicação
        $registrationResponse = $this->dynamicClientRegistrate($accessToken);

        // verifica o status da criação do cliente
        if ($registrationResponse->status() === 201) {

            // Armazena os dados do client
            $this->bdClientRegistrate($registrationResponse);
            $this->addAcrMapper($registrationResponse);
            $this->info("Cliente OIDC registrado com sucesso!");

        } else {
            $this->error("Erro ao registrar client: " . $registrationResponse->body());
            Log::error("Erro ao registrar client: " . $registrationResponse->body());
        }
    }

    // verifica a existencia do client
    private function bdRecordsverify(): bool
    {
        return OidcClient::where('client_id', $this->clientId)->exists();
    }

    // Busca o Token de acesso
    private function getAccessTokens()
    {

        // Obtém o token
        $tokenResponse = Http::asForm()->post("{$this->keycloakUrl}/realms/{$this->realm}/protocol/openid-connect/token", [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        // verfica se o token foi obtido
        if (!$tokenResponse->successful()) {
            $this->error("Erro ao obter token: " . $tokenResponse->body());
            Log::error("Erro ao obter token: " . $tokenResponse->body());
            return;
        }

        return $tokenResponse->json()['access_token'];
    }
    // Registra o novo cliente dinamicamente
    private function dynamicClientRegistrate($accessToken)
    {
        $registrationResponse = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$this->keycloakUrl}/realms/{$this->realm}/clients-registrations/openid-connect", $this->clientData);

        return $registrationResponse;
    }

    // Guarda as informaçoes do client no BD
    private function bdClientRegistrate($registrationData)
    {
        OidcClient::create([
            'client_id' => $registrationData->json()['client_id'],
            'client_name' => $registrationData->json()['client_name'],
            'client_secret' => $registrationData->json()['client_secret'],
            'registration_access_token' => $registrationData->json()['registration_access_token'],
            'registration_client_uri' => $registrationData->json()['registration_client_uri'],
            'redirect_uris' => json_encode($registrationData->json()['redirect_uris'] ?? []),
            'token_endpoint_auth_method' => $registrationData->json()['token_endpoint_auth_method'] ?? null,
            'grant_types' => json_encode($registrationData->json()['grant_types'] ?? []),
            'response_types' => json_encode($registrationData->json()['response_types'] ?? []),
            'scopes' => json_encode($registrationData->json()['scopes'] ?? []),
        ]);
    }

    private function addAcrMapper($registrationResponse)
    {
        $clientUri = $registrationResponse->json()['registration_client_uri'];
        $registrationToken = $registrationResponse->json()['registration_access_token'];

        $acrMapper = [
            "name" => "acr-mapper",
            "protocol" => "openid-connect",
            "protocolMapper" => "oidc-acr-mapper",
            "consentRequired" => false,
            "config" => [
                "id.token.claim" => "true",
                "access.token.claim" => "true",
                "claim.name" => "acr",
                "jsonType.label" => "String"
            ]
        ];

        // Envia o mapper para o cliente registrado
        $response = Http::withToken($registrationToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($clientUri . '/protocol-mappers/models', $acrMapper);

        if (!$response->successful()) {
            Log::error("Erro ao adicionar acr-mapper: " . $response->body());
        }
    }

}
