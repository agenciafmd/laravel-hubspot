<?php

declare(strict_types=1);

namespace Agenciafmd\Hubspot\Providers;

use Illuminate\Support\ServiceProvider;
use Override;

final class HubspotServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        //
    }

    #[Override]
    public function register(): void
    {
        $this->loadConfigs();
    }

    protected function loadConfigs(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-hubspot.php', 'laravel-hubspot');
    }
}
