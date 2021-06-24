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

    /**
     * Tests that getting the version class of a model with the ModeratableVersionTrait
     * uses ModeratableVersions base.
     */
    public function testVersionClassIsModeratable() {
        $user = new TestModeratableVersionsUser();
        $user->name = "Jaymeh";
        $user->email = "jaymeh@test.php";
        $user->password = "12345";
        $user->last_login = $user->freshTimestamp();
        $user->save();

        $version = $user->currentVersion();

        $this->assertInstanceOf(ModeratableVersion::class, $version);
    }

    /**
     * Tests that default attributes with no values are null.
     */
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

    /**
     * Tests that a user can't approve something if they are logged out.
     */
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

    /**
     * Tests that a logged in user can approve something.
     */
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

    /**
     * Tests that an item cannot be unapproved when a user is logged out.
     */
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

    /**
     * Tests that a version can be unapproved when a user is logged in.
     */
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
     * Tests that when creating a new model, it has no approved versions associated.
     */
    public function testThatANewModelHasNoApprovedVersions() {
        $user = new TestModeratableVersionsUser();
        $user->name = "Jaymeh";
        $user->email = "jaymeh@test.php";
        $user->password = "12345";
        $user->last_login = $user->freshTimestamp();
        $user->save();

        $approvedVersions = $user->approvedVersions()->get();

        $this->assertEmpty($approvedVersions);
    }

    /**
     * Tests that when we approve a version it shows up in the approved versions list.
     */
    public function testThatAnApprovedModelHasApprovedVersions()
    {
        $user = new TestModeratableVersionsUser();
        $user->name = "Jaymeh";
        $user->email = "jaymeh@test.php";
        $user->password = "12345";
        $user->last_login = $user->freshTimestamp();
        $user->save();

        $currentVersion = $user->currentVersion();
        $currentVersion->approve();

        // dd($user->approvedVersions()->count());

        $this->assertEquals(1, $user->approvedVersions()->count());
    }

    /**
     * As part of the saving process we want to not save a model until 
     * a version has been approved. As part of this I only want to merge
     * data from version when approved.
     */
    public function testThatAModelIsntUpdatedWithoutApproval() {
        $user = new TestModeratableVersionsUser();
        $initialName = 'Jaymeh';
        $user->name = $initialName;
        $user->email = "jaymeh@test.php";
        $user->password = "12345";
        $user->last_login = $user->freshTimestamp();
        $user->save();

        $user->name = 'Jeff';
        $user->save();

        // Pull a fresh copy of a model out of the database.
        $userData = $user->fresh();

        dd($userData->with('versions')->get());

        $this->assertEquals($userData->name, $initialName);

        // TODO: Ensure a version is still created with the 
        // change documented so that it can be applied.
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

    // protected $versionClass = ModeratableVersion::class ;

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
