<?php

use Illuminate\Support\Facades\Route;

Route::name("users.promotion.store")->post("users/{user}/promotion", "UserPromotionController@store");
Route::name("users.promotion.destroy")->delete("users/{user}/promotion", "UserPromotionController@destroy");

Route::apiResource('users','UserController');





