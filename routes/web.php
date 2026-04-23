<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{UserController, DepartmentController};
use App\Http\Controllers\Document\DocumentController;
use App\Http\Controllers\Chat\ChatController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

// 1. Language Switcher (Publicly accessible)
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['tk', 'ru', 'en'])) {
        if (auth()->check()) {
            auth()->user()->update(['preferred_lang' => $locale]);
        }
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

// 2. Auth Routes (For Bootstrap Login)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// 3. Protected Routes (Must be logged in)
Route::middleware(['auth', 'setLanguage'])->group(function () {

    // Main Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('departments', DepartmentController::class);
        Route::get('/logs', [\App\Http\Controllers\Admin\LogController::class, 'index'])->name('logs');
          // Di dalam Route::prefix('admin')->name('admin.')->group(function () { ...
Route::resource('folders', \App\Http\Controllers\Admin\FolderController::class);
    });

    // Document Routes
    Route::prefix('documents')->name('docs.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::post('/upload', [DocumentController::class, 'store'])->name('store');
        Route::get('/download/{document}', [DocumentController::class, 'download'])->name('download');
        // Tambahkan baris ini di dalam group prefix('documents')
Route::get('/editor/{document}', [DocumentController::class, 'editor'])->name('editor');
        Route::get('/edit/{document}', [DocumentController::class, 'edit'])->name('edit');
        Route::put('/update/{document}', [DocumentController::class, 'update'])->name('update');
        Route::delete('/delete/{document}', [DocumentController::class, 'destroy'])->name('destroy');
        // --------------------------------

        Route::post('/share/{document}', [DocumentController::class, 'share'])->name('share');
        // Tambahkan di bawah route('docs.share') atau di grup route dokumen
       Route::delete('/permissions/{permission}', [\App\Http\Controllers\Document\DocumentController::class, 'unshare'])->name('unshare');

    });

  

    // Chat Routes
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/start/{userId}', [ChatController::class, 'startConversation'])->name('start');
        Route::get('/{conversation}', [ChatController::class, 'show'])->name('show');
        Route::post('/{conversation}/send', [ChatController::class, 'sendMessage'])->name('send');
    });

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});
