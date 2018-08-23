<?php
use App\keyword;
Route::group(['prefix' => '/v1', 'namespace' => 'Api\V1', 'as' => 'api.'], function () {
    Route::get('/keywords', function(){
        return keyword::all();
    });
});
