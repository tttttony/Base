<?php

Route::group(['middleware' => 'guest'], function () {
    // Authentication Routes
    Route::get('login', '\Modules\Base\Http\Controllers\Auth\AuthController@showLoginForm')
        ->name('auth.login');
    Route::post('login', '\Modules\Base\Http\Controllers\Auth\AuthController@login');

    // Socialite Routes
    Route::get('login/{provider}', '\Modules\Base\Http\Controllers\Auth\AuthController@loginThirdParty')
        ->name('auth.provider');

    // Registration Routes
    Route::get('register', '\Modules\Base\Http\Controllers\Auth\AuthController@showRegistrationForm')
        ->name('auth.register');
    Route::post('register', '\Modules\Base\Http\Controllers\Auth\AuthController@register');

    // Confirm Account Routes
    Route::get('account/confirm/{token}', '\Modules\Base\Http\Controllers\Auth\AuthController@confirmAccount')
        ->name('account.confirm');
    Route::get('account/confirm/resend/{token}', '\Modules\Base\Http\Controllers\Auth\AuthController@resendConfirmationEmail')
        ->name('account.confirm.resend');

    // Password Reset Routes
    Route::get('password/reset/{token?}', '\Modules\Base\Http\Controllers\Auth\PasswordController@showResetForm')
        ->name('auth.password.reset');
    Route::post('password/email', '\Modules\Base\Http\Controllers\Auth\PasswordController@sendResetLinkEmail');
    Route::post('password/reset', '\Modules\Base\Http\Controllers\Auth\PasswordController@reset');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', '\Modules\Base\Http\Controllers\DashboardController@index')->name('admin.dashboard');
    Route::get('dashboard', '\Modules\Base\Http\Controllers\DashboardController@index')->name('admin.dashboard');

    Route::get('logout', '\Modules\Base\Http\Controllers\Auth\AuthController@logout')->name('auth.logout');

    // Change Password Routes
    Route::get('password/change', '\Modules\Base\Http\Controllers\Auth\PasswordController@showChangePasswordForm')->name('auth.password.change');
    Route::post('password/change', '\Modules\Base\Http\Controllers\Auth\PasswordController@changePassword')->name('auth.password.update');
});


Route::group(['middleware' => 'access.routeNeedsPermission:view-access-management'], function() {

    /**
     * User Management
     */

    Route::resource('users', '\Modules\Base\Http\Controllers\Access\User\UserController', ['except' => ['show'], 'as' => 'admin.access']);
    Route::get('users/', '\Modules\Base\Http\Controllers\Access\User\UserController@index')->name('admin.access.users.index');

    Route::get('users/deactivated', '\Modules\Base\Http\Controllers\Access\User\UserController@deactivated')->name('admin.access.users.deactivated');
    Route::get('users/deleted', '\Modules\Base\Http\Controllers\Access\User\UserController@deleted')->name('admin.access.users.deleted');
    Route::get('account/confirm/resend/{user_id}', '\Modules\Base\Http\Controllers\Access\User\UserController@resendConfirmationEmail')->name('admin.account.confirm.resend');

    /**
     * Specific User
     */
    Route::group(['prefix' => 'user/{id}', 'where' => ['id' => '[0-9]+']], function () {
        Route::get('delete', '\Modules\Base\Http\Controllers\Access\User\UserController@delete')->name('admin.access.user.delete-permanently');
        Route::get('restore', '\Modules\Base\Http\Controllers\Access\User\UserController@restore')->name('admin.access.user.restore');
        Route::get('mark/{status}', '\Modules\Base\Http\Controllers\Access\User\UserController@mark')->name('admin.access.user.mark')->where(['status' => '[0,1]']);
        Route::get('password/change', '\Modules\Base\Http\Controllers\Access\User\UserController@changePassword')->name('admin.access.user.change-password');
        Route::post('password/change', '\Modules\Base\Http\Controllers\Access\User\UserController@updatePassword')->name('admin.access.user.change-password');
    });

    /**
     * Role Management
     */
    Route::resource('roles', '\Modules\Base\Http\Controllers\Access\Role\RoleController', ['except' => ['show'], 'as' => 'admin']);


    /**
     * Permission Management
     */
    Route::resource('permission-group', '\Modules\Base\Http\Controllers\Access\Permission\PermissionGroupController', ['except' => ['index', 'show'], 'as' => 'admin']);
    Route::resource('permissions', '\Modules\Base\Http\Controllers\Access\Permission\PermissionController', ['except' => ['show'], 'as' => 'admin']);

    Route::group(['prefix' => 'groups'], function() {
        Route::post('update-sort', '\Modules\Base\Http\Controllers\Access\Permission\PermissionGroupController@updateSort')->name('admin.access.roles.groups.update-sort');
    });
});