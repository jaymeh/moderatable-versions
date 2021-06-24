<?php
namespace Jaymeh\ModeratableVersions;

use Mpociot\Versionable\Version;
use Illuminate\Support\Facades\Auth;

/**
 * Class Version
 * @package Jaymeh\ModeratableVersions
 */
class ModeratableVersion extends Version
{
    protected $isUpdating = false;

    public function approve() {
        $this->approved_at = time();
        $this->approved_by = $this->getAuthUserId();
        $this->save();
        return $this;
    }

    public function unapprove() {
        // For security, if we are logged out we can't unapprove something.
        if ( ! $this->getAuthUserId()) {
            return $this;
        }

        $this->approved_at = null;
        $this->approved_by = null;
        $this->save();
        return $this;
    }

    public function getIsApprovedAttribute() {
        return $this->approved_at != null;
    }

    /**
     * @return int|null
     */
    protected function getAuthUserId()
    {
        return Auth::check() ? Auth::id() : null;
    }

    /**
     * Initialize model events
     */
    public static function boot()
    {
        parent::boot();
        static::saved(function ($model) {
            $model->moderatableVersionsPostSave();
        });
    }

    public function moderatableVersionsPostSave() {
        // TODO: Go back through here and find out what the model's versions are.
        // Find the newest one in the list that has been approved and just use that one.

        $model = $this->getModel();
        $latest_approved_version = $model->firstVersion();
        
        // TODO: Make this check a bit more elegant.
        $approvedVersions = $model->approvedVersions;
        if ($approvedVersions->count()) {
            // Use the newest one.
            $latest_approved_version = $approvedVersions->first();
        }

        if (! $latest_approved_version) {
            return;
        }

        $latest_approved_version->revert();
    }
}