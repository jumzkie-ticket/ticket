<?php

use App\Http\Controllers\AboutUsController;
use App\Http\Controllers\AccountManagerController;
use App\Http\Controllers\AssignFcController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ClientUserRegistrationController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientRegistrationController;
use App\Http\Controllers\ContactSupportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IndustryBusinessTypeController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ProductDetailsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SapProductController;
use App\Http\Controllers\SecurityLevelController;
use App\Http\Controllers\ServiceOrderDetailController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketStatusController;
use App\Http\Controllers\UserRegistrationController;
use App\Http\Controllers\WorkAgreementController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login')
);

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
Route::get('/register', [ClientUserRegistrationController::class, 'create'])->name('register');
Route::post('/register', [ClientUserRegistrationController::class, 'store'])->name('register.store');

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/about-us', AboutUsController::class)->name('about-us');
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::get('/client-registration', [ClientRegistrationController::class, 'index'])->name('clients.registration');
    Route::get('/client-registration/country-codes', [ClientRegistrationController::class, 'countryCodes'])->name('clients.country-codes');
    Route::post('/client-registration', [ClientRegistrationController::class, 'store'])->name('clients.store');
    Route::get('/service-order-details', [ServiceOrderDetailController::class, 'index'])->name('service-order-details.index');
    Route::post('/service-order-details', [ServiceOrderDetailController::class, 'store'])->name('service-order-details.store');
    Route::get('/service-order-details/detail', [ServiceOrderDetailController::class, 'detail'])->name('service-order-details.detail');
    Route::get('/service-order-details/{serviceOrder}/edit', [ServiceOrderDetailController::class, 'edit'])->name('service-order-details.edit');
    Route::put('/service-order-details/{serviceOrder}', [ServiceOrderDetailController::class, 'update'])->name('service-order-details.update');
    Route::get('/service-order-details/{serviceOrder}/attachment', [ServiceOrderDetailController::class, 'viewAttachment'])->name('service-order-details.attachment');
    Route::get('/service-order-details/{serviceOrder}', [ServiceOrderDetailController::class, 'show'])->name('service-order-details.show');

    Route::get('/industry-business-types', [IndustryBusinessTypeController::class, 'index'])->name('industry-business-types.index');
    Route::post('/industry-business-types', [IndustryBusinessTypeController::class, 'store'])->name('industry-business-types.store');
    Route::put('/industry-business-types/{industryBusinessType}', [IndustryBusinessTypeController::class, 'update'])->name('industry-business-types.update');
    Route::delete('/industry-business-types/{industryBusinessType}', [IndustryBusinessTypeController::class, 'destroy'])->name('industry-business-types.destroy');
    Route::get('/sap-products', [SapProductController::class, 'index'])->name('sap-products.index');
    Route::post('/sap-products', [SapProductController::class, 'store'])->name('sap-products.store');
    Route::put('/sap-products/{sapProduct}', [SapProductController::class, 'update'])->name('sap-products.update');
    Route::delete('/sap-products/{sapProduct}', [SapProductController::class, 'destroy'])->name('sap-products.destroy');
    Route::resource('security-levels', SecurityLevelController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('ticket-statuses', TicketStatusController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/account-managers', [AccountManagerController::class, 'index'])->name('account-managers.index');
    Route::post('/account-managers', [AccountManagerController::class, 'store'])->name('account-managers.store');
    Route::put('/account-managers/{accountManager}', [AccountManagerController::class, 'update'])->name('account-managers.update');
    Route::delete('/account-managers/{accountManager}', [AccountManagerController::class, 'destroy'])->name('account-managers.destroy');
    Route::get('/assign-fcs', [AssignFcController::class, 'index'])->name('assign-fcs.index');
    Route::post('/assign-fcs', [AssignFcController::class, 'store'])->name('assign-fcs.store');
    Route::put('/assign-fcs/{assignFc}', [AssignFcController::class, 'update'])->name('assign-fcs.update');
    Route::delete('/assign-fcs/{assignFc}', [AssignFcController::class, 'destroy'])->name('assign-fcs.destroy');
    Route::get('/work-agreements', [WorkAgreementController::class, 'index'])->name('work-agreements.index');
    Route::post('/work-agreements', [WorkAgreementController::class, 'store'])->name('work-agreements.store');
    Route::put('/work-agreements/{workAgreement}', [WorkAgreementController::class, 'update'])->name('work-agreements.update');
    Route::delete('/work-agreements/{workAgreement}', [WorkAgreementController::class, 'destroy'])->name('work-agreements.destroy');
    Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
    Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
    Route::put('/packages/{package}', [PackageController::class, 'update'])->name('packages.update');
    Route::delete('/packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');
    Route::get('/product-details', [ProductDetailsController::class, 'index'])->name('product-details');
    Route::post('/product-details', [ProductDetailsController::class, 'store'])->name('product-details.store');
    Route::put('/product-details/{productDetail}', [ProductDetailsController::class, 'update'])->name('product-details.update');
    Route::delete('/product-details/{productDetail}', [ProductDetailsController::class, 'destroy'])->name('product-details.destroy');
    Route::get('/contact-support', [ContactSupportController::class, 'index'])->name('contact-support');
    Route::post('/contact-support', [ContactSupportController::class, 'store'])->name('contact-support.store');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::post('/tickets/{ticket}/resolutions', [TicketController::class, 'storeResolution'])->name('tickets.resolutions.store');
    Route::put('/tickets/{ticket}/resolutions/{resolution}', [TicketController::class, 'updateResolution'])->name('tickets.resolutions.update');
    Route::put('/tickets/{ticket}/classification', [TicketController::class, 'updateClassification'])->name('tickets.classification.update');
    Route::get('/tickets/{ticket}/attachment', [TicketController::class, 'viewAttachment'])->name('tickets.attachment');
    Route::get('/system-settings', SystemSettingsController::class)->name('system-settings');
    Route::put('/system-settings', [SystemSettingsController::class, 'update'])->name('system-settings.update');
    Route::resource('roles', RoleController::class)->except(['create', 'edit']);
    Route::get('/user-registration', [UserRegistrationController::class, 'index'])->name('users.index');
    Route::post('/user-registration', [UserRegistrationController::class, 'store'])->name('users.store');
    Route::put('/user-registration/{user}', [UserRegistrationController::class, 'update'])->name('users.update');
    Route::delete('/user-registration/{user}', [UserRegistrationController::class, 'destroy'])->name('users.destroy');
});
