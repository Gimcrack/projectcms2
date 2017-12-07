<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\NewProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Project;
use Illuminate\Http\Request;

class CategoryProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Category $category
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
        return response()->json($category->projects, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param NewProjectRequest $request
     * @param Category $category
     * @return \Illuminate\Http\Response
     */
    public function store(NewProjectRequest $request, Category $category)
    {
        $category->projects()->save( Project::create( $request->validated() ) );

        return response()->json([],201);
    }

    /**
     * Display the specified resource.
     *
     * @param Category $category
     * @param Project $project
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category, Project $project)
    {
        return response()->json( $category->projects()->findOrFail($project->id), 200 );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProjectRequest $request
     * @param Category $category
     * @param Project $project
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProjectRequest $request, Category $category, Project $project)
    {
        $category->projects()->findOrFail($project->id)->update( $request->validated() );

        return response()->json([],202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @param Project $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category, Project $project)
    {
        $category->projects()->findOrFail($project->id)->delete();

        return response()->json([],202);
    }
}
