<?php
use App\keyword;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\SendSmsController;
use Illuminate\Http\Request;

Route::group(['prefix' => '/v1', 'namespace' => 'Api\V1', 'as' => 'api.'], function () {
    Route::get('/keywords', function(){
        return keyword::all();
    });

    Route::get('/fiboMoRecieves', function(Request $request){
        return HomeController::moGateWay($request);
    });

    Route::get('/2waysRecieves', function(Request $request){
	$sms = new SendSmsController();
        return $sms->moGateWay($request);
    });

});
