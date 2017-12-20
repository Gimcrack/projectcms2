<?php

namespace Tests\Feature;

use App\Group;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_can_get_a_listing_of_groups()
    {
        $groups = create(Group::class,3);

        $this->actingAsUser()
            ->get("groups.index")
            ->response()
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonModelCollection($groups);
    }

    /** @test */
    public function it_can_get_a_single_group()
    {
        $group = create(Group::class);

        $this->actingAsUser()
            ->get(["groups.show",$group])
            ->response()
            ->assertStatus(200)
            ->assertJsonModel($group);
    }

    /** @test */
    public function it_can_store_a_new_group()
    {
        $atts = make_array(Group::class);

        $this->actingAsUser()
            ->post("groups.store",$atts)
            ->response()
            ->assertStatus(201);

        $this->assertDatabaseHas('groups',$atts);
    }

    /** @test */
    public function it_wont_store_a_new_group_without_a_name()
    {
        $atts = make_array(Group::class, [
            'name' => null
        ]);

        $this->actingAsUser()
             ->post("groups.store",$atts)
             ->response()
             ->assertStatus(422)
             ->assertJsonValidationErrors('name');

        $this->assertDatabaseMissing('groups',$atts);
    }

    /** @test */
    public function it_wont_store_a_new_group_with_a_duplicate_name()
    {
        create(Group::class, ['name' => 'Group Name']);


        $atts = make_array(Group::class, [
            'name' => 'Group Name'
        ]);

        $this->actingAsUser()
             ->post("groups.store",$atts)
             ->response()
             ->assertStatus(422)
             ->assertJsonValidationErrors('name');
    }

    /** @test */
    public function it_can_update_a_group()
    {
        $group = create(Group::class, ['name' => 'Group Name']);

        $this->actingAsUser()
            ->patch(["groups.update", $group], ['name' => 'New Name'])
            ->response()
            ->assertStatus(202);

        $this->assertDatabaseHas('groups',['name' => 'New Name']);
    }

    /** @test */
    public function it_can_delete_a_group()
    {
        $group = create(Group::class);

        $this->actingAsUser()
             ->delete(["groups.destroy", $group])
             ->response()
             ->assertStatus(202);

        $this->assertCount(0, Group::all());
    }
 }
