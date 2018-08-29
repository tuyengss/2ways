<?php
use App\keyword;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;

Route::group(['prefix' => '/v1', 'namespace' => 'Api\V1', 'as' => 'api.'], function () {
    Route::get('/keywords', function(){
        return keyword::all();
    });

    Route::post('/fiboMoRecieves', function(Request $request){
        return HomeController::moGateWay($request);
    });
});
