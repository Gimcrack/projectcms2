<?php

namespace App\Http\Controllers;

use App\Image;
use Illuminate\Http\Response;
use App\Events\ImageUploaded;
use App\Http\Requests\NewImageRequest;
use App\Http\Requests\UpdateImageRequest;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return response()->json(Image::all(),200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param NewImageRequest $request
     * @return Response
     */
    public function store(NewImageRequest $request)
    {
        $image = Image::create($request->atts());

        ImageUploaded::dispatch($image);

        return response()->json([],201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Image  $image
     * @return Response
     */
    public function show(Image $image)
    {
        return response()->json($image,200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateImageRequest $request
     * @param  \App\Image $image
     * @return Response
     */
    public function update(UpdateImageRequest $request, Image $image)
    {
        $image->update($request->atts());

        ImageUploaded::dispatch( $image->fresh() );

        return response()->json([],202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Image  $image
     * @return Response
     */
    public function destroy(Image $image)
    {
        $image->delete();

        return response()->json([],202);
    }
}