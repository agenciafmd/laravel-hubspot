<?php

namespace Agenciafmd\Hubspot\Providers;

use Illuminate\Support\ServiceProvider;

class HubspotServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register()
    {
        $this->loadConfigs();
    }

    protected function loadConfigs()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-hubspot.php', 'laravel-hubspot');
    }
}
