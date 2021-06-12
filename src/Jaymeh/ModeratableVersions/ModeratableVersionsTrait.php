<?php

namespace Jaymeh\ModeratableVersions;

use Mpociot\Versionable\VersionableTrait;

trait ModeratableVersionsTrait {
    use VersionableTrait;

    /**
     * @inheritdoc
     */
    protected function getVersionClass()
    {
        if( property_exists( self::class, 'versionClass') ) {
            return $this->versionClass;
        }

        return config('versionable.version_model', ModeratableVersion::class);
    }

    /**
     * Returns the latest version available
     * @return ModeratableVersion
     */
    public function currentVersion()
    {
        return $this->getLatestVersions()->first();
    }

    // public $approvalEnabled = true;

    // /**
    //  * @return $this
    //  */
    // public function enableApproval()
    // {
    //     $this->approvalEnabled = true;
    //     return $this;
    // }

    // /**
    //  * @return $this
    //  */
    // public function disableApproval()
    // {
    //     $this->approvalEnabled = false;
    //     return $this;
    // }

    // TODO: How do I handle new models in general?

    // Handle Model saving.
}
