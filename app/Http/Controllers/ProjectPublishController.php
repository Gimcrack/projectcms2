<?php

namespace App\Http\Controllers;

use App\Project;
use App\Http\Requests\NewProjectPublishRequest;
use App\Http\Requests\DeleteProjectPublishRequest;

class ProjectPublishController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  NewProjectPublishRequest $request
     * @param  Project  $project
     * @return \Illuminate\Http\Response
     */
    public function store( NewProjectPublishRequest $request, Project $project )
    {
        $project->publish($request['published_at']);

        return response()->json([],201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\DeleteProjectPublishRequest $request
     * @param Project $project
     * @return \Illuminate\Http\Response
     */
    public function destroy( DeleteProjectPublishRequest $request, Project $project)
    {
        $project->unpublish($request['unpublished_at']);

        return response()->json([],202);
    }
}
