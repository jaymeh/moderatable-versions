<?php

namespace Jaymeh\ModeratableVersions\Providers;

use Mpociot\Versionable\Providers\ServiceProvider as VersionableServiceProvider;

class ServiceProvider extends VersionableServiceProvider {
    public function boot() {
        parent::boot();

        $this->publishes([
            __DIR__ . '/../../../migrations/' => database_path('/migrations'),
        ], 'migrations');
    }
}
