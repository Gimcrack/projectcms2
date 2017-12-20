<?php

namespace Tests\Feature;

use App\Org;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrgTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_can_get_a_listing_of_orgs()
    {
        $orgs = create(Org::class,3);

        $this->actingAsUser()
            ->get("orgs.index")
            ->response()
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonModelCollection($orgs);
    }

    /** @test */
    public function it_can_get_a_single_org()
    {
        $org = create(Org::class);

        $this->actingAsUser()
            ->get(["orgs.show",$org])
            ->response()
            ->assertStatus(200)
            ->assertJsonModel($org);
    }

    /** @test */
    public function it_can_store_a_new_org()
    {
        $atts = make_array(Org::class);

        $this->actingAsUser()
            ->post("orgs.store",$atts)
            ->response()
            ->assertStatus(201);

        $this->assertDatabaseHas('orgs',$atts);
    }

    /** @test */
    public function it_wont_store_a_new_org_without_a_name()
    {
        $atts = make_array(Org::class, [
            'name' => null
        ]);

        $this->actingAsUser()
             ->post("orgs.store",$atts)
             ->response()
             ->assertStatus(422)
             ->assertJsonValidationErrors('name');

        $this->assertDatabaseMissing('orgs',$atts);
    }

    /** @test */
    public function it_wont_store_a_new_org_with_a_duplicate_name()
    {
        create(Org::class, ['name' => 'Org Name']);


        $atts = make_array(Org::class, [
            'name' => 'Org Name'
        ]);

        $this->actingAsUser()
             ->post("orgs.store",$atts)
             ->response()
             ->assertStatus(422)
             ->assertJsonValidationErrors('name');
    }

    /** @test */
    public function it_can_update_a_org()
    {
        $org = create(Org::class, ['name' => 'Org Name']);

        $this->actingAsUser()
            ->patch(["orgs.update", $org], ['name' => 'New Name'])
            ->response()
            ->assertStatus(202);

        $this->assertDatabaseHas('orgs',['name' => 'New Name']);
    }

    /** @test */
    public function it_can_delete_a_org()
    {
        $org = create(Org::class);

        $this->actingAsUser()
             ->delete(["orgs.destroy", $org])
             ->response()
             ->assertStatus(202);

        $this->assertCount(0, Org::all());
    }
 }
