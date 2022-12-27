<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    $data = [
        'name' => 'علی'
    ];
//    return view('factor',$data);
    $pdf = PDF::loadView('factor', $data, [], [
        'format' => [685.98, 396.85],
    ]);
    return $pdf->download(now()->toDateTimeString().'-test.pdf');
    return \niklasravnsborg\LaravelPdf\Facades\Pdf::loadView('factor', ['name' => "علی"])->download('test.pdf');
    return view('welcome');
});
