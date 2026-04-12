<?php

use App\Http\Controllers\Admin\ActivityTypeController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BusinessAccountController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DynamicFieldController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SliderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.login');
});


Route::get('/locale/{lang}', function (string $lang) {
    if (in_array($lang, ['en', 'ar'])) {
        session(['locale' => $lang]);
    }
    return redirect()->back();
})->name('locale.switch');

// Admin routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::middleware('auth:admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Business Accounts
        Route::middleware('permission:view-business-accounts')->group(function () {
            Route::get('/business-accounts', [BusinessAccountController::class, 'index'])->name('business-accounts.index');
            Route::get('/business-accounts/{businessAccount}', [BusinessAccountController::class, 'show'])->name('business-accounts.show');
        });
        Route::middleware('permission:manage-business-accounts')->group(function () {
            Route::post('/business-accounts/{businessAccount}/approve', [BusinessAccountController::class, 'approve'])->name('business-accounts.approve');
            Route::post('/business-accounts/{businessAccount}/reject', [BusinessAccountController::class, 'reject'])->name('business-accounts.reject');
        });

        // Categories
        Route::middleware('permission:view-categories')->group(function () {
            Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        });
        Route::middleware('permission:manage-categories')->group(function () {
            Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
            Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
            Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
            Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
            Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

            // Dynamic Fields (nested under categories)
            Route::get('/categories/{category}/fields', [DynamicFieldController::class, 'index'])->name('categories.fields.index');
            Route::get('/categories/{category}/fields/create', [DynamicFieldController::class, 'create'])->name('categories.fields.create');
            Route::post('/categories/{category}/fields', [DynamicFieldController::class, 'store'])->name('categories.fields.store');
            Route::get('/categories/{category}/fields/{field}/edit', [DynamicFieldController::class, 'edit'])->name('categories.fields.edit');
            Route::put('/categories/{category}/fields/{field}', [DynamicFieldController::class, 'update'])->name('categories.fields.update');
            Route::delete('/categories/{category}/fields/{field}', [DynamicFieldController::class, 'destroy'])->name('categories.fields.destroy');
        });

        // Cities
        Route::middleware('permission:view-cities')->group(function () {
            Route::get('/cities', [CityController::class, 'index'])->name('cities.index');
        });
        Route::middleware('permission:manage-cities')->group(function () {
            Route::get('/cities/create', [CityController::class, 'create'])->name('cities.create');
            Route::post('/cities', [CityController::class, 'store'])->name('cities.store');
            Route::get('/cities/{city}/edit', [CityController::class, 'edit'])->name('cities.edit');
            Route::put('/cities/{city}', [CityController::class, 'update'])->name('cities.update');
            Route::delete('/cities/{city}', [CityController::class, 'destroy'])->name('cities.destroy');

            // Activity Types (reference data, same permission as cities)
            Route::get('/activity-types', [ActivityTypeController::class, 'index'])->name('activity-types.index');
            Route::get('/activity-types/create', [ActivityTypeController::class, 'create'])->name('activity-types.create');
            Route::post('/activity-types', [ActivityTypeController::class, 'store'])->name('activity-types.store');
            Route::get('/activity-types/{activityType}/edit', [ActivityTypeController::class, 'edit'])->name('activity-types.edit');
            Route::put('/activity-types/{activityType}', [ActivityTypeController::class, 'update'])->name('activity-types.update');
            Route::delete('/activity-types/{activityType}', [ActivityTypeController::class, 'destroy'])->name('activity-types.destroy');
        });

        // Services
        Route::middleware('permission:view-services')->group(function () {
            Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
            Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
        });
        Route::middleware('permission:manage-services')->group(function () {
            Route::post('/services/{service}/approve', [ServiceController::class, 'approve'])->name('services.approve');
            Route::post('/services/{service}/reject', [ServiceController::class, 'reject'])->name('services.reject');
        });

        // Sliders
        Route::middleware('permission:manage-sliders')->group(function () {
            Route::get('/sliders', [SliderController::class, 'index'])->name('sliders.index');
            Route::get('/sliders/create', [SliderController::class, 'create'])->name('sliders.create');
            Route::post('/sliders', [SliderController::class, 'store'])->name('sliders.store');
            Route::get('/sliders/{slider}/edit', [SliderController::class, 'edit'])->name('sliders.edit');
            Route::put('/sliders/{slider}', [SliderController::class, 'update'])->name('sliders.update');
            Route::delete('/sliders/{slider}', [SliderController::class, 'destroy'])->name('sliders.destroy');
        });

        // Placeholder routes for Phase 6+ (sidebar links — prevents errors)
        Route::get('/reports', fn() => redirect()->route('admin.dashboard'))->name('reports.index');

        // Roles (Super Admin only)
        Route::middleware('permission:manage-roles')->group(function () {
            Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
            Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
            Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
            Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
            Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
            Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        });

        // Admin Users (Super Admin only)
        Route::middleware('permission:manage-admins')->group(function () {
            Route::get('/admins', [AdminUserController::class, 'index'])->name('admins.index');
            Route::get('/admins/create', [AdminUserController::class, 'create'])->name('admins.create');
            Route::post('/admins', [AdminUserController::class, 'store'])->name('admins.store');
            Route::get('/admins/{admin}/edit', [AdminUserController::class, 'edit'])->name('admins.edit');
            Route::put('/admins/{admin}', [AdminUserController::class, 'update'])->name('admins.update');
            Route::delete('/admins/{admin}', [AdminUserController::class, 'destroy'])->name('admins.destroy');
        });
    });
});
