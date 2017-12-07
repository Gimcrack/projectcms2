<?php

use Illuminate\Support\Facades\Route;

Route::prefix('projects/{project}')->group( function() {
    Route::name("projects.approval.store")->post("approve","ProjectApprovalController@store");
    Route::name("projects.approval.destroy")->delete("approve","ProjectApprovalController@destroy");

    Route::name("projects.publish.store")->post("publish","ProjectPublishController@store");
    Route::name("projects.publish.destroy")->delete("publish","ProjectPublishController@destroy");

    Route::name("projects.ready.store")->post("ready","ProjectReadyController@store");
});

Route::apiResource("projects.images","ProjectImageController");
Route::apiResource("categories.projects","CategoryProjectController");

Route::apiResource("images","ImageController");
Route::apiResource("projects","ProjectController");
Route::apiResource("categories","CategoryController");
