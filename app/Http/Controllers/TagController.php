<?php

namespace App\Http\Controllers;

use App\Tag;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\NewTagRequest;
use App\Http\Requests\UpdateTagRequest;

class TagController extends Controller
{

    /**
     * Get a listing Tags
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(Tag::all(), 200);
    }

    /**
     * Display the specified Tag.
     *
     * @param  Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $tag)
    {
        return response()->json($tag,200);
    }

    /**
     * Store the new Tag
     * @method store
     *
     * @param NewTagRequest $request
     * @return JsonResponse
     */
    public function store(NewTagRequest $request)
    {
        Tag::create( $request->validated() );

        return response([], 201);
    }

    /**
     * Update the specified Tag
     *
     * @param UpdateTagRequest $request
     * @param Tag $tag
     * @return JsonResponse
     */
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $tag->update( $request->validated() );

        return response([],202);
    }

    /**
     * Destroy the specified Tag
     *
     * @param Tag $tag
     * @return JsonResponse
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response([], 202);
    }
}
