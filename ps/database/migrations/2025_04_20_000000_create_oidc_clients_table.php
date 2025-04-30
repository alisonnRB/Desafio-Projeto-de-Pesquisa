<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('oidc_clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_id')->unique();
            $table->string('client_name');
            $table->string('client_secret');
            $table->longText('registration_access_token');
            $table->string('registration_client_uri');
            $table->json('redirect_uris')->nullable();
            $table->string('token_endpoint_auth_method')->nullable();
            $table->json('grant_types')->nullable();
            $table->json('response_types')->nullable();
            $table->json('scopes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oidc_clients');
    }
};
