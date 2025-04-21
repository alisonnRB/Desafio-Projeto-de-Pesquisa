<?php

namespace App\Providers;

use App\Services\OidcClientRegistrar;
use Illuminate\Support\ServiceProvider;

class OidcClientServiceProvider extends ServiceProvider
{
    /**
     * Registre qualquer serviço de aplicação.
     *
     * @return void
     */
    public function register()
    {
        // Registra o serviço OidcClientRegistrar
        $this->app->singleton(OidcClientRegistrar::class, function ($app) {
            return new OidcClientRegistrar();
        });
    }

    /**
     * Bootstrap qualquer serviço de aplicação.
     *
     * @return void
     */
    public function boot()
    {
        // Aqui você pode chamar o método que realiza o registro, quando o sistema iniciar.
        $oidcClientRegistrar = $this->app->make(OidcClientRegistrar::class);
    }
}
