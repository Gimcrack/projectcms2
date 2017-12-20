<?php

namespace Tests\Unit;

use App\Group;
use Tests\TestCase;
use App\Events\GroupWasCreated;
use App\Events\GroupWasUpdated;
use App\Events\GroupWasDestroyed;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_have_a_name()
    {
        create(Group::class, [
            'name' => null
        ]);
    }

    /**
     * @test
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_have_a_unique_name()
    {
        create(Group::class, [
            'name' => 'Group Name'
        ]);

        create(Group::class, [
            'name' => 'Group Name'
        ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_group_is_created()
    {
        Event::fake();
        $group = create(Group::class);
        $this->assertEvent(GroupWasCreated::class, [ 'group' => $group ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_group_is_updated()
    {
        Event::fake();
        // given a group
        $group = create(Group::class);

        // act - update the group
        $group->update([
            'name' => 'New Name'
        ]);
        $this->assertEvent(GroupWasUpdated::class, [ 'group' => $group ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_group_is_destroyed()
    {
        Event::fake();
        // given a group
        $group = create(Group::class);

        // act - delete the group
        $group->delete();
        $this->assertEvent(GroupWasDestroyed::class, [ 'group' => $group ]);
    }
 }
