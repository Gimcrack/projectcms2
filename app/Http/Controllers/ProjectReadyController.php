<?php

namespace App\Http\Controllers;

use App\Project;

class ProjectReadyController extends Controller
{
    public function store(Project $project)
    {
        $project->ready(true);

        return response()->json([],201);
    }
}
