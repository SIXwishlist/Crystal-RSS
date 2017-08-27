<?php

Route::group(['middleware' => ['menus']], function () {
    Route::get('/', 'HomeController@index')->name('home.index');
    Route::get('/login', 'HomeController@login')->name('home.login');

    /* ****************
     * Logged on users *
     **************** */
    Route::group(['middleware' => ['auth']], function () {
        Route::resource('manage_feeds', 'FeedManagerController', ['except' => 'show']);
        Route::put('update_feeds', 'HomeController@updateFeeds')->name('home.update_feeds');
    });

    Auth::routes();
});