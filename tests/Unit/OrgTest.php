<?php

namespace Tests\Unit;

use App\Org;
use Tests\TestCase;
use App\Events\OrgWasCreated;
use App\Events\OrgWasUpdated;
use App\Events\OrgWasDestroyed;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrgTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_have_a_name()
    {
        create(Org::class, [
            'name' => null
        ]);
    }

    /**
     * @test
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_have_a_unique_name()
    {
        create(Org::class, [
            'name' => 'Org Name'
        ]);

        create(Org::class, [
            'name' => 'Org Name'
        ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_org_is_created()
    {
        Event::fake();
        $org = create(Org::class);
        $this->assertEvent(OrgWasCreated::class, [ 'org' => $org ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_org_is_updated()
    {
        Event::fake();
        // given a org
        $org = create(Org::class);

        // act - update the org
        $org->update([
            'name' => 'New Name'
        ]);
        $this->assertEvent(OrgWasUpdated::class, [ 'org' => $org ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_org_is_destroyed()
    {
        Event::fake();
        // given a org
        $org = create(Org::class);

        // act - delete the org
        $org->delete();
        $this->assertEvent(OrgWasDestroyed::class, [ 'org' => $org ]);
    }
 }
