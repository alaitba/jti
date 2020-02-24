<?php


use Illuminate\Support\Facades\Route;

Route::group(['prefix' => config('project.admin_prefix'), 'middleware' => ['web']], function () {
    Route::get('login', 'AuthController@getLogin')->name('admin.get.login');
    Route::post('login', 'AuthController@postLogin')->name('admin.post.login');
    Route::get('logout', 'AuthController@logout')->name('admin.logout');
});

Route::group(['prefix' => config('project.admin_prefix'), 'middleware' => ['web', 'adminMiddleware']], function () {
    Route::get('/', 'HomeController@index')->name('admin');
//    Route::group(['prefix' => 'users/admins/profile'], function () {
//        Route::get('/', 'Backend\Users\Admins\AdminProfileController@profile')->name('admin.users.admins.profile');
//        Route::post('update', 'Backend\Users\Admins\AdminProfileController@update')->name('admin.users.admins.profile.update');
//    });

    Route::group(['prefix' => 'admins', 'middleware' => ['adminPermissionMiddleware:manage_admins']], function () {
        Route::get('/', 'AdminController@index')->name('admin.admins');
        Route::get('get-list', 'AdminController@getList')->name('admin.admins.list');
        Route::get('create', 'AdminController@create')->name('admin.admins.create');
        Route::post('store', 'AdminController@store')->name('admin.admins.store');
        Route::get('{id}/edit', 'AdminController@edit')->name('admin.admins.edit');
        Route::post('{id}/update', 'AdminController@update')->name('admin.admins.update');
        Route::get('{id}/delete', 'AdminController@delete')->name('admin.admins.delete');

        Route::group(['prefix' => 'roles'], function () {
            Route::get('/', 'RoleController@index')->name('admin.admins.roles');
            Route::get('get-list', 'RoleController@getList')->name('admin.admins.roles.list');
            Route::get('create', 'RoleController@create')->name('admin.admins.roles.create');
            Route::post('store', 'RoleController@store')->name('admin.admins.roles.store');
            Route::get('{roleId}/edit', 'RoleController@edit')->name('admin.admins.roles.edit');
            Route::post('{roleId}/update', 'RoleController@update')->name('admin.admins.roles.update');
            Route::get('{roleId}/delete', 'RoleController@delete')->name('admin.admins.roles.delete');
            Route::get('api/all', 'RoleController@getAllRoles')->name('admin.admins.roles.all');
        });

        Route::group(['prefix' => 'permissions'], function () {
            Route::get('/', 'PermissionController@index')->name('admin.admins.permissions');
            Route::get('get-list', 'PermissionController@getList')->name('admin.admins.permissions.list');
            Route::get('create', 'PermissionController@create')->name('admin.admins.permissions.create');
            Route::post('store', 'PermissionController@store')->name('admin.admins.permissions.store');
            Route::get('{permissionId}/edit', 'PermissionController@edit')->name('admin.admins.permissions.edit');
            Route::post('{permissionId}/update', 'PermissionController@update')->name('admin.admins.permissions.update');
            Route::get('{permissionId}/delete', 'PermissionController@delete')->name('admin.admins.permissions.delete');
            Route::get('api/all', 'PermissionController@getAllPermissions')->name('admin.admins.permissions.all');
        });
    });

    /**
     * Отчеты
     */
    Route::group(['prefix' => 'reports', 'as' => 'admin.reports'], function () {
        /**
         * Sales plan
         */
        Route::group(['prefix' => 'sales-plan', 'as' => '.sales_plan'], function () {
            Route::get('/', 'SalesPlanController@index')->name('.index');
            Route::match(['get', 'post'], 'get-list', 'SalesPlanController@getList')->name('.list');
        });

        /**
         * Registered partners
         */
        Route::group(['prefix' => 'partners', 'as' => '.partners'], function () {
            Route::get('/', 'PartnersReportController@index')->name('.index');
            Route::get('get-list', 'PartnersReportController@getList')->name('.list');
        });
    });

    /**
     * Призы
     */
    Route::group(['prefix' => 'rewards', 'as' => 'admin.rewards'], function () {
        Route::get('/', 'RewardsController@index')->name('.index');
        Route::match(['get', 'post'], 'get-list', 'RewardsController@getList')->name('.list');
        Route::get('{id}/edit', 'RewardsController@edit')->name('.edit');
        Route::post('{id}/update', 'RewardsController@update')->name('.update');
        Route::post('{rewardId}/media', 'RewardsController@media')->name('.media');
        Route::get('{rewardId}/media-delete', 'RewardsController@deleteMedia')->name('.media.delete');
    });

    /**
     * Новости
     */
    Route::group(['prefix' => 'news', 'as' => 'admin.news'], function () {
        Route::get('/', 'NewsController@index')->name('.index');
        Route::get('create', 'NewsController@create')->name('.create');
        Route::post('store', 'NewsController@store')->name('.store');
        Route::get('list', 'NewsController@getList')->name('.list');
        Route::get('{id}/edit', 'NewsController@edit')->name('.edit');
        Route::post('{id}/update', 'NewsController@update')->name('.update');
        Route::get('{id}/delete', 'NewsController@delete')->name('.delete');
        Route::post('{newsId}/media', 'NewsController@media')->name('.media');
        Route::get('{newsId}/media-delete', 'NewsController@deleteMedia')->name('.media.delete');
    });

    /**
     * Пуши из админки
     */
    Route::group(['prefix' => 'notifications', 'as' => 'admin.notifications'], function () {
        Route::group(['prefix' => 'all', 'as' => '.all'], function () {
            Route::get('/', 'NotificationsController@indexAll')->name('.index');
        });
        Route::group(['prefix' => 'bylist', 'as' => '.bylist'], function () {
            Route::get('/', 'NotificationsController@indexByList')->name('.index');
        });
    });
});

