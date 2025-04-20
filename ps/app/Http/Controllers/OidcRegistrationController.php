<?php

// app/Http/Controllers/OidcRegistrationController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OidcRegistrationController extends Controller
{
    public function register(Request $request)
    {
        $keycloakUrl = env('KEYCLOAK_URL', 'http://localhost:8080');
        $realm = env('KEYCLOAK_REALM', 'baita-realm');
        $clientId = env('KEYCLOAK_CLIENT_ID', 'registrador');
        $clientSecret = env('KEYCLOAK_CLIENT_SECRET', 'secretKey');

        // Obter access token
        $tokenResponse = Http::asForm()->post("{$keycloakUrl}/realms/{$realm}/protocol/openid-connect/token", [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);

        if (!$tokenResponse->successful()) {
            return response()->json([
                'error' => 'Erro ao obter token.',
                'details' => $tokenResponse->body()
            ], 500);
        }

        $accessToken = $tokenResponse->json()['access_token'];

        // Montar dados do novo client
        $clientData = [
            "client_name" => $request->input("client_name", "novo-client"),
            "redirect_uris" => $request->input("redirect_uris", ["http://localhost:5000/callback"]),
            "grant_types" => ["authorization_code"],
            "response_types" => ["code"],
            "token_endpoint_auth_method" => "none",
        ];

        // Registrar o novo client
        $registrationResponse = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$keycloakUrl}/realms/{$realm}/clients-registrations/openid-connect", $clientData);

        if ($registrationResponse->status() === 201) {
            return response()->json($registrationResponse->json(), 201);
        }

        return response()->json([
            'error' => 'Erro ao registrar client.',
            'status' => $registrationResponse->status(),
            'details' => $registrationResponse->body()
        ], $registrationResponse->status());
    }
}