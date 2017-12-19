<?php

namespace Tests\Feature;

use App\Tag;
use function make_array;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_can_get_a_listing_of_the_resource()
    {
        $tags = create(Tag::class,3);

        $this->actingAsUser()
            ->get("tags.index")
            ->response()
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonModelCollection($tags);
    }

    /** @test */
    public function it_can_get_a_single_resource()
    {
        $tag = create(Tag::class);

        $this->actingAsUser()
            ->get(["tags.show",$tag])
            ->response()
            ->assertStatus(200)
            ->assertJsonModel($tag);
    }

    /** @test */
    public function it_can_store_a_new_tag()
    {
        $atts = make_array(Tag::class);

        $this->actingAsUser()
            ->post("tags.store",$atts)
            ->response()
            ->assertStatus(201);

        $this->assertDatabaseHas('tags',$atts);
    }

    /** @test */
    public function it_wont_store_a_new_tag_without_a_name()
    {
        $atts = make_array(Tag::class, [
            'name' => null
        ]);

        $this->actingAsUser()
             ->post("tags.store",$atts)
             ->response()
             ->assertStatus(422)
             ->assertJsonValidationErrors('name');

        $this->assertDatabaseMissing('tags',$atts);
    }

    /** @test */
    public function it_wont_store_a_new_tag_with_a_duplicate_name()
    {
        create(Tag::class, ['name' => 'Tag Name']);


        $atts = make_array(Tag::class, [
            'name' => 'Tag Name'
        ]);

        $this->actingAsUser()
             ->post("tags.store",$atts)
             ->response()
             ->assertStatus(422)
             ->assertJsonValidationErrors('name');
    }

    /** @test */
    public function it_can_update_a_tag()
    {
        $tag = create(Tag::class, ['name' => 'Tag Name']);

        $this->actingAsUser()
            ->patch(["tags.update", $tag], ['name' => 'New Name'])
            ->response()
            ->assertStatus(202);

        $this->assertDatabaseHas('tags',['name' => 'New Name']);
    }

    /** @test */
    public function it_can_delete_a_tag()
    {
        $tag = create(Tag::class);

        $this->actingAsUser()
             ->delete(["tags.destroy", $tag])
             ->response()
             ->assertStatus(202);

        $this->assertCount(0, Tag::all());
    }
 }
