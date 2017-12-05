<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteProjectApprovalRequest;
use App\Project;
use App\Http\Requests\NewProjectApprovalRequest;

class ProjectApprovalController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  NewProjectApprovalRequest  $request
     * @param  Project  $project
     * @return \Illuminate\Http\Response
     */
    public function store( NewProjectApprovalRequest $request, Project $project )
    {
        $project->approveBy($request->user());

        return response()->json([],201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteProjectApprovalRequest $request
     * @param Project $project
     * @return \Illuminate\Http\Response
     */
    public function destroy( DeleteProjectApprovalRequest $request, Project $project)
    {
        $project->unapprove();

        return response()->json([],202);
    }
}
