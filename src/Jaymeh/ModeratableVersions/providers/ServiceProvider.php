<?php

namespace Jaymeh\ModeratableVersions\Providers;

use Mpociot\Versionable\Providers\ServiceProvider as VersionableServiceProvider;

class ServiceProvider extends VersionableServiceProvider {
    public function boot() {
        parent::boot();
        
        // Register my migrations?
    }
}
