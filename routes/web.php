<?php

use App\Http\Controllers\addons\included\CurrencyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\PlanPricingController;
use App\Http\Controllers\admin\SetupController;
use App\Http\Controllers\admin\VendorRecordController;
use App\Http\Controllers\admin\BranchController;
use App\Http\Controllers\admin\LocationController;
use App\Http\Controllers\admin\WhatsAppController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\SettingsController;
use App\Http\Controllers\admin\PaymentController;
use App\Http\Controllers\admin\TransactionController;
use App\Http\Controllers\addons\MediaController;
use App\Http\Controllers\admin\BannerController;
use App\Http\Controllers\admin\GlobalExtrasController;
use App\Http\Controllers\admin\StoreCategoryController;
use App\Http\Controllers\admin\VendorController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\BookingController;
use App\Http\Controllers\admin\ServiceRequestController;
use App\Http\Controllers\admin\BookingServiceController;
use App\Http\Controllers\admin\OtherPagesController;
use App\Http\Controllers\admin\SystemAddonsController;
use App\Http\Controllers\admin\FeaturesController;
use App\Http\Controllers\admin\TimeController;
use App\Http\Controllers\admin\NotificationController;
use App\Http\Controllers\admin\RecaptchaController;
use App\Http\Controllers\web\HomeController;
use App\Http\Controllers\web\FavoriteController;
use App\Http\Controllers\admin\TaxController;
use App\Http\Controllers\admin\ThemeController;
use App\Http\Controllers\admin\WhoWeAreController;
use App\Http\Controllers\admin\WorksController;
use App\Http\Controllers\web\UserController as WebUserController;
use App\Http\Controllers\landing\HomeController as LandingHomeController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



//  ------------------------------- ----------- -----------------------------------------   //
//  -------------------------------  FOR ADMIN  -----------------------------------------   //
//  ------------------------------- ----------- -----------------------------------------   //	
Route::group(['namespace' => 'admin', 'prefix' => 'admin'], function () {
    // 'guest': an already signed-in user who opens the login or signup page is bounced to the
    // dashboard instead of being shown a form they cannot use.
    Route::get('/', [AdminController::class, 'login'])->middleware('guest');
    Route::post('checklogin-{logintype}', [AdminController::class, 'check_admin_login']);
    // The old single-page signup is superseded by the public wizard.
    Route::get('register', fn() => redirect('register/1'))->middleware('guest');
    Route::post('register_vendor', [VendorController::class, 'register_vendor']);
    Route::get('register/check', [VendorController::class, 'check_availability']);
    Route::get('forgot_password', [VendorController::class, 'forgot_password'])->middleware('guest');
    Route::post('send_password', [VendorController::class, 'send_password']);
    Route::post('/getarea', [VendorController::class, 'getarea']);


    Route::get(
        '/verification',
        function () {
            return view('admin.auth.verify');
        }
    );
    Route::post('systemverification', [AdminController::class, 'systemverification'])->name('admin.systemverification');

    Route::group(
        ['middleware' => 'AuthMiddleware'],
        function () {
            Route::get('apps', [SystemAddonsController::class, 'index'])->name('systemaddons');
            Route::get('createsystem-addons', [SystemAddonsController::class, 'createsystemaddons']);
            Route::post('systemaddons/store', [SystemAddonsController::class, 'store']);
            Route::get('systemaddons/status-{id}/{status}', [SystemAddonsController::class, 'change_status']);
            // -------- COMMON --------
            Route::get('admin_back', [VendorController::class, 'admin_back']);
            Route::get('logout', [AdminController::class, 'logout']);
            Route::get('dashboard', [AdminController::class, 'index']);

            // SETTINGS
            Route::post('settings/updaterecaptcha', [RecaptchaController::class, 'updaterecaptcha']);

            Route::get('settings', [SettingsController::class, 'settings_index']);
            Route::post('settings/update', [SettingsController::class, 'settings_update']);
            Route::post('settings/updateseo', [SettingsController::class, 'settings_updateseo']);
            Route::post('settings/updatetheme', [SettingsController::class, 'settings_updatetheme']);
            Route::post('settings/updateanalytics', [SettingsController::class, 'settings_updateanalytics']);
            Route::post('settings/updatecustomedomain', [SettingsController::class, 'settings_updatecustomedomain']);
            Route::post('settings/update-profile-{id}', [VendorController::class, 'update']);
            Route::post('settings/change-password', [VendorController::class, 'change_password']);

            Route::post('settings/maintenance_update', [SettingsController::class, 'maintenance_update']);
            Route::post('settings/estimated_delivery', [SettingsController::class, 'estimated_delivery']);


            // TRANSACTION
            Route::get('transaction', [TransactionController::class, 'index']);
            Route::get('transaction/plandetails-{id}', [PlanPricingController::class, 'plan_details']);
            Route::get('transaction/generatepdf-{id}', [PlanPricingController::class, 'generatepdf']);
            // WhatsApp Cloud API: connection, inbox and the AI knowledge base.
            Route::group(['prefix' => 'whatsapp'], function () {
                Route::get('/settings', [WhatsAppController::class, 'settings']);
                Route::post('/settings', [WhatsAppController::class, 'save_settings']);
                Route::get('/regenerate-token', [WhatsAppController::class, 'regenerate_token']);
                Route::get('/conversations', [WhatsAppController::class, 'conversations']);
                Route::get('/conversations/takeover-{id}', [WhatsAppController::class, 'takeover']);
                Route::post('/conversations/reply-{id}', [WhatsAppController::class, 'reply']);
                Route::get('/knowledge', [WhatsAppController::class, 'knowledge']);
                Route::post('/knowledge/save', [WhatsAppController::class, 'knowledge_save']);
                Route::post('/knowledge/save-{id}', [WhatsAppController::class, 'knowledge_save']);
                Route::get('/knowledge/delete-{id}', [WhatsAppController::class, 'knowledge_delete']);
                Route::get('/knowledge/status-{id}/{status}', [WhatsAppController::class, 'knowledge_status']);
            });

            // V2 LOCATIONS & BRANCHES (vendor side) — GPS/map driven, no manual city or area.
            Route::group(['prefix' => 'branches'], function () {
                Route::get('/', [BranchController::class, 'index']);
                Route::get('/add', [BranchController::class, 'add']);
                Route::get('/edit-{id}', [BranchController::class, 'edit']);
                Route::post('/save', [BranchController::class, 'save']);
                Route::post('/save-{id}', [BranchController::class, 'save']);
                Route::get('/delete-{id}', [BranchController::class, 'delete']);
            });
            // Admin Locations & Marketplace coverage
            Route::group(['prefix' => 'locations'], function () {
                Route::get('/', [LocationController::class, 'index']);
                Route::post('/review-{id}', [LocationController::class, 'review']);
            });
            // V2 SETUP MODE — System/Activity/Specialization + website activation
            Route::group(['prefix' => 'setup'], function () {
                Route::get('/', [SetupController::class, 'index']);
                Route::post('/save-{step}', [SetupController::class, 'save']);
                Route::post('/submit', [SetupController::class, 'submit']);
                Route::get('/specializations', [SetupController::class, 'specializations']);
                Route::get('/agreement', [SetupController::class, 'agreement']);
                Route::get('/document/delete-{id}', [SetupController::class, 'delete_document']);
            });
            // PLANS
            Route::get('plan', [PlanPricingController::class, 'view_plan']);
            Route::get('/themeimages', [PlanPricingController::class, 'themeimages']);
            // PAYMENT
            Route::group(
                ['prefix' => 'payment'],
                function () {
                    Route::get('/', [PaymentController::class, 'index']);
                    Route::post('update', [PaymentController::class, 'update']);
                    Route::post('/reorder_payment', [PaymentController::class, 'reorder_payment']);
                }
            );
            // inquiries
            Route::get('/inquiries', [OtherPagesController::class, 'inquiries']);
            Route::get('/inquiries/delete-{id}', [OtherPagesController::class, 'inquiries_delete']);
            Route::get('/inquiries/view-{id}', [OtherPagesController::class, 'inquiries_view']);
            Route::post('/inquiries/update-{id}', [OtherPagesController::class, 'inquiries_update']);
            Route::get('/inquiries/archive-{id}', [OtherPagesController::class, 'inquiries_archive']);

            // Other Pages
            Route::get('/subscribers', [OtherPagesController::class, 'subscribers']);
            Route::get('/subscribers/delete-{id}', [OtherPagesController::class, 'subscribers_delete']);
            Route::get('/subscribers/status-{id}/{status}', [OtherPagesController::class, 'subscribers_status']);

            Route::get('privacy-policy', [OtherPagesController::class, 'privacypolicy']);
            Route::get('refund-policy', [OtherPagesController::class, 'refundpolicy']);
            Route::post('refund-policy/update', [OtherPagesController::class, 'refundpolicy_update']);
            Route::post('privacy-policy/update', [OtherPagesController::class, 'privacypolicy_update']);
            Route::get('terms-conditions', [OtherPagesController::class, 'termscondition']);
            Route::post('terms-conditions/update', [OtherPagesController::class, 'termscondition_update']);
            Route::get('aboutus', [OtherPagesController::class, 'aboutus']);
            Route::post('aboutus/update', [OtherPagesController::class, 'aboutus_update']);

            //shipping
            Route::group(['prefix' => 'shipping'], function () {
                Route::get('/', [OtherPagesController::class, 'shippingindex']);
                Route::post('/savecontent', [OtherPagesController::class, 'savecontent']);
            });

            // tax
            Route::group(
                ['prefix' => 'tax'],
                function () {
                    Route::get('/', [TaxController::class, 'index']);
                    Route::get('add', [TaxController::class, 'add']);
                    Route::post('save', [TaxController::class, 'save']);
                    Route::get('edit-{id}', [TaxController::class, 'edit']);
                    Route::post('update-{id}', [TaxController::class, 'update']);
                    Route::get('change_status-{id}/{status}', [TaxController::class, 'change_status']);
                    Route::get('delete-{id}', [TaxController::class, 'delete']);
                    Route::post('reorder_tax', [TaxController::class, 'reorder_tax']);
                }
            );

            //   FAQs
            Route::group(
                ['prefix' => 'faqs'],
                function () {
                    Route::get('/', [OtherPagesController::class, 'faq_index']);
                    Route::get('/add', [OtherPagesController::class, 'faq_add']);
                    Route::post('/save', [OtherPagesController::class, 'faq_save']);
                    Route::get('/edit-{id}', [OtherPagesController::class, 'faq_edit']);
                    Route::post('/update-{id}', [OtherPagesController::class, 'faq_update']);
                    Route::get('/delete-{id}', [OtherPagesController::class, 'faq_delete']);
                    Route::post('/reorder_faq', [OtherPagesController::class, 'reorder_faq']);
                }
            );
            Route::post('social_links/update', [SettingsController::class, 'social_links_update']);
            Route::post('settings/otherdata/update', [SettingsController::class, 'otherdataupdate']);
            Route::get('settings/delete-sociallinks-{id}', [SettingsController::class, 'delete_sociallinks']);
            Route::post('settings/safe-secure-store', [SettingsController::class, 'safe_secure_store']);
            Route::post('tips_settings/update', [SettingsController::class, 'tips_settings']);
            Route::post('/orders/customerinfo/', [OrderController::class, 'customerinfo']);
            Route::post('/orders/vendor_note/', [OrderController::class, 'vendor_note']);
            Route::post('fun_fact/update', [SettingsController::class, 'fun_fact_update']);
            Route::get('settings/delete-feature-{id}', [SettingsController::class, 'delete_feature']);

            Route::middleware('adminmiddleware')->group(
                function () {
                    Route::get('transaction-{id}-{status}', [TransactionController::class, 'status']);
                    // PLAN
                    Route::group(
                        ['prefix' => 'plan'],
                        function () {
                            Route::get('add', [PlanPricingController::class, 'add_plan']);
                            Route::post('save_plan', [PlanPricingController::class, 'save_plan']);
                            Route::get('edit-{id}', [PlanPricingController::class, 'edit_plan']);
                            Route::post('update_plan-{id}', [PlanPricingController::class, 'update_plan']);
                            Route::get('status_change-{id}/{status}', [PlanPricingController::class, 'status_change']);
                            Route::get('delete-{id}', [PlanPricingController::class, 'delete']);
                            Route::post('reorder_plan', [PlanPricingController::class, 'reorder_plan']);
                        }
                    );
                    // VENDORS
                    Route::group(
                        ['prefix' => 'users'],
                        function () {
                            Route::get('/', [VendorController::class, 'index']);
                            Route::get('add', [VendorController::class, 'add']);
                            Route::get('edit-{slug}', [VendorController::class, 'edit']);
                            Route::post('update-{slug}', [VendorController::class, 'update']);
                            Route::get('status-{slug}/{status}', [VendorController::class, 'status']);
                            Route::get('login-{id}', [VendorController::class, 'vendor_login']);
                            Route::post('/store/page/is_allow', [VendorController::class, 'is_allow']);
                            Route::get('delete-{id}', [VendorController::class, 'deletevendor']);
                            // Vendor 360 — archive instead of delete, plus the unified vendor record.
                            Route::get('archive-{id}', [VendorController::class, 'archive']);
                            Route::get('restore-{id}', [VendorController::class, 'restore']);
                            Route::get('sandbox-{id}', [VendorController::class, 'sandbox']);
                            Route::get('record-{id}', [VendorRecordController::class, 'show']);
                            Route::post('record-{id}/status', [VendorRecordController::class, 'update_status']);
                            Route::post('record-{id}/note', [VendorRecordController::class, 'save_note']);
                            Route::post('record-{id}/document-{docId}/review', [VendorRecordController::class, 'review_document']);
                        }
                    );

                    //features
                    Route::group(
                        ['prefix' => 'features'],
                        function () {
                            Route::get('/', [FeaturesController::class, 'index']);
                            Route::get('/add', [FeaturesController::class, 'add']);
                            Route::post('/save', [FeaturesController::class, 'save']);
                            Route::get('/edit-{id}', [FeaturesController::class, 'edit']);
                            Route::post('/update-{id}', [FeaturesController::class, 'update']);
                            Route::get('/delete-{id}', [FeaturesController::class, 'delete']);
                            Route::post('/reorder_features', [FeaturesController::class, 'reorder_features']);
                        }
                    );

                    // citys
                    Route::group(
                        ['prefix' => 'cities'],
                        function () {
                            Route::get('/', [OtherPagesController::class, 'cities']);
                            Route::get('/add', [OtherPagesController::class, 'add_city']);
                            Route::post('/save', [OtherPagesController::class, 'save_city']);
                            Route::get('/edit-{id}', [OtherPagesController::class, 'edit_city']);
                            Route::post('/update-{id}', [OtherPagesController::class, 'update_city']);
                            Route::get('/delete-{id}', [OtherPagesController::class, 'delete_city']);
                            Route::get('/change_status-{id}/{status}', [OtherPagesController::class, 'statuschange_city']);
                            Route::post('/reorder_city', [OtherPagesController::class, 'reorder_city']);
                        }
                    );

                    // areas
                    Route::group(
                        ['prefix' => 'areas'],
                        function () {
                            Route::get('/', [OtherPagesController::class, 'areas']);
                            Route::get('/add', [OtherPagesController::class, 'add_area']);
                            Route::post('/save', [OtherPagesController::class, 'save_area']);
                            Route::get('/edit-{id}', [OtherPagesController::class, 'edit_area']);
                            Route::post('/update-{id}', [OtherPagesController::class, 'update_area']);
                            Route::get('/delete-{id}', [OtherPagesController::class, 'delete_area']);
                            Route::get('/change_status-{id}/{status}', [OtherPagesController::class, 'statuschange_area']);
                            Route::post('/reorder_area', [OtherPagesController::class, 'reorder_area']);
                        }
                    );
                    // promotional banner
                    Route::group(
                        ['prefix' => 'promotionalbanners'],
                        function () {
                            Route::get('/', [BannerController::class, 'promotional_banner']);
                            Route::get('add', [BannerController::class, 'promotional_banneradd']);
                            Route::get('edit-{id}', [BannerController::class, 'promotional_banneredit']);
                            Route::post('save', [BannerController::class, 'promotional_bannersave_banner']);
                            Route::post('update-{id}', [BannerController::class, 'promotional_bannerupdate']);
                            Route::get('delete-{id}', [BannerController::class, 'promotional_bannerdelete']);
                            Route::post('reorder_promotionalbanner', [BannerController::class, 'reorder_promotionalbanner']);
                        }
                    );
                    // STORE CATEGORIES
                    Route::group(
                        ['prefix' => 'store_categories'],
                        function () {
                            Route::get('/', [StoreCategoryController::class, 'index']);
                            Route::get('add', [StoreCategoryController::class, 'add_category']);
                            Route::post('save', [StoreCategoryController::class, 'save_category']);
                            Route::get('edit-{id}', [StoreCategoryController::class, 'edit_category']);
                            Route::post('update-{id}', [StoreCategoryController::class, 'update_category']);
                            Route::get('change_status-{id}/{status}', [StoreCategoryController::class, 'change_status']);
                            Route::get('delete-{id}', [StoreCategoryController::class, 'delete_category']);
                            Route::post('/reorder_category', [StoreCategoryController::class, 'reorder_category']);
                        }
                    );

                    // theme
                    Route::get('/themes', [ThemeController::class, 'index']);
                    Route::get('themes/add', [ThemeController::class, 'add']);
                    Route::post('/themes/save', [ThemeController::class, 'save']);
                    Route::get('/themes/edit-{id}', [ThemeController::class, 'edit']);
                    Route::post('/themes/update-{id}', [ThemeController::class, 'update']);
                    Route::get('/themes/delete-{id}', [ThemeController::class, 'delete']);
                    Route::post('/themes/reorder_theme', [ThemeController::class, 'reorder_theme']);

                    // how works
                    Route::get('/how_works', [WorksController::class, 'index']);
                    Route::post('/how_works/savecontent', [WorksController::class, 'savecontent']);
                    Route::get('/how_works/add', [WorksController::class, 'add']);
                    Route::get('/how_works/edit-{id}', [WorksController::class, 'edit']);
                    Route::post('/how_works/update-{id}', [WorksController::class, 'update']);
                    Route::post('/how_works/save', [WorksController::class, 'save']);
                    Route::get('/how_works/delete-{id}', [WorksController::class, 'delete']);
                    Route::post('how_works/reorder_status', [WorksController::class, 'reorder_status']);

                    Route::post('/landingsettings', [SettingsController::class, 'landingsettings']);
                }
            );
            Route::middleware('VendorMiddleware')->group(
                function () {
                    // OTHERS
                    Route::get('settings/delete-banner', [SettingsController::class, 'delete_viewall_page_image']);
                    Route::get('share', [OtherPagesController::class, 'share']);
                    Route::get('getorder', [NotificationController::class, 'getorder']);
                    Route::post('app_section/update', [SettingsController::class, 'app_section']);
                    // TIME
                    Route::group(
                        ['prefix' => 'time'],
                        function () {
                            Route::get('/', [TimeController::class, 'index']);
                            Route::post('store', [TimeController::class, 'store']);
                        }
                    );
                    // ORDERS
                    Route::get('/report', [OrderController::class, 'index']);
                    // V2 AI Content Assistant (modular — one endpoint for all AI text features)
                    Route::post('/ai/assist', [\App\Http\Controllers\admin\AiController::class, 'assist']);
                    // V2 AI Store Builder (post-registration auto-setup)
                    Route::get('/store-setup', [\App\Http\Controllers\admin\AiBuildController::class, 'setup']);
                    Route::post('/store-setup/extract', [\App\Http\Controllers\admin\AiBuildController::class, 'extract']);
                    Route::post('/store-setup/build', [\App\Http\Controllers\admin\AiBuildController::class, 'build']);
                    // V2 Booking module (dashboard)
                    Route::get('/bookings', [BookingController::class, 'index']);
                    Route::post('/bookings/status', [BookingController::class, 'updatestatus']);
                    // V2 Booking — Services catalog (dashboard)
                    Route::get('/booking-services', [BookingServiceController::class, 'index']);
                    Route::post('/booking-services', [BookingServiceController::class, 'store']);
                    Route::post('/booking-services/update', [BookingServiceController::class, 'update']);
                    Route::post('/booking-services/status', [BookingServiceController::class, 'updatestatus']);
                    Route::post('/booking-services/delete', [BookingServiceController::class, 'destroy']);
                    // V2 Clinic module — doctors (dashboard, clinic business type only)
                    Route::get('/doctors', [\App\Http\Controllers\admin\DoctorController::class, 'index']);
                    Route::post('/doctors', [\App\Http\Controllers\admin\DoctorController::class, 'store']);
                    Route::post('/doctors/update', [\App\Http\Controllers\admin\DoctorController::class, 'update']);
                    Route::post('/doctors/status', [\App\Http\Controllers\admin\DoctorController::class, 'updatestatus']);
                    Route::post('/doctors/delete', [\App\Http\Controllers\admin\DoctorController::class, 'destroy']);
                    // V2 Service Request module (dashboard)
                    Route::get('/service-requests', [ServiceRequestController::class, 'index']);
                    Route::post('/service-requests/status', [ServiceRequestController::class, 'updatestatus']);

                    Route::group(
                        ['prefix' => 'orders'],
                        function () {
                            Route::get('/', [OrderController::class, 'index']);
                            Route::get('/update-{id}-{status}-{type}', [OrderController::class, 'update']);
                            Route::get('/invoice/{order_number}', [OrderController::class, 'invoice']);
                            Route::get('/print/{order_number}', [OrderController::class, 'print']);
                            Route::post('/payment_status-{status}', [OrderController::class, 'payment_status']);
                            Route::get('/generatepdf/{order_number}', [OrderController::class, 'generatepdf']);
                        }
                    );
                    // CATEGORIES
                    Route::group(
                        ['prefix' => 'categories'],
                        function () {
                            Route::get('/', [CategoryController::class, 'index']);
                            Route::get('add', [CategoryController::class, 'add_category']);
                            Route::post('save', [CategoryController::class, 'save_category']);
                            Route::get('edit-{slug}', [CategoryController::class, 'edit_category']);
                            Route::post('update-{slug}', [CategoryController::class, 'update_category']);
                            Route::get('change_status-{slug}/{status}', [CategoryController::class, 'change_status']);
                            Route::get('delete-{slug}', [CategoryController::class, 'delete_category']);
                            Route::post('reorder_category', [CategoryController::class, 'reorder_category']);
                        }
                    );
                    // PRODUCTS
                    Route::group(
                        ['prefix' => 'products'],
                        function () {
                            Route::get('/', [ProductController::class, 'index']);
                            Route::get('add', [ProductController::class, 'add']);
                            Route::post('save', [ProductController::class, 'save']);
                            Route::get('edit-{slug}', [ProductController::class, 'edit']);
                            Route::post('update-{slug}', [ProductController::class, 'update_product']);
                            Route::post('updateimage', [ProductController::class, 'update_image']);
                            Route::post('storeimages', [ProductController::class, 'store_image']);
                            Route::post('destroyimage', [ProductController::class, 'destroyimage']);
                            Route::get('status-{slug}/{status}', [ProductController::class, 'status']);
                            Route::get('delete/variation-{id}-{product_id}', [ProductController::class, 'delete_variation']);
                            Route::get('delete/extras-{id}', [ProductController::class, 'delete_extras']);
                            Route::get('delete-{slug}', [ProductController::class, 'delete_product']);
                            Route::post('/product-variants-possibilities/{product_id}', [ProductController::class, 'getProductVariantsPossibilities']);
                            Route::get('/get-product-variants-possibilities', [ProductController::class, 'getProductVariantsPossibilities']);
                            Route::get('/variants/edit/{product_id}', [ProductController::class, 'productVariantsEdit']);
                            Route::post('reorder_product', [ProductController::class, 'reorder_product']);
                            Route::post('/reorder_image-{item_id}', [ProductController::class, 'reorder_image']);
                        }
                    );

                    // extras
                    Route::get('/getextras', [GlobalExtrasController::class, 'getextras']);
                    Route::get('/editgetextras-{id}', [GlobalExtrasController::class, 'editgetextras']);
                    Route::group(
                        ['prefix' => 'extras'],
                        function () {
                            Route::get('/', [GlobalExtrasController::class, 'index']);
                            Route::get('/add', [GlobalExtrasController::class, 'add']);
                            Route::post('/save', [GlobalExtrasController::class, 'save']);
                            Route::get('/edit-{id}', [GlobalExtrasController::class, 'edit']);
                            Route::post('/update-{id}', [GlobalExtrasController::class, 'update']);
                            Route::get('/change_status-{id}/{status}', [GlobalExtrasController::class, 'change_status']);
                            Route::get('delete-{id}', [GlobalExtrasController::class, 'delete']);
                            Route::post('/reorder_extras', [GlobalExtrasController::class, 'reorder_extras']);
                        }
                    );
                    // Media
                    Route::group(
                        ['prefix' => 'media'],
                        function () {
                            Route::get('/', [MediaController::class, 'index']);
                            Route::post('/add_image', [MediaController::class, 'add_image']);
                            Route::get('delete-{id}', [MediaController::class, 'delete_media']);
                            Route::get('download-{id}', [MediaController::class, 'download']);
                        }
                    );
                    // PLAN
                    Route::group(
                        ['prefix' => 'plan'],
                        function () {
                            Route::get('selectplan-{id}', [PlanPricingController::class, 'select_plan']);
                            Route::post('buyplan', [PlanPricingController::class, 'buyplan']);
                            Route::any('buyplan/paymentsuccess/success', [PlanPricingController::class, 'success']);
                        }
                    );
                    // BANNERS
                    Route::group(
                        ['prefix' => 'banner'],
                        function () {
                            Route::get('/', [BannerController::class, 'index'])->name('banner');
                            Route::get('/add', [BannerController::class, 'add']);
                            Route::post('/store', [BannerController::class, 'store']);
                            Route::get('/edit-{id}', [BannerController::class, 'show']);
                            Route::post('/update-{id}', [BannerController::class, 'update']);
                            Route::get('/delete-{id}', [BannerController::class, 'delete']);
                            Route::post('/reorder_banner', [BannerController::class, 'reorder_banner']);
                        }
                    );

                    Route::group(
                        ['prefix' => 'whoweare'],
                        function () {
                            Route::get('/', [WhoWeAreController::class, 'index']);
                            Route::get('/add', [WhoWeAreController::class, 'add']);
                            Route::get('/edit-{id}', [WhoWeAreController::class, 'edit']);
                            Route::post('/savecontent', [WhoWeAreController::class, 'savecontent']);
                            Route::post('/save', [WhoWeAreController::class, 'save']);
                            Route::post('/update-{id}', [WhoWeAreController::class, 'update']);
                            Route::get('/delete-{id}', [WhoWeAreController::class, 'delete']);
                            Route::post('/reorder_whoweare', [WhoWeAreController::class, 'reorder_whoweare']);
                        }
                    );
                }
            );
            // currency-setting
            Route::group(['prefix' => 'currency-settings'], function () {
                Route::get('/', [CurrencyController::class, 'index']);
                Route::get('/currency/edit-{id}', [CurrencyController::class, 'edit']);
                Route::post('/update-{id}', [CurrencyController::class, 'update']);
            });
        }
    );
});


// ---- Public registration wizard: system+activity -> business -> verification -> plan+payment ----
Route::group(['prefix' => 'register'], function () {
    Route::get('/', fn() => redirect('register/1'));
    Route::get('/specializations', [\App\Http\Controllers\RegistrationController::class, 'specializations']);
    Route::get('/agreement', [\App\Http\Controllers\RegistrationController::class, 'agreement']);
    Route::post('/system', [\App\Http\Controllers\RegistrationController::class, 'save_system']);
    Route::post('/complete', [\App\Http\Controllers\RegistrationController::class, 'complete']);
    Route::get('/{step}', [\App\Http\Controllers\RegistrationController::class, 'show'])
        ->where('step', '[1-3]')->middleware('guest');   // no signup wizard while signed in
});

// ---- SEO: sitemap for search engines (listed in the domain root robots.txt) ----
Route::get('sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index']);

// ---- WhatsApp Cloud API webhook (public: Meta calls these, no auth, no CSRF) ----
Route::get('webhook/whatsapp', [\App\Http\Controllers\WhatsAppWebhookController::class, 'verify']);
Route::post('webhook/whatsapp', [\App\Http\Controllers\WhatsAppWebhookController::class, 'receive']);

// One-click unsubscribe from a marketing email — public, token-based, no login needed.
Route::get('unsubscribe/{token}', [\App\Http\Controllers\UnsubscribeController::class, 'unsubscribe']);
Route::get('resubscribe/{token}', [\App\Http\Controllers\UnsubscribeController::class, 'resubscribe']);

Route::get('login/google', [VendorController::class, 'redirectToGoogle']);
Route::get('login/google/callback', [VendorController::class, 'handleGoogleCallback']);
Route::get('login/facebook', [VendorController::class, 'redirectToFacebook']);
Route::get('login/facebook/callback', [VendorController::class, 'handleFacebookCallback']);


//  ------------------------------- ----------- -----------------------------------------   //
//  -------------------------------  FOR WEB/FRONT  -------------------------------------   //
//  ------------------------------- ----------- -----------------------------------------   //

Route::group(['namespace' => '', 'middleware' => 'landingMiddleware'], function () {
    Route::get('/', [LandingHomeController::class, 'index']);
    Route::post('/emailsubscribe', [LandingHomeController::class, 'emailsubscribe']);
    Route::post('/inquiry', [LandingHomeController::class, 'inquiry']);

    Route::get('/about_us', [LandingHomeController::class, 'about_us']);
    Route::get('/privacy_policy', [LandingHomeController::class, 'privacy_policy']);
    Route::get('/terms_condition', [LandingHomeController::class, 'terms_condition']);
    Route::get('/refund_policy', [LandingHomeController::class, 'refund_policy']);
    Route::get('/faqs', [LandingHomeController::class, 'faqs']);

    // LandingHomeController has no contact() method — the form lives in the #contact section of
    // the landing page. Redirect instead of 500-ing.
    Route::get('/contact', fn() => redirect(URL::to('/') . '#contact'));
    Route::get('/stores', [LandingHomeController::class, 'allstores']);
    Route::get('/marketplace', [LandingHomeController::class, 'marketplace']);
    Route::get('/blog_list', [LandingHomeController::class, 'blogs']);
    Route::get('/blog_details-{id}', [LandingHomeController::class, 'blogs_details']);
    Route::post('/getarea', [VendorController::class, 'getarea']);
});


$domain = env('WEBSITE_HOST');
$prefix = '{vendor}';
$parsedUrl = parse_url(url()->current());
$host = $parsedUrl['host'] ?? null;
$websiteHost = parse_url(
    str_contains(env('WEBSITE_HOST', ''), '://') ? env('WEBSITE_HOST') : 'http://' . env('WEBSITE_HOST'),
    PHP_URL_HOST
);

if (!empty($host) && !empty($websiteHost)) {
    // Match only the hostname here because parse_url(url()->current()) excludes the port.
    if ($host !== $websiteHost) {
        $prefix = '';
    }
}

Route::post('/product-details', [HomeController::class, 'details'])->name('front.details');
Route::get('/product-reviews', [HomeController::class, 'reviews'])->name('front.reviews');
Route::post('/orders/checkplan', [HomeController::class, 'checkplan'])->name('front.checkplan');
Route::post('add-to-cart', [HomeController::class, 'addtocart'])->name('front.addtocart');
Route::post('/cart/qtyupdate', [HomeController::class, 'qtyupdate'])->name('front.qtyupdate');
Route::post('/cart/deletecartitem', [HomeController::class, 'deletecartitem'])->name('front.deletecartitem');
Route::post('/orders/paymentmethod', [HomeController::class, 'paymentmethod'])->name('front.whatsapporder');

Route::post('/changeqty', [HomeController::class, 'changeqty']);
Route::get('get-products-variant-quantity', [HomeController::class, 'getProductsVariantQuantity']);
Route::group(['namespace' => "front", 'prefix' => $prefix, 'middleware' => 'FrontMiddleware'], function () {

    Route::get('/', [HomeController::class, 'index'])->name('front.home');
    Route::get('/pwa', [HomeController::class, 'index']);
    Route::get('/categories', [HomeController::class, 'categories'])->name('front.categories');
    Route::get('/product/{id}', [HomeController::class, 'show'])->name('front.home');
    // V2 Booking module (storefront)
    Route::get('/booking', [HomeController::class, 'bookingpage'])->name('front.booking');
    Route::post('/save-booking', [HomeController::class, 'savebooking'])->name('front.savebooking');
    Route::get('/booking-success/{booking_number}', [HomeController::class, 'bookingsuccess'])->name('front.bookingsuccess');
    // V2 Service Request module (storefront)
    Route::get('/service', [HomeController::class, 'servicepage'])->name('front.service');
    Route::post('/save-service', [HomeController::class, 'saveservice'])->name('front.saveservice');
    Route::get('/service-success/{request_number}', [HomeController::class, 'servicesuccess'])->name('front.servicesuccess');
    Route::get('/cart', [HomeController::class, 'cart'])->name('front.cart');
    Route::get('/cart-fragment', [HomeController::class, 'cartFragment'])->name('front.cartfragment');
    Route::get('/checkout', [HomeController::class, 'checkout'])->name('front.checkout');
    Route::get('/stripe/success', [HomeController::class, 'stripeCheckoutSuccess']);
    Route::get('/search', [HomeController::class, 'search']);

    Route::get('/cancel-order/{ordernumber}', [HomeController::class, 'cancelorder'])->name('front.cancelorder');
    // third party suucess route
    Route::any('/payment', [HomeController::class, 'ordercreate']);

    Route::get('/terms', [HomeController::class, 'terms'])->name('front.terms');
    Route::get('/privacy-policy', [HomeController::class, 'privacy'])->name('front.privacy');
    Route::get('/track-order/{ordernumber}', [HomeController::class, 'trackorder'])->name('front.trackorder');
    Route::get('/success', [HomeController::class, 'trackorder'])->name('front.trackorder');
    Route::get('/success/{order_number}', [HomeController::class, 'ordersuccess']);
    Route::get('/privacypolicy', [HomeController::class, 'privacyshow']);
    Route::get('/refundprivacypolicy', [HomeController::class, 'refundprivacypolicy']);
    Route::get('/terms_condition', [HomeController::class, 'terms_condition']);
    Route::get('/aboutus', [HomeController::class, 'aboutus']);
    Route::get('/faqshow', [HomeController::class, 'faqshow']);
    Route::get('/whoweare', [HomeController::class, 'whoweareshow']);
    Route::get('/details-{slug}', [HomeController::class, 'productdetails'])->name('front.productdetails');
    Route::post('/timeslot', [HomeController::class, 'timeslot']);
    Route::post('/subscribe', [HomeController::class, 'user_subscribe']);


    Route::get('/login', [WebUserController::class, 'user_login']);
    Route::post('/checklogin-{logintype}', [WebUserController::class, 'check_login']);
    Route::get('/register', [WebUserController::class, 'user_register']);
    Route::get('/forgotpassword', [WebUserController::class, 'userforgotpassword']);

    Route::post('/send_password', [WebUserController::class, 'send_password']);

    Route::post('/register_customer', [WebUserController::class, 'register_customer']);
    Route::get('/logout', [WebUserController::class, 'logout']);

    Route::get('/profile', [WebUserController::class, 'profile']);
    Route::post('/updateprofile', [WebUserController::class, 'updateprofile']);

    Route::get('/change-password', [WebUserController::class, 'changepassword']);
    Route::post('/change_password', [WebUserController::class, 'change_password']);

    Route::get('/orders', [WebUserController::class, 'orders']);
    Route::get('/loyality', [WebUserController::class, 'loyality']);

    //CONTACTS
    Route::get('/contact', [HomeController::class, 'contact']);
    Route::post('/submit', [HomeController::class, 'save_contact']);


    //CONTACTS

    Route::get('/terms_condition', [HomeController::class, 'terms_condition']);
    Route::get('/privacypolicy', [HomeController::class, 'privacyshow']);

    // favorite
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('user-favouritelist');
    Route::post('/managefavorite', [FavoriteController::class, 'managefavorite']);

    Route::get('/delete-password', [WebUserController::class, 'deletepassword']);
    Route::get('/deleteaccount', [WebUserController::class, 'deleteaccount']);
    Route::get('/wallet', [WebUserController::class, 'wallet']);
    Route::get('/addmoney', [WebUserController::class, 'addmoneywallet']);
    Route::post('/wallet/recharge', [WebUserController::class, 'addwallet']);
    Route::any('/addwalletsuccess', [WebUserController::class, 'addsuccess']);
    Route::any('/addfail', [WebUserController::class, 'addfail']);
    Route::get('/topdeals', [HomeController::class, 'alltopdeals']);
});
