<?php

namespace Tests\Unit;

use App\Tag;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_have_a_name()
    {
        create(Tag::class, [
            'name' => null
        ]);
    }

    /**
     * @test
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_have_a_unique_name()
    {
        create(Tag::class, [
            'name' => 'Tag Name'
        ]);

        create(Tag::class, [
            'name' => 'Tag Name'
        ]);
    }
 }
