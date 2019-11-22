<?php

use Illuminate\Http\Request;

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

Route::prefix('v1')->middleware(['response.json'])->group(function () {

    Route::middleware(['auth:api'])->group(function (){
        Route::middleware(['auth:role:provider', 'auth:role:manager'])->group(function (){ // заготовка для provider и :manager

        });
//        Route::get('/calibration', 'CalibrationController@index')->name('home');
//        Route::get('/calibration/{usercal_id?}', 'CalibrationController@index')->where('usercal_id', '[0-9]+')->name('homecal'); // где usercal_id проверяется на регулярное выражение
//        Route::patch('/calibration', 'CalibrationController@update')->name('homecal_update'); // где usercal_id проверяется на регулярное выражение
//        Route::post('/calibration', 'CalibrationController@store')->name('homecal_store'); //
//        Route::get('/calibration/trail/{usercal_id?}', 'CalibrationController@show')->name('homecal_show'); //

        Route::get('calibration/start-calculate-vectors/', 'Api\CalibrationController@startCalculateVectors');
        Route::resource('calibration', 'Api\CalibrationController');

        Route::get('provider/init', 'Api\ProviderController@init')->name('provider.init');
        Route::post('provider/data', 'Api\ProviderController@data')->name('provider.data');
        Route::get('provider/exportExcel', 'Api\ProviderController@exportExcel')->name('provider.exportExcel');
        Route::resource('provider', 'Api\ProviderController');

        Route::get('provider-pos/init', 'Api\ProviderPosController@init')->name('provider-pos.init');
        Route::post('provider-pos/data', 'Api\ProviderPosController@data')->name('provider-pos.data');
        Route::get('provider-pos/exportExcel', 'Api\ProviderPosController@exportExcel')->name('providerPos.exportExcel');
        Route::resource('provider-pos', 'Api\ProviderPosController');

        Route::get('provider-pos-payment/init', 'Api\ProviderPosPaymentController@init')->name('provider-pos-payment.init');
        Route::post('provider-pos-payment/data', 'Api\ProviderPosPaymentController@data')->name('provider-pos-payment.data');
        Route::get('provider-pos-payment/exportExcel', 'Api\ProviderPosPaymentController@exportExcel')->name('providerPos-payment.exportExcel');
        Route::get('provider-pos-payment/balance', 'Api\ProviderPosPaymentController@balance')->name('provider-pos-payment.balance');
        Route::resource('provider-pos-payment', 'Api\ProviderPosPaymentController');

        Route::get('device/init', 'Api\DeviceController@init')->name('device.init');
        Route::post('device/data', 'Api\DeviceController@data')->name('device.data');
        Route::get('device/exportExcel', 'Api\DeviceController@exportExcel')->name('device.exportExcel');
        Route::resource('device', 'Api\DeviceController');
        Route::post('device/{id}/tracks', 'Api\DeviceController@tracks')->name('device.tracks');

        Route::get('device-tariff/init', 'Api\DeviceTariffController@init')->name('device-tariff.init');
        Route::post('device-tariff/data', 'Api\DeviceTariffController@data')->name('device-tariff.data');
        Route::get('device-tariff/exportExcel', 'Api\DeviceTariffController@exportExcel')->name('deviceTariff.exportExcel');
        Route::resource('device-tariff', 'Api\DeviceTariffController');
        Route::resource('operation', 'Api\OperationController');

        Route::get('report-trackers/init', 'Api\ReportTrackersController@init')->name('reportTrackers.init');
        Route::post('report-trackers/data', 'Api\ReportTrackersController@data')->name('reportTrackers.data');
        Route::get('report-trackers/exportExcel', 'Api\ReportTrackersController@exportExcel')->name('reportTrackers.exportExcel');

        Route::get('report-trackers-details/init', 'Api\ReportTrackersDetailsController@init')->name('reportTrackersDetails.init');
        Route::post('report-trackers-details/data', 'Api\ReportTrackersDetailsController@data')->name('reportTrackersDetails.data');
        Route::get('report-trackers-details/exportExcel', 'Api\ReportTrackersDetailsController@exportExcel')->name('reportTrackersDetails.exportExcel');
        Route::get('report-trackers-details/tracks/{id}', 'Api\ReportTrackersDetailsController@tracks')->name('reportTrackersDetails.tracks');

        Route::resource('coordinates', 'Api\CoordinatesController');
        Route::resource('sensors', 'Api\SensorsController');
        Route::resource('users', 'Api\UsersController');
        Route::resource('tracker-session', 'Api\TrackerSessionController');

        Route::post('passport/get-details', 'Api\PassportController@getDetails');
        Route::post('passport/user-list', 'Api\PassportController@userList');
    });

    Route::post('passport/login', 'Api\PassportController@login');
    Route::post('passport/register', 'Api\PassportController@register');

    Route::post('coordinates/via-alias', 'Api\CoordinatesController@viaAlias')->name('coordinates.viaAlias');

    Route::get('device/validate-alias/{alias}', 'Api\DeviceController@validateAlias')->name('device.validateAlias');

});