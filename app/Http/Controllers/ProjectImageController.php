<?php

namespace App\Http\Controllers;

use App\Image;
use App\Project;
use Illuminate\Http\Response;
use App\Events\ImageUploaded;
use App\Http\Requests\NewImageRequest;
use App\Http\Requests\UpdateImageRequest;

class ProjectImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Project $project
     * @return \Illuminate\Http\Response
     */
    public function index(Project $project)
    {
        return response()->json( $project->images, 200 );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param NewImageRequest $request
     * @param Project $project
     * @return \Illuminate\Http\Response
     */
    public function store(NewImageRequest $request, Project $project)
    {
        $project->images()->save( $image = Image::create($request->atts()) );

        ImageUploaded::dispatch($image);

        return response()->json([],201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Project $project
     * @param  Image $image
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project, Image $image)
    {
        return response()->json($project->images()->find($image),200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateImageRequest $request
     * @param  Project $project
     * @param  Image $image
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateImageRequest $request, Project $project, Image $image)
    {
        $project->images()->findOrFail($image->id)->update($request->atts());

        ImageUploaded::dispatch( $image->fresh() );

        return response()->json([],202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Project $project
     * @param  Image $image
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project, Image $image)
    {
        $project->images()->findOrFail($image->id)->delete();

        return response()->json([],202);
    }
}