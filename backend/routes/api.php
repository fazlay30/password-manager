<?php

use App\Http\Controllers\GroupProjectController;
use App\Http\Controllers\GroupProjectCredentialController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserCredentialController;
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

Route::get('group-projects/verify-invite/{user_id}/{invitation_token}', [GroupProjectController::class, 'verifyInvitation'])
    ->middleware(['throttle:6,1'])
    ->name('group-projects.invite.verify');

Route::middleware(['auth:sanctum'])->group(function () {
    // Authenticated User
    Route::group(['controller' => UserController::class, 'prefix' => 'user', 'as' => 'user.'], function () {
        Route::get('/', 'show')->name('show');
        Route::post('/profile-update', 'profileUpdate')->name('profileUpdate');
        Route::post('/change-password', 'changePassword')->name('changePassword');
        Route::get('/delete-account', 'deleteAccount')->name('deleteAccount');
    });

    // User Notifications
    Route::group(['controller' => NotificationController::class, 'prefix' => 'user/notifications', 'as' => 'user.notifications.'], function () {
        Route::get('/all', 'all')->name('all');
        Route::get('/unread', 'unread')->name('unread');
        Route::get('/read', 'read')->name('read');
        Route::get('/mark-as-read', 'markAsRead')->name('markAsRead');
        Route::get('/delete', 'delete')->name('delete');
    });

    // Role
    Route::group(['controller' => RoleController::class, 'prefix' => 'roles', 'as' => 'roles.'], function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{role}', 'show')->name('show');
        Route::post('update/{role}', 'update')->name('update');
    });

    // User Credential
    Route::group(['controller' => UserCredentialController::class, 'prefix' => 'user-credentials', 'as' => 'user-credentials.'], function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{user_credential}', 'show')->name('show');
        Route::post('update/{user_credential}', 'update')->name('update');
        Route::post('delete/{user_credential}', 'destroy')->name('destroy');
        Route::get('search/{query}', 'search')->name('search');
    });

    //Group Projects
    Route::group(['controller' => GroupProjectController::class, 'prefix' => 'group-projects', 'as' => 'group-projects.'], function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{group_project}', 'show')->name('show');
        Route::post('update/{group_project}', 'update')->name('update');
        Route::post('delete/{group_project}', 'destroy')->name('destroy');

        Route::post('invite/{group_project}', 'sendInvitation')
            ->middleware(['throttle:6,1'])
            ->name('invite.send');

        Route::get('member-list/{group_project}', 'memberList')->name('memberList');
        Route::get('leave/{group_project}', 'leaveProject')->name('leaveProject');

        Route::get('search/{query}', 'search')->name('search');

        // Action History
        Route::get('/{group_project}/action-histories', 'actionHistories')->name('actionHistories');
    });

    // Group Project Credential
    Route::group(['controller' => GroupProjectCredentialController::class, 'prefix' => 'group-project/{group_project}/credentials', 'as' => 'group-project.credentials.'], function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{project_credential}', 'show')->name('show');
        Route::post('update/{project_credential}', 'update')->name('update');
        Route::post('delete/{project_credential}', 'destroy')->name('destroy');
    });
});
