<?php

Route::group(['prefix' => 'api/v1'], function () {
    Route::resource('installs', 'Wwrf\MobileApp\Http\Installs');
});

Route::group(['prefix' => 'api/v1/account'], function () {
    Route::post('signin', 'Wwrf\MobileApp\Http\Account@signin');
    Route::post('register', 'Wwrf\MobileApp\Http\Account@register');
});