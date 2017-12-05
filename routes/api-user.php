<?php

use Illuminate\Support\Facades\Route;

Route::prefix('projects/{project}')->group( function() {
    Route::post("approval","ProjectApprovalController@store");
    Route::delete("approval","ProjectApprovalController@destroy");

    Route::post("publish","ProjectPublishController@store");
    Route::delete("publish","ProjectPublishController@destroy");
});

Route::apiResource('projects','ProjectController');
