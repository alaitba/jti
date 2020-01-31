<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['prefix' => 'v1', 'middleware' => 'api'], function () {

    /**
     * Auth
     */
    Route::group(['prefix' => 'auth'], function () {
        Route::post('phone', 'Front\AuthController@postPhone');
        Route::post('sms-code', 'Front\AuthController@postSmsCode');
        Route::post('create-password', 'Front\AuthController@postCreatePassword');
        Route::post('login', 'Front\AuthController@postLogin');
        Route::get('logout', 'Front\AuthController@logout');

        /**
         * Password reset
         */
        Route::group(['prefix' => 'reset'], function () {
            Route::post('phone', 'Front\AuthController@postPhoneReset');
            Route::post('sms-code', 'Front\AuthController@postSmsCodeReset');
            Route::post('create-password', 'Front\AuthController@postCreatePasswordReset');
        });

        /**
         * Refresh token
         */
        Route::post('refresh', 'Front\AuthController@refresh')->middleware('partnerMiddleware:true');

        /**
         * Set tradepoint
         */
        Route::post('set-tradepoint', 'Front\AuthController@postSetTradePoint')->middleware('partnerMiddleware:true');
    });

    /**
     * Анкеты
     */
    Route::group(['prefix' => 'client', 'middleware' => 'partnerMiddleware'], function () {
        Route::post('send-sms', 'Front\ClientController@sendSms');
        Route::post('check-sms', 'Front\ClientController@checkSms');
        Route::post('create-lead', 'Front\ClientController@createLead');
        Route::get('lead-history', 'Front\ClientController@getLeadHistory');
    });

    /**
     * Справочники
     */
    Route::group(['prefix' => 'dict', 'middleware' => 'partnerMiddleware'], function () {
        Route::get('tobacco-products', 'Front\DictionaryController@getTobaccoProducts');
    });

    /**
     * Призы
     */
    Route::group(['prefix' => 'rewards', 'middleware' => 'partnerMiddleware'], function () {
        Route::get('balance', 'Front\RewardsController@getBalance');
        Route::get('available', 'Front\RewardsController@getAvailableRewards');
        Route::get('get', 'Front\RewardsController@createReward');
    });

    /**
     * Ноости
     */
    Route::group(['prefix' => 'news', 'middleware' => 'partnerMiddleware'], function () {
        Route::get('/', 'Front\NewsController@getNews');
    });

});
