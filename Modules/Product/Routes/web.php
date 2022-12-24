<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Database\Eloquent\Builder;
use Modules\Storeroom\Entities\StoreroomEntrance;

Route::get('token', function () {

    return \Modules\User\Entities\User::first()->createToken('auth_token')->plainTextToken;
});
