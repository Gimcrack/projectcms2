<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;
use App\Events\UserWasCreated;
use App\Events\UserWasUpdated;
use App\Events\UserWasDestroyed;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_have_a_name() {
        create(User::class, [
            'name' => null
        ]);
    }

    /**
     * @test
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_have_a_unique_name()
    {
        create(User::class, [
            'name' => 'User Name'
        ]);

        create(User::class, [
            'name' => 'User Name'
        ]);
    }

    /**
     * @test
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_have_a_email() {
        create(User::class, [
            'email' => null
        ]);
    }

    /**
     * @test
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_have_a_password() {
        create(User::class, [
            'password' => null
        ]);
    }

    /** @test */
    public function it_has_an_api_token() {
        $user = create(User::class);

        $this->assertNotNull($user->api_token);
    }

    /**
     * @test
     */
    public function it_can_be_configured_as_an_admin()
    {
        $user = create_state(User::class,'admin');

        $this->assertTrue( $user->isAdmin() );
    }

    /** @test */
    function it_can_be_promoted_to_an_admin()
    {
        $user = create(User::class);
        $this->assertFalse( $user->isAdmin() );

        $user->promoteToAdmin();
        $this->assertTrue( $user->fresh()->isAdmin() );
    }

    /** @test */
    function it_can_be_demoted_to_a_user()
    {
        $user = create_state(User::class,'admin');
        $this->assertTrue( $user->isAdmin() );

        $user->demoteToUser();
        $this->assertFalse( $user->fresh()->isAdmin() );
    }

    /**
     * @test
     */
    public function it_can_be_configured_as_an_approver()
    {
        $user = create_state(User::class,'approver');

        $this->assertTrue( $user->isApprover() );
    }

    /** @test */
    function it_can_be_promoted_to_an_approver()
    {
        $user = create(User::class);
        $this->assertFalse( $user->isApprover() );

        $user->promoteToApprover();
        $this->assertTrue( $user->fresh()->isApprover() );
    }

    /** @test */
    function it_can_be_demoted_to_a_user_from_an_approver()
    {
        $user = create_state(User::class,'approver');
        $this->assertTrue( $user->isApprover() );

        $user->demoteApproverToUser();
        $this->assertFalse( $user->fresh()->isApprover() );
    }

    /** @test */
    function an_event_is_dispatched_when_a_user_is_created()
    {
        Event::fake();
        $user = create(User::class);
        $this->assertEvent(UserWasCreated::class, [ 'user' => $user ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_user_is_updated()
    {
        Event::fake();
        // given a published user
        $user = create(User::class);

        // act - update the user
        $user->promoteToAdmin();
        $this->assertEvent(UserWasUpdated::class, [ 'user' => $user ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_user_is_destroyed()
    {
        Event::fake();
        // given a user
        $user = create(User::class);

        // act - delete the user
        $user->delete();
        $this->assertEvent(UserWasDestroyed::class, [ 'user' => $user ]);
    }
}