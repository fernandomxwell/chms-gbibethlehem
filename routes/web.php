<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CongregantController;
use App\Http\Controllers\CongregantServiceTypeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ServiceTypesController;
use Illuminate\Support\Facades\Route;

Route::get('login', [AuthController::class, 'create'])->name('login');
Route::post('login', [AuthController::class, 'store']);

Route::middleware(['auth'])
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home.index');

        Route::delete('activities/bulk-destroy', [ActivityController::class, 'bulkDestroy'])->name('activities.bulk-destroy');
        Route::resource('activities', ActivityController::class);

        Route::get('congregants/export', [CongregantController::class, 'export'])->name('congregants.export');
        Route::get('congregants/template', [CongregantController::class, 'downloadTemplate'])->name('congregants.template');
        Route::get('congregants/import', [CongregantController::class, 'importForm'])->name('congregants.import.form');
        Route::post('congregants/import', [CongregantController::class, 'import'])->name('congregants.import');
        Route::delete('congregants/bulk-destroy', [CongregantController::class, 'bulkDestroy'])->name('congregants.bulk-destroy');
        Route::resource('congregants', CongregantController::class);

        Route::delete('service_types/bulk-destroy', [ServiceTypesController::class, 'bulkDestroy'])->name('service_types.bulk-destroy');
        Route::resource('service_types', ServiceTypesController::class);

        Route::delete('congregant_services/bulk-destroy', [CongregantServiceTypeController::class, 'bulkDestroy'])->name('congregant_services.bulk-destroy');
        Route::resource('congregant_services', CongregantServiceTypeController::class)->except(['show']);

        Route::get('schedules/{schedule}/export', [ScheduleController::class, 'export'])->name('schedules.export');
        Route::delete('schedules/bulk-destroy', [ScheduleController::class, 'bulkDestroy'])->name('schedules.bulk-destroy');
        Route::resource('schedules', ScheduleController::class)->except(['edit', 'update']);

        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

        Route::prefix('ajax')
            ->group(function () {
                Route::get('activities', [ActivityController::class, 'ajax'])->name('ajax.activities');
                Route::get('congregants', [CongregantController::class, 'ajax'])->name('ajax.congregants');
            });
    });
