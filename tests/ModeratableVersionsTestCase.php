<?php

class ModeratableVersionsTestCase extends VersionableTestCase {
    public function setUp(): void
    {
        parent::setup();
    }

    protected function setUpDatabase()
    {
        parent::setUpDatabase();
        include_once __DIR__ . '/../src/migrations/2021_06_02_200902_add_versions_approval_fields.php';

        (new \AddVersionsApprovalFields())->up();
    }

    protected function getPackageProviders($app)
    {
        return [
            \Jaymeh\ModeratableVersions\Providers\ServiceProvider::class,
        ];
    }
}