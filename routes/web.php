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
/*
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
*/
    /**
     * Шаблоны писем
     */
/*    Route::group(['prefix' => 'email-templates'], function () {
        Route::get('/', 'Settings\EmailTemplateController@index')->name('admin.email_templates.index');
        Route::get('{id}/edit', 'Settings\EmailTemplateController@edit')->name('admin.email_templates.edit');
        Route::post('{id}/update', 'Settings\EmailTemplateController@update')->name('admin.email_templates.update');

        Route::get('create', 'Settings\EmailTemplateController@create')->name('admin.email_templates.create');
        Route::post('store', 'Settings\EmailTemplateController@store')->name('admin.email_templates.store');
    });
*/
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

        /**
         * Опросы
         */
        Route::group(['prefix' => 'polls', 'as' => '.polls'], function () {
            Route::get('/', 'PollReportController@index')->name('.index');
            Route::match(['get', 'post'], 'get-list', 'PollReportController@getList')->name('.list');
            Route::get('{id}/view', 'PollReportController@view')->name('.view');
        });

        /**
         * Викторины
         */
        Route::group(['prefix' => 'quizzes', 'as' => '.quizzes'], function () {
            Route::get('/', 'QuizReportController@index')->name('.index');
            Route::match(['get', 'post'], 'get-list', 'QuizReportController@getList')->name('.list');
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
        Route::get('/', 'NotificationsController@index')->name('.index');
        Route::get('get-list', 'NotificationsController@getList')->name('.list');
        Route::get('create', 'NotificationsController@create')->name('.create');
        Route::post('store', 'NotificationsController@store')->name('.store');
        Route::get('get-users', 'NotificationsController@getUsers')->name('.users');
        Route::get('get-file/{id}', 'NotificationsController@getFile')->name('.custom-file');
    });

    /**
     * Календарь праздников
     */
    Route::group(['prefix' => 'holidays', 'as' => 'admin.holidays'], function () {
        Route::get('/', 'HolidaysController@index')->name('.index');
        Route::post('update', 'HolidaysController@update')->name('.update');
    });

    /**
     * Табачная продукция
     */
    Route::group(['prefix' => 'tobacco-products', 'as' => 'admin.tobacco_products'], function () {
        Route::get('/', 'TobaccoProductsController@index')->name('.index');
        Route::match(['get', 'post'], 'get-list', 'TobaccoProductsController@getList')->name('.list');
        Route::get('{id}/edit', 'TobaccoProductsController@edit')->name('.edit');
        Route::post('{rewardId}/media', 'TobaccoProductsController@media')->name('.media');
        Route::get('{rewardId}/media-delete', 'TobaccoProductsController@deleteMedia')->name('.media.delete');
    });

    /**
     * Бренды (картинки)
     */
    Route::group(['prefix' => 'brands', 'as' => 'admin.brands'], function () {
        Route::get('/', 'BrandsController@index')->name('.index');
        Route::match(['get', 'post'], 'get-list', 'BrandsController@getList')->name('.list');
        Route::get('{id}/edit', 'BrandsController@edit')->name('.edit');
        Route::post('{brandId}/media', 'BrandsController@media')->name('.media');
        Route::get('{brandId}/media-delete', 'BrandsController@deleteMedia')->name('.media.delete');
    });

    /**
     * Анкеты
     */
    Route::group(['prefix' => 'quizzes', 'as' => 'admin.quizzes'], function () {
        Route::get('/', 'QuizController@index')->name('.index');
        Route::match(['get', 'post'], 'get-list', 'QuizController@getList')->name('.list');
        Route::get('create', 'QuizController@create')->name('.create');
        Route::post('store', 'QuizController@store')->name('.store');
        Route::get('{id}/edit', 'QuizController@edit')->name('.edit');
        Route::post('{id}/update', 'QuizController@update')->name('.update');
        Route::get('get-file/{id}', 'QuizController@getFile')->name('.custom-file');
        Route::get('{mediaId}/media-delete', 'QuizController@deleteMedia')->name('.media.delete');
        Route::get('{id}/delete', 'QuizController@delete')->name('.delete');
        Route::group(['prefix' => '{quizId}/questions', 'as' => '.questions'], function () {
            Route::get('/', 'QuizController@questions')->name('.index');
            Route::get('list', 'QuizController@questionsList')->name('.list');
            Route::get('create', 'QuizController@createQuestion')->name('.create');
            Route::post('store', 'QuizController@storeQuestion')->name('.store');
            Route::get('{id}/edit', 'QuizController@editQuestion')->name('.edit');
            Route::post('{id}/update', 'QuizController@updateQuestion')->name('.update');
            Route::get('{id}/delete', 'QuizController@deleteQuestion')->name('.delete');
            Route::get('{mediaId}/media-delete', 'QuizController@deleteQuestionMedia')->name('.media.delete');
        });
    });

});


//Webhook
Route::webhooks('webhook');
