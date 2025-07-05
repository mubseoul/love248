<?php

use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Admin\TagPixelController;
use App\Http\Controllers\Admin\EmailCampaignController;
use App\Http\Controllers\Admin\StreamsController;
use App\Http\Controllers\AdminNotificationController;

// admin panel routes
Route::get('admin/role-list', [RoleController::class, 'index'])->name('roles.index');
Route::get('admin/role-create', [RoleController::class, 'create'])->name("roles.create");
Route::get('admin/role-show/{id}', [RoleController::class, 'show'])->name("roles.show");
Route::get('admin/role-edit/{id}', [RoleController::class, 'edit'])->name("roles.edit");
Route::get('admin/role-delete/{id}', [RoleController::class, 'destroy'])->name("roles.destroy");
Route::post('admin/role-save', [RoleController::class, 'store'])->name("roles.store");
Route::post('admin/role-update/{id}', [RoleController::class, 'update'])->name("roles.update");
Route::get('admin', [Admin::class, 'dashboard'])->name('admin.dashboard');

// Vendors Related
Route::get('admin/users', [Admin::class, 'users']);
Route::get('admin/user/{user}', [Admin::class, 'showUser'])->name('admin.users.show');
Route::get('admin/user/{user}/add-tokens', [Admin::class, 'adjustTokenForm']);
Route::post('admin/save-token-balance/{user}', [Admin::class, 'saveTokenBalance']);
Route::get('admin/streamers', [Admin::class, 'streamers'])->name('admin.streamers');
Route::get('admin/approve-streamer', [Admin::class, 'approveStreamer'])->name('admin.approveStreamer');
Route::get('admin/loginAs/{vendorId}', [Admin::class, 'loginAsVendor']);
Route::get('admin/add-plan/{vendorId}', [Admin::class, 'addPlanManually']);
Route::post('admin/add-plan/{vendorId}', [Admin::class, 'addPlanManuallyProcess']);
Route::get('admin/users/setadmin/{user}', [Admin::class, 'setAdminRole']);
Route::get('admin/users/unsetadmin/{user}', [Admin::class, 'unsetAdminRole']);
Route::get('admin/users/setsubadmin/{user}', [Admin::class, 'setSubAdminRole']);
Route::get('admin/users/unsetsubadmin/{user}', [Admin::class, 'unsetSubAdminRole']);
Route::get('admin/users/ban/{user}', [Admin::class, 'banUser']);
Route::get('admin/users/unban/{user}', [Admin::class, 'unbanUser']);
Route::post('admin/update-user/{user}', [Admin::class, 'updateUser']);

Route::get('admin/streamer-bans', [Admin::class, 'streamerBans'])->name('admin.streamerBans');

// Payout Requests
Route::get('admin/payout-requests', [Admin::class, 'payoutRequests']);
Route::get('admin/payout/mark-as-paid/{withdrawal}', [Admin::class, 'markPaymentRequestAsPaid']);

// Videos
Route::get('admin/videos', [Admin::class, 'videos']);
Route::get('admin/videos/edit/{video}', [Admin::class, 'editVideo']);
Route::post('admin/videos/save/{video}', [Admin::class, 'saveVideo']);
Route::get('admin/video-approve', [Admin::class, 'approveVideo']);

//gallery
Route::get('admin/galleries', [Admin::class, 'galleries']);
Route::get('admin/gallery-approve', [Admin::class, 'approveGallery']);

// Tokens
Route::get('admin/token-sales', [Admin::class, 'tokenSales']);
Route::get('admin/token-packs', [Admin::class, 'tokenPacks']);
Route::get('admin/add-token-sale', [Admin::class, 'addTokenSale'])->name('admin.addTokenSale');
Route::post('admin/save-token-sale/{user}', [Admin::class, 'saveTokenSale']);
Route::get('admin/create-token-pack', [Admin::class, 'createTokenPack']);
Route::post('admin/add-token-pack', [Admin::class, 'addTokenPack']);
Route::get('admin/edit-token-pack/{tokenPack}', [Admin::class, 'editTokenPack']);
Route::post('admin/update-token-pack/{tokenPack}', [Admin::class, 'updateTokenPack']);

//    subscription plan ont the time basis instead of tokens
Route::get('admin/subscription-plans', [Admin::class, 'subscriptionPlans']);
Route::get('admin/create-subscription-plan', [Admin::class, 'createSubscriptionPlan']);
Route::post('admin/add-subscription-plans', [Admin::class, 'addSubscriptionPlans']);
Route::get('admin/edit-subscription-plan/{tokenPack}', [Admin::class, 'editSubscriptionPlan']);
Route::post('admin/update-subscription-plans/{tokenPack}', [Admin::class, 'updateSubscriptionPlans']);


Route::get('admin/subscription-sells', [Admin::class, 'subscriptionSales']);
Route::get('admin/add-subscription-sale', [Admin::class, 'addSubscriptionSale'])->name('admin.addSubscriptionSale');
Route::post('admin/save-subscription-sale/{user}', [Admin::class, 'saveSubscriptionSale']);


//transactions routes
Route::get('admin/user-transactions/{id}', [Admin::class, 'userTransactions'])->name('admin.user.transactions');
Route::get('admin/user-transactions-pdf/{id}', [Admin::class, 'generateUserPaymentPDF']);
Route::get('admin/user-invoice-pdf/{id}', [Admin::class, 'generateInvoicePDF']);
Route::get('admin/transactions', [Admin::class, 'transactions'])->name('admin.transactions');
Route::get('admin/transactions/export', [Admin::class, 'exportTransactionsCsv'])->name('admin.transactions.export');
Route::get('admin/transaction-pdf/{id}', [Admin::class, 'generateTransactionPDF'])->name('admin.transaction.pdf');



// Subscriptions related
Route::get('admin/subscriptions', [Admin::class, 'subscriptions']);
Route::get('admin/edit-subscription/{subscription}', [Admin::class, 'editSubscription']);
Route::get('admin/delete-subscription/{subscription}', [Admin::class, 'deleteSubscription']);
Route::post('admin/update-subscription/{subscription}', [Admin::class, 'updateSubscription']);

// Tips Related
Route::get('admin/tips', [Admin::class, 'tips']);


// Category Related
Route::get('admin/categories', [Admin::class, 'categories_overview']);
Route::post('admin/add_category', [Admin::class, 'add_category']);
Route::post('admin/update_category', [Admin::class, 'update_category']);
Route::get('admin/video-categories', [Admin::class, 'video_categories']);
Route::post('admin/add_video_category', [Admin::class, 'add_video_category']);
Route::post('admin/update_video_category', [Admin::class, 'update_video_category']);

// CMS
Route::get('admin/cms', [Admin::class, 'pages'])->name('admin-cms');
Route::post('admin/cms', [Admin::class, 'create_page']);
Route::get('admin/cms-edit-{page}', [Admin::class, 'showUpdatePage']);
Route::post('admin/cms-edit-{page}', [Admin::class, 'processUpdatePage']);
Route::get('admin/cms-delete/{page}', [Admin::class, 'deletePage']);
Route::post('admin/cms/upload-image', [Admin::class, 'uploadImageFromCMS']);

// Payments Setup
Route::get('admin/configuration/payment', [Admin::class, 'paymentsSetup']);
Route::post('admin/configuration/payment', [Admin::class, 'paymentsSetupProcess']);

// Admin config logins
Route::get('admin/config-logins', [Admin::class, 'configLogins']);
Route::post('admin/save-logins', [Admin::class, 'saveLogins'])->name('admin.save-logins');

Route::get('admin/configuration', [Admin::class, 'configuration']);
Route::get('admin/configuration/streaming', [Admin::class, 'streamingConfig']);
Route::post('admin/configuration/streaming', [Admin::class, 'saveStreamingConfig']);
Route::get('admin/configuration/chat', [Admin::class, 'chatConfig']);
Route::post('admin/configuration/chat', [Admin::class, 'saveChatConfig']);
Route::post('admin/send/whatsapp', [Admin::class, 'sendWhatsAppMessage'])->name('admin.whatsapp.messages');
Route::post('admin/configuration', [Admin::class, 'configurationProcess']);
Route::get('admin/split/commisson', [Admin::class, 'SpitConfituration']);
Route::post('admin/split/commisson', [Admin::class, 'SpitConfiturationSave']);

// Mail Server Configuration
Route::get('admin/mailconfiguration', [Admin::class, 'mailConfiguration']);
Route::post('admin/mailconfiguration', [Admin::class, 'updateMailConfiguration']);
Route::get('admin/mailtest', [Admin::class, 'mailtest']);

// Cloud settings
Route::get('admin/cloud', [Admin::class, 'cloudSettings']);
Route::post('admin/save-cloud-settings', [Admin::class, 'saveCloudSettings']);

// streamer earning
Route::get('admin/streamer-earning', [Admin::class, 'getStreamingEarning']);
Route::get('admin/videos-sales', [Admin::class, 'getVideoSales']);
Route::get('admin/gallery-sales', [Admin::class, 'getGallerySales']);
Route::get('admin/commission-list', [Admin::class, 'getCommission']);
Route::get('admin/export', [Admin::class, 'exportUserCsv'])->name('admin.exportCSV');
Route::get('admin/report/stream', [Admin::class, 'reportStream'])->name('admin.report.stream');

// Stream Management Routes
Route::prefix('admin/streams')->name('admin.streams.')->group(function () {
    Route::get('/', [StreamsController::class, 'index'])->name('index');
    Route::get('/export', [StreamsController::class, 'export'])->name('export');
    Route::get('/{id}', [StreamsController::class, 'show'])->name('show');
    Route::post('/{id}/cancel', [StreamsController::class, 'cancel'])->name('cancel');
    Route::post('/{id}/interrupt', [StreamsController::class, 'interrupt'])->name('interrupt');
    Route::post('/{id}/release-payment', [StreamsController::class, 'releasePayment'])->name('release-payment');
    Route::post('/{id}/force-release-payment', [StreamsController::class, 'forceReleasePayment'])->name('force-release-payment');
    Route::post('/{id}/refund-user', [StreamsController::class, 'refundUser'])->name('refund-user');
    Route::post('/{id}/resolve-dispute', [StreamsController::class, 'resolveDispute'])->name('resolve-dispute');
});

//report content
Route::get('admin/report/user', [Admin::class, 'reportUsers'])->name('admin.report.user');
Route::get('admin/report/content', [Admin::class, 'reportContent'])->name('admin.report.content');

Route::get('admin/user-pdf/{id}', [Admin::class, 'userPdfReport']);
Route::post('admin/user-pdf/mail', [Admin::class, 'userPdfReportSend'])->name('admin.user-pdf');

// Tag Pixels
Route::controller(TagPixelController::class)->prefix('admin/tag-pixels/')->group(function () {
    Route::get('/', 'index')->name('admin.tag-pixels.index');
    Route::get('create', 'create')->name('admin.tag-pixels.create');
    Route::post('store', 'store')->name('admin.tag-pixels.store');
    Route::get('edit/{id}', 'edit')->name('admin.tag-pixels.edit');
    Route::post('update', 'update')->name('admin.tag-pixels.update');
    Route::get('delete/{id}', 'destory')->name('admin.tag-pixels.delete');
});

Route::controller(EmailCampaignController::class)->prefix('admin/email-campaigns/')->group(function () {
    Route::get('/', 'index')->name('admin.email-campaigns.index');
    Route::get('create', 'create')->name('admin.email-campaigns.create');
    Route::post('store', 'store')->name('admin.email-campaigns.store');
    Route::get('show/{id}', 'show')->name('admin.email-campaigns.show');
    Route::get('delete/{id}', 'destroy')->name('admin.email-campaigns.destroy');
});

// SNS Notification Management Routes
Route::controller(AdminNotificationController::class)->prefix('admin/notifications/')->group(function () {
    Route::get('/', 'index')->name('admin.notifications.index');
    Route::get('send-broadcast', 'sendBroadcast')->name('admin.notifications.send-broadcast');
    Route::post('send-broadcast', 'processBroadcast')->name('admin.notifications.process-broadcast');
    Route::get('maintenance', 'maintenanceForm')->name('admin.notifications.maintenance');
    Route::post('maintenance', 'sendMaintenance')->name('admin.notifications.send-maintenance');
    Route::get('security', 'securityForm')->name('admin.notifications.security');
    Route::post('security', 'sendSecurity')->name('admin.notifications.send-security');
    Route::get('topics', 'topicManagement')->name('admin.notifications.topics');
    Route::post('topics/create', 'createTopic')->name('admin.notifications.create-topic');
    Route::get('configuration', 'configuration')->name('admin.notifications.configuration');
    Route::post('test-connection', 'testConnection')->name('admin.notifications.test-connection');
});
