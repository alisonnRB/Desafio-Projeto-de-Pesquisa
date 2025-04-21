<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OidcClient extends Model
{
    protected $fillable = [
        'client_id',
        'client_name',
        'registration_access_token',
        'registration_client_uri',
        'redirect_uris',
        'token_endpoint_auth_method',
        'grant_types',
        'response_types',
        'scopes',
    ];
}
