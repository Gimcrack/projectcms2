<?php

namespace App\Http\Controllers;

use App\Org;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\NewOrgRequest;
use App\Http\Requests\UpdateOrgRequest;

class OrgController extends Controller
{

    /**
     * Get a listing Orgs
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(Org::all(), 200);
    }

    /**
     * Display the specified Org.
     *
     * @param  Org  $org
     * @return \Illuminate\Http\Response
     */
    public function show(Org $org)
    {
        return response()->json($org,200);
    }

    /**
     * Store the new Org
     * @method store
     *
     * @param NewOrgRequest $request
     * @return JsonResponse
     */
    public function store(NewOrgRequest $request)
    {
        Org::create( $request->validated() );

        return response([], 201);
    }

    /**
     * Update the specified Org
     *
     * @param UpdateOrgRequest $request
     * @param Org $org
     * @return JsonResponse
     */
    public function update(UpdateOrgRequest $request, Org $org)
    {
        $org->update( $request->validated() );

        return response([],202);
    }

    /**
     * Destroy the specified Org
     *
     * @param Org $org
     * @return JsonResponse
     */
    public function destroy(Org $org)
    {
        $org->delete();

        return response([], 202);
    }
}
