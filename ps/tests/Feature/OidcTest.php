<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\Assert;

class OidcTest extends TestCase
{
    private $keycloakUrl;
    private $realm;
    private $initialClientId;
    private $initialClientSecret;
    private static $newClientData = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->keycloakUrl = rtrim(env('KEYCLOAK_BASE_URL', 'http://keycloak:8080'), '/');
        $this->realm = config('oidc.realm', 'baita-realm');
        $this->initialClientId = config('oidc.client_id', 'terraform-client');
        $this->initialClientSecret = config('oidc.client_secret');

        if (empty($this->initialClientSecret)) {
            $this->markTestSkipped('Client secret inicial (oidc.client_secret) não configurado.');
        }
        if (empty($this->initialClientId)) {
            $this->markTestSkipped('Client ID inicial (oidc.client_id) não configurado.');
        }
        if (empty($this->realm)) {
            $this->markTestSkipped('Realm (oidc.realm) não configurado.');
        }
    }

    /**
     * Testa o registro dinâmico de cliente.
     * @return array Dados do cliente criado.
     */
    public function test_dynamic_client_creation(): array
    {
        $clientName = "test_client_" . uniqid();
        $clientData = [
            "client_name" => $clientName,
            "redirect_uris" => ["http://localhost:8081/callback"],
            "grant_types" => ["authorization_code", "password", "refresh_token"],
            "response_types" => ["code"],
            "token_endpoint_auth_method" => "client_secret_post",
        ];

        $responseTokenAccess = Http::asForm()->post("{$this->keycloakUrl}/realms/{$this->realm}/protocol/openid-connect/token", [
            'grant_type' => 'client_credentials',
            'client_id' => $this->initialClientId,
            'client_secret' => $this->initialClientSecret,
        ]);

        $this->assertTrue($responseTokenAccess->successful(), "Erro ao obter o token inicial: " . $responseTokenAccess->body());
        $accessToken = $responseTokenAccess->json()['access_token'];

        $registrationResponse = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$this->keycloakUrl}/realms/{$this->realm}/clients-registrations/openid-connect", $clientData);

        $this->assertEquals(201, $registrationResponse->status(), "Erro ao registrar o cliente: " . $registrationResponse->body());

        $registrationData = $registrationResponse->json();
        $this->assertArrayHasKey('client_id', $registrationData);
        $this->assertArrayHasKey('client_secret', $registrationData);

        self::$newClientData = [
            'client_id' => $registrationData['client_id'],
            'client_name' => $registrationData['client_name'],
            'client_secret' => $registrationData['client_secret'],
        ];

        return self::$newClientData;
    }

    /**
     * @depends test_dynamic_client_creation
     */
    public function test_login_oidc(array $newClient): array
    {
        $username = 'user1';
        $password = 'password123';

        $tokenResponse = Http::asForm()->post("{$this->keycloakUrl}/realms/{$this->realm}/protocol/openid-connect/token", [
            'grant_type' => 'password',
            'client_id' => $newClient['client_id'],
            'client_secret' => $newClient['client_secret'],
            'username' => $username,
            'password' => $password,
        ]);

        $this->assertTrue($tokenResponse->successful(), "Erro ao obter tokens via ROPC: " . $tokenResponse->body());

        return $tokenResponse->json();
    }

    /**
     * @depends test_login_oidc
     */
    public function test_attribute_in_user(array $tokenData)
    {
        $this->assertArrayHasKey('access_token', $tokenData);

        $payload = $this->decodeJwtPayload($tokenData['access_token']);
        $this->assertNotNull($payload);

        $this->assertSame('TI', $payload['departamento'] ?? null);
        $this->assertSame('Analista', $payload['cargo'] ?? null);
        $this->assertSame('Pleno', $payload['senioridade'] ?? null);
    }

    /**
     * @depends test_login_oidc
     */
    public function test_redirect_when_user_has_low_access_level(array $tokenData)
    {
        $accessToken = $tokenData['access_token'];

        $payload = $this->decodeJwtPayload($accessToken);
        $this->assertNotNull($payload);
        $this->assertNotEquals('2', $payload['acr'] ?? '');

        // Simula a requisição autenticada com o token
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$accessToken}"
        ])->get('/home');

        $this->assertTrue(
            in_array($response->status(), [302, 401, 403]),
            "Usuário sem nivel_acesso=alto conseguiu acessar /home."
        );
    }

    private function decodeJwtPayload(string $jwt): ?array
    {
        try {
            $parts = explode('.', $jwt);
            if (count($parts) !== 3) {
                return null;
            }

            $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1]));
            return json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            Assert::fail("Falha ao decodificar JWT payload: " . $e->getMessage());
            return null;
        }
    }
}
