<?php

Route::group(['prefix' => '/v1', 'namespace' => 'Api\V1', 'as' => 'api.'], function () {
    Route::resource('companies', 'CompaniesController', ['except' => ['create', 'edit']]);
});

Route::group(['prefix' => '/v1', 'namespace' => 'Api\V1', 'as' => 'api.'], function () {
    Route::post('log', ['as' => 'log', 'uses' => 'HomeController@log']);
    Route::group(['prefix' => '/specimen', 'as' => 'specimen.'], function () {
        Route::post('create', ['as' => 'create', 'uses' => 'HomeController@store']);
        Route::get('all', ['as' => 'all', 'uses' => 'HomeController@all']);
    });
});
