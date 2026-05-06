<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CongregantController;
use App\Http\Controllers\CongregantServiceTypeController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ServiceTypesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
    Route::get('login', [AuthController::class, 'create'])->name('login');
    Route::post('login', [AuthController::class, 'store']);

    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});

Route::middleware(['auth'])
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home.index');

        Route::delete('activities/bulk-destroy', [ActivityController::class, 'bulkDestroy'])->name('activities.bulk-destroy');
        Route::patch('activities/reorder', [ActivityController::class, 'reorder'])->name('activities.reorder');
        Route::resource('activities', ActivityController::class);

        Route::get('congregants/export', [CongregantController::class, 'export'])->name('congregants.export');
        Route::get('congregants/template', [CongregantController::class, 'downloadTemplate'])->name('congregants.template');
        Route::get('congregants/import', [CongregantController::class, 'importForm'])->name('congregants.import.form');
        Route::post('congregants/import', [CongregantController::class, 'import'])->name('congregants.import');
        Route::delete('congregants/bulk-destroy', [CongregantController::class, 'bulkDestroy'])->name('congregants.bulk-destroy');
        Route::resource('congregants', CongregantController::class);

        Route::get('service_types/export', [ServiceTypesController::class, 'export'])->name('service_types.export');
        Route::get('service_types/template', [ServiceTypesController::class, 'downloadTemplate'])->name('service_types.template');
        Route::get('service_types/import', [ServiceTypesController::class, 'importForm'])->name('service_types.import.form');
        Route::post('service_types/import', [ServiceTypesController::class, 'import'])->name('service_types.import');
        Route::delete('service_types/bulk-destroy', [ServiceTypesController::class, 'bulkDestroy'])->name('service_types.bulk-destroy');
        Route::patch('service_types/reorder', [ServiceTypesController::class, 'reorder'])->name('service_types.reorder');
        Route::resource('service_types', ServiceTypesController::class);

        Route::get('congregant_services/export', [CongregantServiceTypeController::class, 'export'])->name('congregant_services.export');
        Route::get('congregant_services/template', [CongregantServiceTypeController::class, 'downloadTemplate'])->name('congregant_services.template');
        Route::get('congregant_services/import', [CongregantServiceTypeController::class, 'importForm'])->name('congregant_services.import.form');
        Route::post('congregant_services/import', [CongregantServiceTypeController::class, 'import'])->name('congregant_services.import');
        Route::delete('congregant_services/bulk-destroy', [CongregantServiceTypeController::class, 'bulkDestroy'])->name('congregant_services.bulk-destroy');
        Route::resource('congregant_services', CongregantServiceTypeController::class)->except(['show']);

        Route::get('schedules/{schedule}/export', [ScheduleController::class, 'export'])->name('schedules.export');
        Route::delete('schedules/bulk-destroy', [ScheduleController::class, 'bulkDestroy'])->name('schedules.bulk-destroy');
        Route::resource('schedules', ScheduleController::class)->except(['edit', 'update']);

        Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'destroy']);

        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

        Route::prefix('ajax')
            ->group(function () {
                Route::get('activities', [ActivityController::class, 'ajax'])->name('ajax.activities');
                Route::get('congregants', [CongregantController::class, 'ajax'])->name('ajax.congregants');
            });
    });
