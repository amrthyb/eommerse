<?php

use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\Permission;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RolesController;
use App\Models\Category;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;


Route::get('/', function () {
    return redirect('/login');
});


Route::get('/register', [AuthenticationController::class, 'registerForm'])->name('registerForm');
Route::post('/register', [AuthenticationController::class, 'register'])->name('register');
Route::get('/login', [AuthenticationController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthenticationController::class, 'login']);

Route::get('users/export', [UserController::class, 'export'])->name('users.export');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('categories', CategoryController::class);
    Route::post('category/import', [CategoryController::class, 'import'])->name('category.import');
    Route::post('category/export', [CategoryController::class, 'export'])->name('category.export');

    Route::resource('products', ProductController::class);
    Route::post('/products/export', [ProductController::class, 'export'])->name('products.export');
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{id}', [UserController::class, 'show'])->name('users.show');

    Route::get('admins', [AdminController::class, 'index'])->name('admins.index');
    Route::get('admins/data', [AdminController::class, 'show'])->name('admins.data');
    Route::get('admins/create', [AdminController::class, 'create'])->name('admins.create');
    Route::post('admins', [AdminController::class, 'store'])->name('admins.store');
    Route::get('admins/{id}', [AdminController::class, 'edit'])->name('admins.edit');
    Route::delete('admins/{id}', [AdminController::class, 'destroy'])->name('admins.destroy');
    Route::get('/search-role', [AdminController::class, 'search'])->name('search.role');
    Route::put('admins/{id}', [AdminController::class, 'update'])->name('admins.update');

    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('orders/{id}/invoice', [OrderController::class, 'downloadInvoice'])->name('orders.invoice');
    Route::get('/order/export', [OrderController::class, 'export'])->name('orders.export');

    Route::get('settings', [AuthenticationController::class, 'settingView'])->name('settings.index');
    Route::post('settings/email', [AuthenticationController::class, 'emailChange'])->name('settings.store');
    Route::post('update-account',[AuthenticationController::class,'updateAccount'])->name('update-account');

    Route::get('roles',[RolesController::class,'index'])->name('roles.index');
    Route::get('roles-add',[RolesController::class,'create'])->name('roles.create');
    Route::post('roles/add',[RolesController::class,'store'])->name('roles.store');
    Route::get('roles/{id}/edit',[RolesController::class,'edit'])->name('roles.edit');
    Route::put('roles/{id}',[RolesController::class,'update'])->name('roles.update');
    Route::delete('roles/{id}',[RolesController::class,'destroy'])->name('roles.destroy');

    Route::post('/notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
    Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

});

Route::get('/greeting/{locale}', function (string $locale) {
    if (! in_array($locale, ['en', 'id'])) {
        abort(400);
    }
    App::setLocale($locale);
    session()->put('locale', $locale);
    return back();
})->name('set.language');
Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');

//verifikasi
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

//forgot password
Route::get('/forgot-password', function () {
    return view('auth.forgot');
})->name('password.request');

Route::post('/forgot-password', [AuthenticationController::class, 'forgot'])->name('password.email');

Route::get('/reset-password/{token}', function (Request $request,string $token) {
    return view('auth.reset', ['token' => $token,'request' =>$request]);
})->name('password.reset');

Route::post('/reset-password', [AuthenticationController::class, 'reset'])->name('password.update');
