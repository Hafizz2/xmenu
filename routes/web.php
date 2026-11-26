<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CafeSettingsController as Settings;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuBuilderController as Menu;
use App\Http\Controllers\PublicMenuController;
use App\Http\Controllers\QrController;
use Illuminate\Support\Facades\Route;

// Customer-facing pages run without sessions: no cookie, no database write per scan.
$noSession = [
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
];

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');
Route::get('/{page}', [HomeController::class, 'legal'])->whereIn('page', ['privacy', 'terms', 'credits'])->name('legal');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Analytics beacon from the (cached, token-less) menu page.
Route::post('/e', [PublicMenuController::class, 'track'])->name('track')->middleware('throttle:120,1')->withoutMiddleware($noSession);

Route::middleware(['auth', 'cafe'])->prefix('dashboard')->name('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index']);

    Route::get('/menu', [Menu::class, 'index'])->name('.menu');
    Route::post('/categories', [Menu::class, 'storeCategory'])->name('.categories.store');
    Route::put('/categories/{category}', [Menu::class, 'updateCategory'])->name('.categories.update');
    Route::delete('/categories/{category}', [Menu::class, 'destroyCategory'])->name('.categories.destroy');
    Route::post('/categories/{category}/move', [Menu::class, 'moveCategory'])->name('.categories.move');

    Route::get('/items/create', [Menu::class, 'createItem'])->name('.items.create');
    Route::post('/items', [Menu::class, 'storeItem'])->name('.items.store');
    Route::get('/items/{item}/edit', [Menu::class, 'editItem'])->name('.items.edit');
    Route::put('/items/{item}', [Menu::class, 'updateItem'])->name('.items.update');
    Route::delete('/items/{item}', [Menu::class, 'destroyItem'])->name('.items.destroy');
    Route::patch('/items/{item}/quick', [Menu::class, 'quickUpdate'])->name('.items.quick');
    Route::post('/items/{item}/duplicate', [Menu::class, 'duplicateItem'])->name('.items.duplicate');
    Route::post('/items/{item}/move', [Menu::class, 'moveItem'])->name('.items.move');

    Route::get('/import', [Menu::class, 'importForm'])->name('.import');
    Route::post('/import', [Menu::class, 'import']);
    Route::get('/import/template', [Menu::class, 'importTemplate'])->name('.import.template');

    Route::get('/info', [Settings::class, 'info'])->name('.info');
    Route::put('/info', [Settings::class, 'updateInfo']);

    Route::get('/payments', [Settings::class, 'payments'])->name('.payments');
    Route::post('/payments', [Settings::class, 'storePayment'])->name('.payments.store');
    Route::put('/payments/{account}', [Settings::class, 'updatePayment'])->name('.payments.update');
    Route::delete('/payments/{account}', [Settings::class, 'destroyPayment'])->name('.payments.destroy');
    Route::post('/payments/{account}/move', [Settings::class, 'movePayment'])->name('.payments.move');

    Route::get('/design', [DesignController::class, 'edit'])->name('.design');
    Route::get('/preview', [PublicMenuController::class, 'preview'])->name('.preview');
    Route::put('/design', [DesignController::class, 'update']);

    Route::get('/qr', [QrController::class, 'index'])->name('.qr');
    Route::get('/qr/preview', [QrController::class, 'preview'])->name('.qr.preview');
    Route::get('/qr/pdf', [QrController::class, 'pdf'])->name('.qr.pdf');
    Route::get('/qr/png', [QrController::class, 'png'])->name('.qr.png');

    Route::get('/games', [Settings::class, 'games'])->name('.games');
    Route::put('/games', [Settings::class, 'updateGames']);

    Route::get('/billing', [BillingController::class, 'index'])->name('.billing');
    Route::post('/billing', [BillingController::class, 'store'])->middleware('throttle:10,1');

    Route::get('/settings', [Settings::class, 'settings'])->name('.settings');
    Route::put('/settings', [Settings::class, 'updateSettings']);
    Route::put('/settings/password', [Settings::class, 'updatePassword'])->name('.password');
});

// ---------- Super admin (SaaS owner) ----------
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\OverviewController::class, 'index'])->name('index');

    Route::get('/cafes', [Admin\CafeController::class, 'index'])->name('cafes.index');
    Route::get('/cafes/{cafe}', [Admin\CafeController::class, 'show'])->name('cafes.show');
    Route::put('/cafes/{cafe}', [Admin\CafeController::class, 'update'])->name('cafes.update');
    Route::post('/cafes/{cafe}/extend', [Admin\CafeController::class, 'extendTrial'])->name('cafes.extend');
    Route::post('/cafes/{cafe}/suspend', [Admin\CafeController::class, 'suspend'])->name('cafes.suspend');
    Route::post('/cafes/{cafe}/manage', [Admin\CafeController::class, 'manage'])->name('cafes.manage');
    Route::delete('/cafes/{cafe}', [Admin\CafeController::class, 'destroy'])->name('cafes.destroy');
    Route::post('/manage/stop', [Admin\CafeController::class, 'stopManage'])->name('manage.stop');

    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}', [Admin\UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/suspend', [Admin\UserController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/password', [Admin\UserController::class, 'resetPassword'])->name('users.password');
    Route::delete('/users/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/sales', [Admin\SalesController::class, 'index'])->name('sales.index');
    Route::post('/sales', [Admin\SalesController::class, 'store'])->name('sales.store');
    Route::post('/sales/{subscription}/approve', [Admin\SalesController::class, 'approve'])->name('sales.approve');
    Route::post('/sales/{subscription}/reject', [Admin\SalesController::class, 'reject'])->name('sales.reject');
    Route::get('/sales/{subscription}/receipt', [Admin\SalesController::class, 'receipt'])->name('sales.receipt');
    Route::get('/plans', [Admin\SalesController::class, 'plans'])->name('plans');
    Route::put('/plans/{plan}', [Admin\SalesController::class, 'updatePlan'])->name('plans.update');

    Route::get('/analytics', [Admin\AnalyticsController::class, 'index'])->name('analytics');

    Route::get('/games', [Admin\PlatformController::class, 'games'])->name('games');
    Route::post('/games', [Admin\PlatformController::class, 'storeGame'])->name('games.store');
    Route::post('/games/{game}/toggle', [Admin\PlatformController::class, 'toggleGame'])->name('games.toggle');
    Route::get('/logs', [Admin\PlatformController::class, 'logs'])->name('logs');
    Route::get('/settings', [Admin\PlatformController::class, 'settings'])->name('settings');
    Route::put('/settings', [Admin\PlatformController::class, 'updateSettings']);
});

// Customer menu: must stay last so it doesn't swallow other routes.
Route::get('/{slug}', [PublicMenuController::class, 'show'])
    ->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')
    ->withoutMiddleware($noSession)
    ->name('menu.show');
