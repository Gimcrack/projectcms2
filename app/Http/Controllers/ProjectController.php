<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Project::all(),200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param NewProjectRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(NewProjectRequest $request)
    {
        Project::create( $request->validated() );

        return response()->json([],201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Project $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        return response()->json($project,200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateProjectRequest $request
     * @param  Project $project
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update($request->validated());

        return response()->json([],202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Project $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json([],202);
    }
}
