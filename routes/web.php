<?php

use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;

use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\FaqController as adminFaqController;
use App\Http\Controllers\Admin\HeroSliderController;

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\CategoryController as FrontendCategoryController;
use App\Http\Controllers\Frontend\ProductController as FrontendProductController;
use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\GalleryController;
use App\Http\Controllers\Frontend\GalleryDetailsController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\HelpController;
use App\Http\Controllers\Frontend\FaqController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocaleController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Frontend Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/categories', [FrontendCategoryController::class, 'index'])->name('categories.index');
Route::get('/category/{id}', [FrontendCategoryController::class, 'show'])->name('categories.show');
Route::get('/products/{product:slug}', [FrontendProductController::class, 'show'])->name('products.show');

// صفحة من نحن
Route::get('/about', [AboutController::class, 'index'])->name('about');

// صفحة المعرض
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/gallery/{id}', [GalleryDetailsController::class, 'show'])->name('gallery.details');

// صفحة المدونة
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{id}', [BlogController::class, 'show'])->name('blog.details');

// صفحة اتصل بنا
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// صفحة المساعدة
Route::get('/help', [HelpController::class, 'index'])->name('help.index');
Route::post('/help', [HelpController::class, 'submit'])->name('help.submit');

Route::get('/faq', [FaqController::class, 'index'])->name('faq');




/*Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});*/

// Admin Routes

Route::prefix('admin')->name('admin.')->middleware(['web'])->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.submit');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});



Route::prefix('admin')->name('admin.')->middleware(['web', 'auth:admin', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('users', UserController::class);

    Route::resource('testimonials', TestimonialController::class);
    Route::resource('faqs', adminFaqController::class);


    Route::resource('hero-sliders', HeroSliderController::class);

    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
});

Route::post('/set-locale', [LocaleController::class, 'setLocale'])->name('setLocale');

require __DIR__.'/auth.php';
