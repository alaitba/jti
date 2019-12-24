<?php


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

    Route::group(['prefix' => 'settings/localisation'], function () {
        Route::get('groups', 'Settings\LocalisationController@groups')->name('admin.settings.localisation');
        Route::get('groupsGetList', 'Settings\LocalisationController@groupList')->name('admin.settings.localisation.groups.list');
        Route::get('groups/create', 'Settings\LocalisationController@groupCreate')->name('admin.settings.localisation.groups.create');
        Route::post('groups/store', 'Settings\LocalisationController@groupStore')->name('admin.settings.localisation.groups.store');
        Route::get('groups/{groupId}/edit', 'Settings\LocalisationController@groupEdit')->name('admin.settings.localisation.groups.edit');
        Route::post('groups/{groupId}/update', 'Settings\LocalisationController@groupUpdate')->name('admin.settings.localisation.groups.update');

        Route::group(['prefix' => 'groups/{groupId}'], function () {
            Route::get('items', 'Settings\LocalisationController@groupsItems')->name('admin.settings.localisation.groups.items');
            Route::get('items/getList', 'Settings\LocalisationController@groupsItemsList')->name('admin.settings.localisation.groups.items.list');
            Route::get('items/create', 'Settings\LocalisationController@groupsItemsCreate')->name('admin.settings.localisation.groups.items.create');
            Route::post('items/store', 'Settings\LocalisationController@groupsItemsStore')->name('admin.settings.localisation.groups.items.store');
            Route::get('items/{itemId}/show', 'Settings\LocalisationController@groupsItemsShow')->name('admin.settings.localisation.groups.items.show');
            Route::get('items/{itemId}/edit', 'Settings\LocalisationController@groupsItemsEdit')->name('admin.settings.localisation.groups.items.edit');
            Route::post('items/{itemId}/update', 'Settings\LocalisationController@groupsItemsUpdate')->name('admin.settings.localisation.groups.items.update');
        });
    });

    /**
     * Шаблоны писем
     */
    Route::group(['prefix' => 'email-templates'], function () {
        Route::get('/', 'Settings\EmailTemplateController@index')->name('admin.email_templates.index');
        Route::get('{id}/edit', 'Settings\EmailTemplateController@edit')->name('admin.email_templates.edit');
        Route::post('{id}/update', 'Settings\EmailTemplateController@update')->name('admin.email_templates.update');

        Route::get('create', 'Settings\EmailTemplateController@create')->name('admin.email_templates.create');
        Route::post('store', 'Settings\EmailTemplateController@store')->name('admin.email_templates.store');
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

    });
});



/**
 * Front
 */

Route::group(['prefix' => '/auth', 'middleware' => ['web']], function () {
    Route::post('phone', 'Front\AuthController@postPhone')->name('partner.post.phone');
    Route::get('logout', 'Front\AuthController@logout')->name('partner.logout');
});
