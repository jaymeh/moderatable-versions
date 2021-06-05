<?php

use Mockery as m;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Jaymeh\ModeratableVersions\ModeratableVersion;
use Jaymeh\ModeratableVersions\ModeratableVersionsTrait;

class ModeratableVersionsTest extends ModeratableVersionsTestCase {
    public function tearDown(): void
    {
        m::close();
        Auth::clearResolvedInstances();
    }

    // TODO: Test User Creating Version
    public function testModeratableAttributesAreNullByDefault() {
        $user = new TestModeratableVersionsUser();
        $user->name = "Jaymeh";
        $user->email = "jaymeh@test.php";
        $user->password = "12345";
        $user->last_login = $user->freshTimestamp();
        $user->save();

        $version = $user->currentVersion();

        $this->assertNull( $version->approved_at );
        $this->assertNull( $version->approved_by );
    }

    public function testVersionApprovalByLoggedOutUser() {
        $user = new TestModeratableVersionsUser();
        $user->name = "Jaymeh";
        $user->email = "jaymeh@test.php";
        $user->password = "12345";
        $user->last_login = $user->freshTimestamp();
        $user->save();

        $version = $user->currentVersion();
        $version->approve();

        $this->assertNull($version->approved_by);
        $this->assertNotNull($version->approved_at);
    }

    public function testApprovalByLoggedInUser() {
        $user = new TestModeratableVersionsUser();
        $user->name = "Jaymeh";
        $user->email = "jaymeh@test.php";
        $user->password = "12345";
        $user->last_login = $user->freshTimestamp();
        $user->save();

        $this->be($user);

        $version = $user->currentVersion();
        $version->approve();

        $this->assertEquals($user->id, $version->approved_by);
        $this->assertNotNull($version->approved_at);
    }

    public function testUnapprovalWhenLoggedOut() {
        $user = new TestModeratableVersionsUser();
        $user->name = "Jaymeh";
        $user->email = "jaymeh@test.php";
        $user->password = "12345";
        $user->last_login = $user->freshTimestamp();
        $user->save();

        $version = $user->currentVersion();
        $version->approve();

        $version->unapprove();

        $this->assertNull($version->approved_by);
        $this->assertNotNull($version->approved_at);
    }

    public function testUnapprovalWhenLoggedIn() {
        $user = new TestModeratableVersionsUser();
        $user->name = "Jaymeh";
        $user->email = "jaymeh@test.php";
        $user->password = "12345";
        $user->last_login = $user->freshTimestamp();
        $user->save();

        $this->be($user);

        $version = $user->currentVersion();
        $version->approve();

        $version->unapprove();

        $this->assertNull($version->approved_by);
        $this->assertNull($version->approved_at);
    }

    /**
     * @dataProvider isApprovedDataProvider
     *
     * @return void
     */
    public function testIsApprovedValues($shouldApprove) {
        $user = new TestModeratableVersionsUser();
        $user->name = "Jaymeh";
        $user->email = "jaymeh@test.php";
        $user->password = "12345";
        $user->last_login = $user->freshTimestamp();
        $user->save();

        $version = $user->currentVersion();
        if ($shouldApprove) {
            $version->approve();
        }

        $this->assertEquals($version->isApproved, $shouldApprove);
    }

    /**
     * Provides a small amount of data for if something should be approved.
     *
     * @return array
     */
    public function isApprovedDataProvider() {
        return [
            [
                true
            ],
            [
                false
            ]
        ];
    }

    // Handle the saving of the model.
    // TODO: Test if we are not using the approval process then we always set timestamp
    // and user (if logged in).
    // public function testApprovalNotEnabledFunctionalityWithLoggedOutUser() {
    //     $user = new TestModeratableVersionsUser();
    //     $user->disableApproval();

    //     $user->name = "Jaymeh";
    //     $user->email = "jaymeh@test.php";
    //     $user->password = "12345";
    //     $user->last_login = $user->freshTimestamp();
    //     $user->save();

    //     $version = $user->currentVersion();

    //     $this->assertNull($version->approved_by);
    //     $this->assertNotNull($version->approved_at);
    // }

    // public function testApprovalNotEnabledFunctionalityWithLoggedInUser() {
    //     $user = new TestModeratableVersionsUser();
    //     $user->disableApproval();

    //     $user->name = "Jaymeh";
    //     $user->email = "jaymeh@test.php";
    //     $user->password = "12345";
    //     $user->last_login = $user->freshTimestamp();
    //     $user->save();

    //     $version = $user->currentVersion();

    //     $this->assertNotNull($version->approved_by);
    //     $this->assertNotNull($version->approved_at);
    // }
}

class TestModeratableVersionsUser extends \Illuminate\Foundation\Auth\User {
    use ModeratableVersionsTrait;

    protected $versionClass = ModeratableVersion::class ;

    protected $table = "users";
}

class DynamicVersionModel extends ModeratableVersion
{
    const TABLENAME = 'other_versions';
    public $table = self::TABLENAME;
}

class ModelWithJsonField extends Model
{
    const TABLENAME = 'table_with_json_field';
    public $table = self::TABLENAME ;
    use ModeratableVersionsTrait;
    protected $casts = ['json_field' => 'array'];
}

class ModelWithDynamicVersion extends Model
{
    const TABLENAME = 'some_data';
    public $table = self::TABLENAME ;
    //use DynamicVersionModelTrait;
    use ModeratableVersionsTrait;
    protected $versionClass = DynamicVersionModel::class ;
}
