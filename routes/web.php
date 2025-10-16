<?php


use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderStageController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\DesignController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SurveyQuestionController;

use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\FaqController as adminFaqController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogCommentController as AdminBlogCommentController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\SEOSettingController;
use App\Http\Controllers\Admin\ContactSectionController;
use App\Http\Controllers\Admin\ExhibitionCategoryController;
use App\Http\Controllers\Admin\ExhibitionController;
//use App\Http\Controllers\Admin\ExhibitionStepController;

use App\Http\Controllers\Admin\HelpController;
// Enhanced System Controllers
use App\Http\Controllers\Admin\EnhancedOrderController;
use App\Http\Controllers\Admin\EnhancedFinancialController;
use App\Http\Controllers\Admin\EnhancedCRMController;
use App\Http\Controllers\Admin\EnhancedEmployeeController;
use App\Http\Controllers\Admin\EnhancedDashboardController;

use App\Http\Controllers\Admin\EnhancedMarketingController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\FinancialController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\CRMController;
use App\Http\Controllers\Admin\MarketingController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\NotificationController;
//use App\Http\Controllers\Admin\QuestionnaireController;




////Home
use App\Http\Controllers\Admin\Home\HeroSliderController;
use App\Http\Controllers\Admin\Home\StepController;
use App\Http\Controllers\Admin\Home\HomeAboutController;
use App\Http\Controllers\Admin\Home\ProcessSectionController;
use App\Http\Controllers\Admin\Home\WhyChooseController;
use App\Http\Controllers\Admin\Home\OrderTimelineController;
use App\Http\Controllers\Admin\Home\ReadyToFurnishController;
use App\Http\Controllers\Admin\AboutPageController;



/////
use App\Http\Controllers\Employee\Auth\LoginController as EmployeeLoginController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PackageController as FrontendPackageController;
use App\Http\Controllers\Frontend\ProductController as FrontendProductController;
use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\GalleryController;
use App\Http\Controllers\Frontend\GalleryDetailsController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\BlogCommentController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\HelpController as FrontendHelpController;
use App\Http\Controllers\Frontend\FaqController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\OrderController as FrontendOrderController;
use App\Http\Controllers\Frontend\ProfileController;

use App\Http\Controllers\Frontend\Auth\AuthController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Auth;

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
Route::post('/login/check', [AuthController::class, 'checkPhone'])->name('login.check');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/verify-code', [AuthController::class, 'verifyCode'])->name('verify.code');


// روتات بدون prefix للعربية (الافتراضية)
Route::group(['middleware' => 'locale'], function () { // ✅ غير هنا
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/packages', [FrontendPackageController::class, 'index'])->name('packages.index');
    Route::get('/package/{id}', [FrontendPackageController::class, 'show'])->name('packages.show');
    Route::get('/products/{product:slug}', [FrontendProductController::class, 'show'])->name('products.show');
    Route::post('/packages/filter', [FrontendPackageController::class, 'filter'])->name('packages.filter');

    Route::get('/about', [AboutController::class, 'index'])->name('about');
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
    Route::get('/gallery/{id}', [GalleryDetailsController::class, 'show'])->name('gallery.details');
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.details');
    Route::post('/blog/{blog}/comment', [BlogCommentController::class, 'store'])->name('blog.comments.store');

    Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
    Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
    Route::get('/help', [FrontendHelpController::class, 'index'])->name('help.index');
    Route::post('/help', [FrontendHelpController::class, 'submit'])->name('help.submit');
    Route::get('/faq', [FaqController::class, 'index'])->name('faq');
    Route::get('/cart', [CartController::class, 'index'])->name('cart');


    Route::get('/confirm/{id}', [FrontendOrderController::class, 'confirm'])->name('order.confirm');
    Route::post('/store/{id}', [FrontendOrderController::class, 'store'])->name('order.store')->middleware('auth');
    Route::get('/success/{order_id?}', [FrontendOrderController::class, 'success'])->name('order.success')->middleware('auth');
    Route::get('/order/{order}', [FrontendOrderController::class, 'show'])->name('order.details')->middleware('auth');
    Route::get('/my-orders', [FrontendOrderController::class, 'myOrders'])->name('order.my')->middleware('auth');
    Route::get('/order/{order}/invoice', [FrontendOrderController::class, 'showInvoice'])->name('order.invoice')->middleware('auth');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index')->middleware('auth');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update')->middleware('auth');

});


// روتات مع prefix للإنجليزية
Route::group(['prefix' => 'en', 'middleware' => 'locale'], function () { // ✅ أضف middleware هنا أيضاً
    Route::get('/', [HomeController::class, 'index'])->name('home.en');
    Route::get('/packages', [FrontendPackageController::class, 'index'])->name('packages.index.en');
    Route::get('/package/{id}', [FrontendPackageController::class, 'show'])->name('packages.show.en');
    Route::post('/packages/filter', [FrontendPackageController::class, 'filter'])->name('packages.filter.en');

    Route::get('/products/{product:slug}', [FrontendProductController::class, 'show'])->name('products.show.en');
    Route::get('/about', [AboutController::class, 'index'])->name('about.en');
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index.en');
    Route::get('/gallery/{id}', [GalleryDetailsController::class, 'show'])->name('gallery.details.en');
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index.en');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.details.en');
    Route::post('/blog/{blog}/comment', [BlogCommentController::class, 'store'])->name('blog.comments.store.en');
    Route::get('/contact', [ContactController::class, 'index'])->name('contact.index.en');
    Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit.en');
    Route::get('/help', [FrontendHelpController::class, 'index'])->name('help.index.en');
    Route::post('/help', [FrontendHelpController::class, 'submit'])->name('help.submit.en');
    Route::get('/faq', [FaqController::class, 'index'])->name('faq.en');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.en');


    Route::get('/confirm/{id}', [FrontendOrderController::class, 'confirm'])->name('order.confirm.en');
    Route::post('/store/{id}', [FrontendOrderController::class, 'store'])->name('order.store.en')->middleware('auth');
    Route::get('/success/{order_id?}', [FrontendOrderController::class, 'success'])->name('order.success.en')->middleware('auth');
    Route::get('/order/{order}', [FrontendOrderController::class, 'show'])->name('order.details.en')->middleware('auth');
    Route::get('/my-orders', [FrontendOrderController::class, 'myOrders'])->name('order.my.en')->middleware('auth');
    Route::get('/order/{order}/invoice', [FrontendOrderController::class, 'showInvoice'])->name('order.invoice.en')->middleware('auth');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index.en')->middleware('auth');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update.en')->middleware('auth');
});


/*Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
*/


Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/');
})->name('logout');


Route::prefix('employee')->name('employee.')->group(function () {

    // تسجيل دخول الموظف
    Route::get('/login', [EmployeeLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [EmployeeLoginController::class, 'login']);

    // Dashboard محمي
    /*Route::middleware('auth:employee')->group(function () {
        Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [EmployeeLoginController::class, 'logout'])->name('logout');
    });*/
});



// ==================== Employee Portal Routes ====================
/*Route::prefix('employee')->name('employee.')->middleware(['auth', 'employee'])->group(function () {
    Route::get('/dashboard', [EnhancedEmployeeController::class, 'employeeDashboard'])->name('dashboard');
    Route::get('/orders', [EnhancedEmployeeController::class, 'employeeOrders'])->name('orders');
    Route::get('/orders/{order}', [EnhancedEmployeeController::class, 'employeeOrderShow'])->name('orders.show');
    Route::post('/orders/{order}/status', [EnhancedEmployeeController::class, 'employeeUpdateOrderStatus'])->name('orders.update-status');
});*/




// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['web'])->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.submit');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});



Route::prefix('admin')->name('admin.')->middleware(['web', 'admin_or_employee'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('packages', PackageController::class);
    Route::resource('order_stages', OrderStageController::class);

    // Roles & Permissions Management
    Route::resource('roles', App\Http\Controllers\Admin\RoleController::class);
    Route::patch('roles/{role}/toggle-status', [App\Http\Controllers\Admin\RoleController::class, 'toggleStatus'])
        ->name('roles.toggle-status');
    Route::post('roles/{role}/duplicate', [App\Http\Controllers\Admin\RoleController::class, 'duplicate'])
        ->name('roles.duplicate');



    // حذف الباكدج كامل
    Route::delete('packages/{package}', [PackageController::class, 'destroy'])
    ->name('packages.destroy');

    // حذف صورة واحدة من الباكدج
    Route::delete('packages/{package}/images/{image}', [PackageController::class, 'deleteImage'])
    ->name('packages.images.destroy');
    Route::post('/admin/package/content', [PackageController::class, 'updatePackage'])->name('package.content.update');


    Route::delete('/packages/{package}/images/{image}', [PackageController::class, 'deleteImage'])->name('packages.images.destroy');

   Route::resource('units', UnitController::class);

    Route::delete('units/{unit}', [UnitController::class, 'destroy'])->name('units.destroy');



    Route::delete('units/{unit}/images/{image}', [UnitController::class, 'destroyImage'])
    ->name('unit-images.destroy');


    Route::get('/units/{unit}/details', [UnitController::class, 'details'])->name('units.details');

    Route::get('/items/by-unit/{unitId}', [ItemController::class, 'getByUnit'])->name('items.by-unit');




    //Route::delete('unit-images/{unitImage}', [UnitController::class, 'destroyimage'])->name('unit-images.destroy');


    Route::resource('designs', DesignController::class);
    Route::resource('designs.items', ItemController::class)->scoped(['design' => 'id']);


    Route::resource('items', ItemController::class);
    Route::delete('admin/items/images/{image}', [ItemController::class, 'destroy'])->name('item-images.destroy');

    Route::delete('admin/items/{item}/destroy-image', [ItemController::class, 'destroyImage'])
    ->name('items.destroy-image');


    Route::get('admin/items', [ItemController::class, 'allItems'])->name('items.all');
    //Route::delete('items/{item}/image', [ItemController::class, 'destroyImage'])->name('items.image.destroy');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');

    //Route::resource('products', ProductController::class);
    //Route::resource('orders', OrderController::class);

    Route::resource('testimonials', TestimonialController::class);
    Route::resource('faqs', adminFaqController::class);
    Route::post('/admin/faqs/content', [adminFaqController::class, 'updateFaq'])->name('faq.content.update');


    //////Home
    Route::resource('hero-sliders', HeroSliderController::class);
    Route::resource('steps', StepController::class);
    Route::get('/home-about', [HomeAboutController::class, 'edit'])->name('home-about.edit');
    Route::put('/home-about', [HomeAboutController::class, 'update'])->name('home-about.update');
    Route::get('/process-section', [ProcessSectionController::class, 'edit'])->name('process.edit');
    Route::put('/process-section', [ProcessSectionController::class, 'update'])->name('process.update');
    Route::get('/why-choose', [WhyChooseController::class, 'edit'])->name('why-choose.edit');
    Route::put('/why-choose', [WhyChooseController::class, 'update'])->name('why-choose.update');
    Route::post('/why-choose/items', [WhyChooseController::class, 'storeItem'])->name('why-choose.items.store');
    Route::put('/why-choose/items/{item}', [WhyChooseController::class, 'updateItem'])->name('why-choose.items.update');
    Route::delete('/why-choose/items/{item}', [WhyChooseController::class, 'destroyItem'])->name('why-choose.items.destroy');
    Route::get('/order-timeline', [OrderTimelineController::class, 'edit'])->name('order-timeline.edit');
    Route::put('/order-timeline', [OrderTimelineController::class, 'update'])->name('order-timeline.update');
    Route::get('/ready-to-furnish', [ReadyToFurnishController::class, 'edit'])->name('ready-to-furnish.edit');
    Route::put('/ready-to-furnish', [ReadyToFurnishController::class, 'update'])->name('ready-to-furnish.update');


    Route::resource('about', AboutPageController::class);

    ///SEO
    Route::get('admin/seo', [SEOSettingController::class, 'index'])->name('seo.index');
    Route::post('admin/seo', [SEOSettingController::class, 'update'])->name('seo.update');


    Route::resource('blogs', AdminBlogController::class);
    Route::resource('blog_categories', BlogCategoryController::class);
    Route::get('/admin/blog/content', [BlogCategoryController::class, 'editBlog'])->name('blog.content.edit');
    Route::post('/admin/blog/content', [BlogCategoryController::class, 'updateBlog'])->name('blog.content.update');
    Route::put('/admin/comments/{id}/approve', [AdminBlogCommentController::class, 'approve'])->name('comments.approve');
    Route::put('/admin/comments/{id}/reject', [AdminBlogCommentController::class, 'reject'])->name('comments.reject');

    //

    Route::resource('exhibition-categories', ExhibitionCategoryController::class);
    Route::resource('exhibitions', ExhibitionController::class);
    Route::post('exhibitions/{exhibition}/images/{image}/set-primary', [ExhibitionController::class, 'setPrimaryImage'])->name('exhibitions.setPrimaryImage');
    Route::delete('exhibitions/{exhibition}/images/{image}', [ExhibitionController::class, 'deleteImage'])->name('exhibitions.deleteImage');
    Route::post('/admin/exhibitions/content', [ExhibitionController::class, 'updateExhibitions'])->name('exhibitions.content.update');

    Route::resource('survey-questions', SurveyQuestionController::class);


    //Route::resource('exhibitions.steps', ExhibitionStepController::class)->shallow();


    Route::resource('contacts', AdminContactController::class);
    Route::get('contact-section/edit', [ContactSectionController::class, 'edit'])->name('contact.edit');
    Route::put('contact-section/update', [ContactSectionController::class, 'update'])->name('contact.update');


    Route::get('/help-requests', [HelpController::class, 'index'])->name('help.index');
    Route::post('/admin/help/content', [HelpController::class, 'updatehelp'])->name('help.content.update');
    Route::delete('/requests/{request}', [HelpController::class, 'destroy'])->name('requests.destroy');


    Route::resource('users', UserController::class);

    //Route::get('/admin/users', [UserController::class, 'index'])->name('users.index');
    //Route::delete('/admin/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');


    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

    // ==================== Enhanced Orders Routes ====================
    Route::prefix('orders/enhanced')->name('orders.enhanced.')->group(function () {
        Route::get('/', [EnhancedOrderController::class, 'index'])->name('index');
        Route::get('/create', [EnhancedOrderController::class, 'create'])->name('create');
        Route::post('/', [EnhancedOrderController::class, 'store'])->name('store');
        Route::get('/{order}', [EnhancedOrderController::class, 'show'])->name('show');
        Route::get('/{order}/edit', [EnhancedOrderController::class, 'edit'])->name('edit');
        Route::put('/{order}', [EnhancedOrderController::class, 'update'])->name('update');
        Route::delete('/{order}', [EnhancedOrderController::class, 'destroy'])->name('destroy');

        //Route::resource('order_stages', OrderStageController::class);

        /*Route::get('/order_stages', [OrderStageController::class, 'index'])->name('order_stages.index');
        Route::get('/order_stages/create', [OrderStageController::class, 'create'])->name('order_stages.create');
        Route::post('/order_stages', [OrderStageController::class, 'store'])->name('order_stages.store');
        Route::get('/order_stages/{orderStage}', [OrderStageController::class, 'show'])->name('order_stages.show');
        Route::get('/order_stages/{orderStage}/edit', [OrderStageController::class, 'edit'])->name('order_stages.edit');
        Route::put('/order_stages/{orderStage}', [OrderStageController::class, 'update'])->name('order_stages.update');
        Route::delete('/order_stages/{orderStage}', [OrderStageController::class, 'destroy'])->name('order_stages.destroy');*/


        Route::put('orders/{order}/update-timeline', [EnhancedOrderController::class, 'updateTimeline'])->name('update-timeline');
        // Order Status Management
       // Route::post('/{order}/status', [EnhancedOrderController::class, 'updateStatus'])->name('update-status');
        //Route::post('/{order}/timeline', [EnhancedOrderController::class, 'addTimelineEvent'])->name('add-timeline');
        //Route::delete('/{order}/assign/{assignment}', [EnhancedOrderController::class, 'removeAssignment'])->name('remove-assignment');
        Route::delete('/assignments/{assignment}', [EnhancedOrderController::class, 'removeAssignment'])->name('remove-assignment');

        Route::get('admin/orders/{order}/details', [EnhancedOrderController::class, 'detailsHtml'])->name('details');

        // Order Actions
        Route::post('/{order}/note', [EnhancedOrderController::class, 'addNote'])->name('add-note');
        Route::post('/{order}/assign', [EnhancedOrderController::class, 'assignEmployee'])->name('assign');
        Route::post('/{order}/status', [EnhancedOrderController::class, 'updateStatus'])->name('update-status');
        Route::post('/{order}/timeline', [EnhancedOrderController::class, 'addTimelineEvent'])->name('add-timeline');

         // Bulk Operations
         Route::post('/bulk/status', [EnhancedOrderController::class, 'bulkUpdateStatus'])->name('bulk.status');
         Route::post('/bulk/assign', [EnhancedOrderController::class, 'bulkAssign'])->name('bulk.assign');
         Route::post('/bulk/export', [EnhancedOrderController::class, 'bulkExport'])->name('bulk.export');
         Route::post('/bulk/action', [EnhancedOrderController::class, 'bulkAction'])->name('bulk-action');
         Route::delete('/bulk/delete', [EnhancedOrderController::class, 'bulkDelete'])->name('bulk.delete');

          // Import/Export
        //Route::get('/export', [EnhancedOrderController::class, 'export'])->name('enhanced.export');
        //Route::get('/import', [EnhancedOrderController::class, 'showImport'])->name('import.show');
       // Route::post('/import', [EnhancedOrderController::class, 'import'])->name('enhanced.import');
       // Route::get('/template', [EnhancedOrderController::class, 'downloadTemplate'])->name('template');


        // Reports
        Route::get('/reports/index', [EnhancedOrderController::class, 'reports'])->name('reports');
        Route::get('/reports/export', [EnhancedOrderController::class, 'export'])->name('export');
        Route::post('/reports/import', [EnhancedOrderController::class, 'import'])->name('import');

        // Duplicate Detection
        Route::get('/duplicates', [EnhancedOrderController::class, 'detectDuplicates'])->name('duplicates');

        // Payment Schedules
        Route::post('/{order}/payment-schedules', [EnhancedOrderController::class, 'addPaymentSchedule'])->name('payment-schedules.add');
        Route::put('/{order}/payment-schedules/{schedule}', [EnhancedOrderController::class, 'updatePaymentSchedule'])->name('payment-schedules.update');
        Route::delete('/{order}/payment-schedules/{schedule}', [EnhancedOrderController::class, 'deletePaymentSchedule'])->name('payment-schedules.delete');
    });

    // ==================== Financial Routes ====================
    Route::prefix('financial')->name('financial.')->group(function () {
        // Dashboard
        Route::get('/', [EnhancedFinancialController::class, 'index'])->name('index');

        // Invoices
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [EnhancedFinancialController::class, 'invoicesIndex'])->name('index');
            Route::get('/create', [EnhancedFinancialController::class, 'createInvoice'])->name('create');
            Route::post('/', [EnhancedFinancialController::class, 'storeInvoice'])->name('store');
            Route::get('/{invoice}', [EnhancedFinancialController::class, 'showInvoice'])->name('show');
            Route::get('/{invoice}/edit', [EnhancedFinancialController::class, 'editInvoice'])->name('edit');
            Route::put('/{invoice}', [EnhancedFinancialController::class, 'updateInvoice'])->name('update');
            Route::get('/{invoice}/download', [EnhancedFinancialController::class, 'downloadInvoice'])->name('download');
        });

        // Payments
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [EnhancedFinancialController::class, 'paymentsIndex'])->name('index');
            Route::get('/create', [EnhancedFinancialController::class, 'createPayment'])->name('create');
            Route::post('/', [EnhancedFinancialController::class, 'storePayment'])->name('store');
            Route::get('/{payment}', [FinancialController::class, 'showPayment'])->name('show');
            Route::get('/{payment}/edit', [FinancialController::class, 'editPayment'])->name('edit');
            Route::put('/{payment}', [FinancialController::class, 'updatePayment'])->name('update');
            Route::delete('/{payment}', [FinancialController::class, 'destroyPayment'])->name('destroy');
            Route::post('/{payment}/refund', [FinancialController::class, 'refundPayment'])->name('refund');

        });

        // Reports
        Route::get('/reports', [EnhancedFinancialController::class, 'reports'])->name('reports');
        Route::get('/export', [EnhancedFinancialController::class, 'export'])->name('export');
    });

    // ==================== CRM Routes ====================
    Route::prefix('crm')->name('crm.')->group(function () {
        // Dashboard
        Route::get('/', [EnhancedCRMController::class, 'index'])->name('index');

        // Leads
        Route::prefix('leads')->name('leads.')->group(function () {
            Route::get('/', [EnhancedCRMController::class, 'leadsIndex'])->name('index');
            Route::get('/create', [EnhancedCRMController::class, 'createLead'])->name('create');
            Route::post('/', [EnhancedCRMController::class, 'storeLead'])->name('store');
            Route::get('/{lead}', [EnhancedCRMController::class, 'showLead'])->name('show');
            Route::get('/{lead}/edit', [EnhancedCRMController::class, 'editLead'])->name('edit');
            Route::put('/{lead}', [EnhancedCRMController::class, 'updateLead'])->name('update');
            Route::post('/{lead}/activity', [EnhancedCRMController::class, 'addActivity'])->name('add-activity');
            Route::post('/{lead}/convert', [EnhancedCRMController::class, 'convertToOrder'])->name('convert');
        });

        // Quotes
        Route::prefix('quotes')->name('quotes.')->group(function () {
            Route::get('/', [EnhancedCRMController::class, 'quotesIndex'])->name('index');
            Route::get('/create', [EnhancedCRMController::class, 'createQuote'])->name('create');
            Route::post('/', [EnhancedCRMController::class, 'storeQuote'])->name('store');
            Route::get('/{quote}', [EnhancedCRMController::class, 'showQuote'])->name('show');
        });

        // Sales Funnel
        Route::get('/funnel', [EnhancedCRMController::class, 'funnel'])->name('funnel');

        // Activities
        Route::get('/activities', [EnhancedCRMController::class, 'activities'])->name('activities');
    });

    // ==================== Marketing Routes ====================
    Route::prefix('marketing')->name('marketing.')->group(function () {
        // Dashboard
        Route::get('/', [EnhancedMarketingController::class, 'index'])->name('index');

        // Campaigns
        Route::prefix('campaigns')->name('campaigns.')->group(function () {
            Route::get('/', [EnhancedMarketingController::class, 'campaigns'])->name('index');
            Route::get('/create', [EnhancedMarketingController::class, 'createCampaign'])->name('create');
            Route::post('/', [EnhancedMarketingController::class, 'storeCampaign'])->name('store');
            Route::get('/{campaign}', [EnhancedMarketingController::class, 'showCampaign'])->name('show');
            Route::get('/{campaign}/edit', [EnhancedMarketingController::class, 'editCampaign'])->name('edit');
            Route::put('/{campaign}', [EnhancedMarketingController::class, 'updateCampaign'])->name('update');
            Route::delete('/{campaign}', [EnhancedMarketingController::class, 'destroyCampaign'])->name('destroy');

            // Campaign Actions
            Route::post('/{campaign}/start', [EnhancedMarketingController::class, 'startCampaign'])->name('start');
            Route::post('/{campaign}/pause', [EnhancedMarketingController::class, 'pauseCampaign'])->name('pause');
            Route::post('/{campaign}/resume', [EnhancedMarketingController::class, 'resumeCampaign'])->name('resume');
            Route::post('/{campaign}/complete', [EnhancedMarketingController::class, 'completeCampaign'])->name('complete');
        });

        // Coupons
        Route::prefix('coupons')->name('coupons.')->group(function () {
            Route::get('/', [EnhancedMarketingController::class, 'coupons'])->name('index');
            Route::get('/create', [EnhancedMarketingController::class, 'createCoupon'])->name('create');
            Route::post('/', [EnhancedMarketingController::class, 'storeCoupon'])->name('store');
            Route::get('/{coupon}/edit', [EnhancedMarketingController::class, 'editCoupon'])->name('edit');
            Route::put('/{coupon}', [EnhancedMarketingController::class, 'updateCoupon'])->name('update');
            Route::delete('/{coupon}', [EnhancedMarketingController::class, 'destroyCoupon'])->name('destroy');
        });

        // Analytics
        Route::get('/analytics', [EnhancedMarketingController::class, 'analytics'])->name('analytics');
    });

    // ==================== Employee Management Routes ====================
    Route::prefix('employees')->name('employees.')->group(function () {
        // Employees
        Route::get('/', [EnhancedEmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EnhancedEmployeeController::class, 'create'])->name('create');
        Route::post('/', [EnhancedEmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}', [EnhancedEmployeeController::class, 'show'])->name('show');
        Route::get('/{employee}/edit', [EnhancedEmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [EnhancedEmployeeController::class, 'update'])->name('update');
        Route::post('/{employee}/toggle', [EnhancedEmployeeController::class, 'toggleStatus'])->name('toggle');
        Route::get('/{employee}/assignments', [EnhancedEmployeeController::class, 'assignOrders'])->name('assignments');
        Route::get('/{employee}/performance', [EnhancedEmployeeController::class, 'performance'])->name('performance');

        // Roles
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [EnhancedEmployeeController::class, 'rolesIndex'])->name('index');
            Route::get('/create', [EnhancedEmployeeController::class, 'createRole'])->name('create');
            Route::post('/', [EnhancedEmployeeController::class, 'storeRole'])->name('store');
            Route::get('/{role}/edit', [EnhancedEmployeeController::class, 'editRole'])->name('edit');
            Route::put('/{role}', [EnhancedEmployeeController::class, 'updateRole'])->name('update');
        });

        // Permissions
        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/matrix', [EnhancedEmployeeController::class, 'permissionsMatrix'])->name('matrix');
            Route::post('/matrix', [EnhancedEmployeeController::class, 'updatePermissionsMatrix'])->name('update-matrix');
        });

        // Performance
        Route::prefix('performance')->name('performance.')->group(function () {
            Route::get('/reports', [EnhancedEmployeeController::class, 'performanceReports'])->name('reports');
        });
    });

     // Security Management
     Route::prefix('security')->name('security.')->group(function () {
        Route::get('/', [SecurityController::class, 'index'])->name('index');
        Route::get('/logs', [SecurityController::class, 'logs'])->name('logs');
        Route::get('/failed-logins', [SecurityController::class, 'failedLogins'])->name('failed-logins');
        Route::get('/analytics', [SecurityController::class, 'analytics'])->name('analytics');
        Route::get('/settings', [SecurityController::class, 'settings'])->name('settings');
        Route::put('/settings', [SecurityController::class, 'updateSettings'])->name('settings.update');

        // Security Actions
        Route::post('/block-ip', [SecurityController::class, 'blockIp'])->name('block-ip');
        Route::post('/unblock-ip', [SecurityController::class, 'unblockIp'])->name('unblock-ip');
        Route::post('/block-email', [SecurityController::class, 'blockEmail'])->name('block-email');
        Route::post('/unblock-email', [SecurityController::class, 'unblockEmail'])->name('unblock-email');
        Route::post('/block-user', [SecurityController::class, 'blockUser'])->name('block-user');
        Route::post('/unblock-user', [SecurityController::class, 'unblockUser'])->name('unblock-user');
        Route::post('/force-password-reset', [SecurityController::class, 'forcePasswordReset'])->name('force-password-reset');

        // Log Actions
        Route::post('/logs/{log}/mark-reviewed', [SecurityController::class, 'markAsReviewed'])->name('logs.mark-reviewed');
        Route::post('/logs/{log}/mark-suspicious', [SecurityController::class, 'markAsSuspicious'])->name('logs.mark-suspicious');

        // Maintenance
        Route::post('/cleanup', [SecurityController::class, 'cleanup'])->name('cleanup');
        Route::get('/export', [SecurityController::class, 'exportReport'])->name('export');

        // API Endpoints
        Route::get('/api/stats', [SecurityController::class, 'getStats'])->name('api.stats');
        Route::get('/api/alerts', [SecurityController::class, 'getAlerts'])->name('api.alerts');
    });
});

Route::post('/set-locale', [LocaleController::class, 'setLocale'])->name('setLocale');




// API Routes for AJAX requests
Route::middleware(['auth'])->prefix('api')->name('api.')->group(function () {
    //Route::get('orders/stats', [OrderController::class, 'getStats'])->name('orders.stats');
    Route::get('dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
});
//require __DIR__.'/auth.php';
