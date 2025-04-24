<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\OidcClient;

class OidcClientCredentialController extends Controller
{
    public function getClient(): JsonResponse
    {
        $client = OidcClient::where('client_name', config("oidc.client_name"))->first();

        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        return response()->json([
            'client_id' => $client->client_id,
            'realm' => config('oidc.realm'),
            'keycloak_url' => config('oidc.keycloak_external_url'),
        ]);
    }
}
