<?php

namespace Jaymeh\ModeratableVersions\Providers;

use Mpociot\Versionable\Providers\ServiceProvider as VersionableServiceProvider;

class ServiceProvider extends VersionableServiceProvider {
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register ()
    {
        $this->mergeConfigFrom(__DIR__.'/../../../config/config.php', 'versionable');
    }

    public function boot() {
        parent::boot();

        $this->publishes([
            __DIR__.'/../../../config/config.php' => config_path('versionable.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../../../migrations/' => database_path('/migrations'),
        ], 'migrations');
    }
}
