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
    dd("salam");
    return collect(range(1, 16))->map(function ($item) {
        return "https://cdna.p30download.ir/p30dl-tutorial/Udemy.React.Js.With.Laravel.Build.Complete.PWA.Ecommerce.Project-p30download.com.part" . ($item < 10 ? "0" . $item : $item) . ".rar";
    })->implode("<br/>");
        $data = [
            'name' => 'علی'
        ];
//    return view('factor',$data);
    $pdf = PDF::loadView('factor', $data, [], [
        'format' => [685.98, 396.85],
    ]);
    return $pdf->download(now()->toDateTimeString() . '-test.pdf');
    return \niklasravnsborg\LaravelPdf\Facades\Pdf::loadView('factor', ['name' => "علی"])->download('test.pdf');
    return view('welcome');
});
