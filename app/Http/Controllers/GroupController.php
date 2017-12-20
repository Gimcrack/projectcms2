<?php

namespace App\Http\Controllers;

use App\Group;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\NewGroupRequest;
use App\Http\Requests\UpdateGroupRequest;

class GroupController extends Controller
{

    /**
     * Get a listing Groups
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(Group::all(), 200);
    }

    /**
     * Display the specified Group.
     *
     * @param  Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        return response()->json($group,200);
    }

    /**
     * Store the new Group
     * @method store
     *
     * @param NewGroupRequest $request
     * @return JsonResponse
     */
    public function store(NewGroupRequest $request)
    {
        Group::create( $request->validated() );

        return response([], 201);
    }

    /**
     * Update the specified Group
     *
     * @param UpdateGroupRequest $request
     * @param Group $group
     * @return JsonResponse
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {
        $group->update( $request->validated() );

        return response([],202);
    }

    /**
     * Destroy the specified Group
     *
     * @param Group $group
     * @return JsonResponse
     */
    public function destroy(Group $group)
    {
        $group->delete();

        return response([], 202);
    }
}
