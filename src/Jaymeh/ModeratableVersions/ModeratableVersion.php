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
    // public static function boot()
    // {
    //     parent::boot();
    //     // static::saving(function ($model) {
    //     //     $model->moderatableVersionsPreSave();
    //     // });

    //     static::saved(function ($model) {
    //         $model->moderatableVersionsPostSave();
    //     });
    // }
}