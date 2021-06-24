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

    /**
     * Get only approved versions.
     */
    public function approvedVersions()
    {
        return $this->getLatestVersions()
            ->whereNotNull('approved_at')
            ->orderByDesc('approved_at');
    }

    /**
     * First Version of a model.
     */
    public function firstVersion()
    {
        return $this->getLatestVersions()
            ->orderBy('version_id')
            ->first();
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
    /**
     * Initialize model events
     */
    public static function bootModeratableVersionsTrait()
    {
        // static::saved(function ($model) {
        //     // Grab last approved Version
        //     $lastApprovedVersion = $model->approvedVersions()->first() ?: null;

        //     // TODO: For some reason this is firing before child. I should try to stop it.

        //     if (!$lastApprovedVersion) {
        //         $lastApprovedVersion = $model->firstVersion() ?: null;
        //     }

        //     if ($lastApprovedVersion !== null && $model->currentVersion()->version_id !== $lastApprovedVersion->version_id) {
        //         $lastApprovedVersion->revert();
        //     }
        
        //     // return false;
        //     // Might be bad but revert to last approved model (if there is one).

        //     // If there isn't revert to oldest known version (likely one it was created as).
        // });
    }

    // TODO: Overwrite this method to work with approved versioning.
    /**
     * Returns the previous version
     * @return Version
     */
    // public function previousVersion()
    // {
    //     return $this->getLatestVersions()->limit(1)->offset(1)->first();
    // }
}
