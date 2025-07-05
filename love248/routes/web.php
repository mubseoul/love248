<?php

use App\Events\ChatMessageEvent;
use App\Events\PrivateChatMessageEvent;
use App\Events\LiveStreamStarted;
use App\Events\LiveStreamStopped;
use App\Http\Controllers\Admin;
use App\Http\Controllers\BankTransferController;
use App\Http\Controllers\BannedController;
use App\Http\Controllers\BrowseChannelsController;
use App\Http\Controllers\CCBillController;
use App\Http\Controllers\MercodaController;
use App\Http\Controllers\MercadoPlanController;
use App\Http\Controllers\MercadoPackagesController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TokensController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\StreamerVerificationController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\TipsController;
use App\Http\Controllers\VideosController;
use App\Http\Middleware\BanMiddleware;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\StripePlanController;
use App\Http\Controllers\PagarmeController;
use App\Http\Controllers\MercadoAccountController;
use App\Http\Controllers\StreamerAvailabilityController;
use App\Http\Controllers\PrivateStreamRequestController;
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


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/webhooks', [HomeController::class, 'webhooks'])->name('webhooks');
Route::get('/home', [HomeController::class, 'index']);

// Live Streaming Controller
Route::get('/channel/{user}', [ChannelController::class, 'userProfile'])->name('channel');
Route::get('/channel/live-stream/{user}', [ChannelController::class, 'liveStream'])->name('channel.livestream');
Route::get('/settings/channel', [ChannelController::class, 'channelSettings'])->name('channel.settings');
Route::post('/settings/channel/update', [ChannelController::class, 'updateChannelSettings'])->name('channel.update-settings');
Route::get('/channel/{user}/followers', [ChannelController::class, 'followers'])->name('channel.followers');
Route::get('/channel/{user}/subscribers', [ChannelController::class, 'subscribers'])->name('channel.subscribers');
Route::get('/channel/{user}/videos', [ChannelController::class, 'channelVideos'])->name('channel.videos');
Route::get('/live-channels', [BrowseChannelsController::class, 'liveNow'])->name('channels.live');
Route::get('/live-channel', [BrowseChannelsController::class, 'liveChannel'])->name('channels.liveNow');
Route::post('/channel/ban-user-from-room/{user}', [ChannelController::class, 'banUserFromRoom'])->name('channel.banUserFromRoom');
Route::get('/channel/banned-from-room/{user}', [ChannelController::class, 'bannedFromRoom'])->name('channel.bannedFromRoom');
Route::get('/channel/settings/banned-users', [ChannelController::class, 'bannedUsers'])->name('channel.bannedUsers');
Route::get('/channel/lif-user-ban/{roomban}', [ChannelController::class, 'liftUserBan'])->name('channel.liftUserBan');

// Streamer Verification
Route::get('/streamer/verify', [StreamerVerificationController::class, 'verifyForm'])->name('streamer.verify');
Route::get('/streamer/pending-verification', [StreamerVerificationController::class, 'pendingVerification'])->name('streamer.pendingVerification');
Route::post('/streamer/submit-verification', [StreamerVerificationController::class, 'submitVerification'])->name('streamer.submitVerification');

// Streamer Availability Routes
Route::prefix('streamer/availability')->name('streamer.availability.')->group(function () {
    Route::get('/', [StreamerAvailabilityController::class, 'index'])->name('index');
    Route::post('/store', [StreamerAvailabilityController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [StreamerAvailabilityController::class, 'edit'])->name('edit');
    Route::post('/update', [StreamerAvailabilityController::class, 'update'])->name('update');
    Route::post('/destroy', [StreamerAvailabilityController::class, 'destroy'])->name('destroy');
    Route::get('/data/{id}', [StreamerAvailabilityController::class, 'getAvailabilityData'])->name('data');
});

// For backward compatibility (can be removed later)
Route::get('/streamer/get-streming-time', [StreamerAvailabilityController::class, 'index'])->name('getStreamingList');
Route::post('/streamer/add-streaming', [StreamerAvailabilityController::class, 'store'])->name('addStreaming');
Route::get('/streamer/edit-streaming/{id}', [StreamerAvailabilityController::class, 'edit'])->name('streaming.edit');
Route::post('/streamer/update-streaming', [StreamerAvailabilityController::class, 'update'])->name('streaming.update');
Route::post('/streamer/delete-streaming', [StreamerAvailabilityController::class, 'destroy'])->name('streaming.delete');
Route::get('/streamer/get-streming-list/{id}', [StreamerAvailabilityController::class, 'getAvailabilityData'])->name('get-streamer-list');

// Tips
Route::post('tip/send', [TipsController::class, 'sendTip'])->name('tips.send');

// Tier Settings
Route::get('/membership/channel/set-membership-tiers', [MembershipController::class, 'setMembershipTiers'])->name('membership.set-tiers');
Route::post('/membership/channel/add-tier', [MembershipController::class, 'addTier'])->name('membership.add-tier');
Route::get('/membership/channel/edit-tier/{tier}', [MembershipController::class, 'editTier'])->name('membership.edit-tier');
Route::post('/membership/channel/update-tier/{tier}', [MembershipController::class, 'updateTier'])->name('membership.update-tier');
Route::post('/membership/channel/delete-tier', [MembershipController::class, 'deleteTier'])->name('membership.delete-tier');
Route::post('/membership/save-thanks-message', [MembershipController::class, 'saveThanks'])->name('membership.save-thanks');

// Tokens

Route::any('/get-tokens', [TokensController::class, 'getTokens'])->name('token.packages');
Route::get('/tokens/select-gateway/{tokenPack}', [TokensController::class, 'selectGateway'])->name('token.selectGateway');
Route::get('/gallery-tokens/select-gateway/{tokenPack}', [TokensController::class, 'selectGateway'])->name('gallery-tokens.selectGateway');
Route::get('/video-tokens/select-gateway/{tokenPack}', [TokensController::class, 'videoSelectGateway'])->name('video-tokens.selectGateway');

// Mercado Token Purchase
Route::match(['get', 'post'], '/token/mercado/purchase/{tokenPack}', [MercadoPackagesController::class, 'purchase'])->name('token.mercado.purchase');
Route::get('/token-packs/success', [MercadoPackagesController::class, 'success'])->name('token.mercado.success');
Route::get('/token-packs/failure', [MercadoPackagesController::class, 'failure'])->name('token.mercado.failure');
Route::get('/token-packs/pending', [MercadoPackagesController::class, 'pending'])->name('token.mercado.pending');
Route::post('/webhook/mercadopago', [MercadoPackagesController::class, 'webhook'])->name('token.mercado.webhook');
Route::get('/token/history', [ProfileController::class, 'myTokens'])->name('tokens.history');

// with mercado
Route::get('/tiers/select-gateways/{tiers}/{plan}', [SubscriptionPlanController::class, 'selectGatewaysForTiers'])->name('subscription.selectGatewaysForTiers');
Route::get('mercado/purchase-tier/{tier}/{plan}', [MercodaController::class, 'purchaseTiers'])->name('mercado.purchaseTiers');
Route::get('mercado/purchase/video/{tokenPack}', [MercodaController::class, 'videoPurchase'])->name('mercado.videoPurchase');
Route::post('mercado/purchase/subs/{plan}', [MercadoPlanController::class, 'createRecurringSubscription'])->name('mercado.createRecurringSubscription');

// PayPal
Route::get('paypal/processing', [PayPalController::class, 'processing'])->name('paypal.processing');
Route::get('stripe/purchase/{tokenPack}', [StripeController::class, 'purchaseToken'])->name('stripe.purchaseTokens');
// Stripe

Route::get('stripe/buy/{tokPack}', [StripeController::class, 'purchase'])->name('stripe.buyToks');
Route::get('stripe/payment-intent/{tokenPack}', [StripeController::class, 'paymentIntent'])->name('stripe.paymentIntent');
Route::get('stripe/order-complete/{tokenSale}', [StripeController::class, 'processOrder'])->name('stripe.processOrder');

Route::get('ccbill/purchase/{tokenPack}', [CCBillController::class, 'purchase'])->name('ccbill.purchaseTokens');

// Bank Transfer
Route::get('bank-transfer/purchase/{tokenPack}', [BankTransferController::class, 'purchase'])->name('bank.purchaseTokens');
Route::post('bank-transfer/request/{tokenPack}', [BankTransferController::class, 'confirmPurchase'])->name('bank.confirmPurchase');
Route::get('bank-transfer/request-processing', [BankTransferController::class, 'requestProcessing'])->name('bank.requestProcessing');




// subscription plan 
Route::any('/get-subscriptions', [SubscriptionPlanController::class, 'getSubscription'])->name('subscription.plan');
Route::get('/subscriptions/select-gateways/{tokenPack}', [SubscriptionPlanController::class, 'selectGateways'])->name('subscription.selectGateways');

// PayPal
Route::get('paypal/purchase/{tokenPack}', [PayPalController::class, 'purchase'])->name('paypal.purchaseTokenss');
Route::get('paypal/processing', [PayPalController::class, 'processing'])->name('paypal.processing');

// Stripe
Route::get('stripe/subscriptions-purchase/{tokPack}', [StripePlanController::class, 'purchasesss'])->name('stripe.purchaseTokenss');
Route::get('stripe/payment-intent/{tokenPack}', [StripePlanController::class, 'paymentIntents'])->name('stripe.paymentIntent');
Route::get('stripe/order-complete/{tokenSale}', [StripePlanController::class, 'processOrders'])->name('stripe.processOrder');
Route::get('mercado/subscriptions-purchase/{tokPack}', [MercadoPlanController::class, 'purchase'])->name('mercado.mercadoSubsPlanPurchase');

//mercado page
Route::get('mercado/purchase/{tokenPack}', [MercodaController::class, 'purchase'])->name('mercado.purchaseTokenss');
Route::get('mercado/purchase/video/{tokenPack}', [MercodaController::class, 'videoPurchase'])->name('mercado.videoPurchase');
Route::get('mercado/success', [MercodaController::class, 'MercadoSuccess'])->name('mercado.success');
Route::get('mercado/fail', [MercodaController::class, 'MercadoFail'])->name('mercado.fail');
Route::get('mercado/oauth', [MercodaController::class, 'SplitPayment']);
Route::get('run/oauth/url', [MercodaController::class, 'runTheOAuthurl'])->name('mercado.oauthurl');
// subscription success route.
Route::get('mercoda/subs-payment-done', [MercodaController::class, 'subsMercadoPaymentdone'])->name('mercoda.subspaymentsuccess');

// Content purchases with direct currency payment (NEW)
Route::prefix('content')->name('mercado.content.')->middleware(['auth'])->group(function () {
    Route::post('/video/purchase/{video}', [App\Http\Controllers\MercadoContentController::class, 'purchaseVideo'])->name('video.purchase');
    Route::post('/gallery/purchase/{gallery}', [App\Http\Controllers\MercadoContentController::class, 'purchaseGallery'])->name('gallery.purchase');
    Route::get('/success', [App\Http\Controllers\MercadoContentController::class, 'success'])->name('success');
    Route::get('/failure', [App\Http\Controllers\MercadoContentController::class, 'failure'])->name('failure');
    Route::get('/pending', [App\Http\Controllers\MercadoContentController::class, 'pending'])->name('pending');
});

// Private stream payments with Mercado Pago escrow
Route::prefix('private-stream')->name('mercado.private-stream.')->middleware(['auth'])->group(function () {
    Route::get('/success', [PrivateStreamRequestController::class, 'mercadoPaymentSuccess'])->name('success');
    Route::get('/failure', [PrivateStreamRequestController::class, 'mercadoPaymentFailure'])->name('failure');
    Route::post('/webhook', [PrivateStreamRequestController::class, 'mercadoWebhook'])->name('webhook')->withoutMiddleware(['auth']);
});


//pagar me
Route::get('pagarme/purchase/{tokenPack}', [PagarmeController::class, 'purchase'])->name('pagarme.purchaseTokenss');
Route::get('pagarme/checkout/{tokenPack}', [PagarmeController::class, 'PagarCard'])->name('pagarme.card');
Route::post('pagarme/card-payment', [PagarmeController::class, 'PagarCardPaymnet'])->name('pagarme.card-payment');
Route::get('pagar/success', [PagarmeController::class, 'PagarSuccess'])->name('pagarme.success');



Route::get('ccbill/purchase/{tokenPack}', [CCBillController::class, 'purchase'])->name('ccbill.purchaseTokenss');

// Bank Transfer
Route::get('bank-transfer/purchase/{tokenPack}', [BankTransferController::class, 'purchases'])->name('bank.purchaseTokenss');
Route::post('bank-transfer/request/{tokenPack}', [BankTransferController::class, 'confirmPurchases'])->name('bank.confirmPurchase');
Route::get('bank-transfer/request-processing', [BankTransferController::class, 'requestProcessings'])->name('bank.requestProcessing');

// Categories
Route::get('/browse-channels/{category?}{slug?}', [BrowseChannelsController::class, 'browse'])->name('channels.browse');

Route::get('/dashboard', [HomeController::class, 'redirectToDashboard'])->middleware(['auth', 'verified'])->name('dashboard');

// Account Settings
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::get('/followings', [ProfileController::class, 'followings'])->name('profile.followings');
Route::get('/my-tokens', [ProfileController::class, 'myTokens'])->name('profile.myTokens');
Route::get('/my-transactions', [ProfileController::class, 'userTransactions'])->name('profile.transactions');
Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
Route::get('/transaction-pdf', [ProfileController::class, 'generatePaymentPDF'])->name('transaction.pdf');
Route::get('/invoice-pdf/{id}', [ProfileController::class, 'generateInvoicePDF'])->name('invoice.pdf');
Route::get('/stream-requests', [ProfileController::class, 'StreamRequest'])->name('streamer.requests');
Route::get('/stream-requests/approve/{paymentid}/{id}', [MercodaController::class, 'CaptureStreamPayment'])->name('streamPayment.capture');
Route::get('/stream-requests/reject/{id}', [MercodaController::class, 'rejectStreamPayment'])->name('rejectPayment.capture');

// Payout Settings
Route::get('/withdrawals', [PayoutController::class, 'withdraw'])->name('payout.withdraw');
Route::post('/withdrawals/payout-request/save', [PayoutController::class, 'saveRequest'])->name('payout.saveRequest');
Route::post('/withdrawals/payout-request/cancel', [PayoutController::class, 'cancelRequest'])->name('payout.cancelRequest');
Route::post('/payout/save-settings', [PayoutController::class, 'saveSettings'])->name('payout.saveSettings');

// Subscription
Route::get('/my-subscribers', [SubscriptionController::class, 'mySubscribers'])->name('mySubscribers');
Route::get('/my-subscriptions', [SubscriptionController::class, 'mySubscriptions'])->name('mySubscriptions');
Route::get('/subscribe/channel/{channel}/tier/{tier}', [SubscriptionController::class, 'selectGateway'])->name('subscribe');
Route::get('/subscribe/confirm-subscription/channel/{user}/tier/{tier}', [SubscriptionController::class, 'confirmSubscription'])->name('confirm-subscription');
Route::get('/my-transaction', [ProfileController::class, 'userTransaction'])->name('myTransaction');

// Transaction History
Route::middleware(['auth'])->group(function () {
    Route::get('/transactions', [App\Http\Controllers\TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{transaction}', [App\Http\Controllers\TransactionController::class, 'show'])->name('transactions.show');
    Route::get('/transaction-types', [App\Http\Controllers\TransactionController::class, 'getTransactionTypes'])->name('transactions.types');
    Route::get('/transaction-statuses', [App\Http\Controllers\TransactionController::class, 'getTransactionStatuses'])->name('transactions.statuses');
});

Route::post('/report/user', [ProfileController::class, 'reportUser'])->name('user.report');
Route::post('/report/content', [ProfileController::class, 'reportContent'])->name('content.report');
Route::post('/report/stream', [ProfileController::class, 'reportStream'])->name('stream.report');
Route::get('/my-plan', [ProfileController::class, 'userplan'])->name('myPlan');
Route::get('/my-plan/remove/{id}', [ProfileController::class, 'userplanremove'])->name('removePlan');
Route::get('/my-plan/cancel', [ProfileController::class, 'cancelPlan'])->name('subscription.cancel');
// Videos
Route::get('/browse-videos/{videocategory?}{slug?}', [VideosController::class, 'browse'])->name('videos.browse');
Route::get('/video/{video}-{slug}', [VideosController::class, 'videoPage'])->name('video.page');
Route::get('/video/unlock/{video}', [VideosController::class, 'unlockVideo'])->name('video.unlock');
Route::post('/video/purchase/{video}', [VideosController::class, 'purchaseVideo'])->name('video.purchase');
Route::post('increase-views/{video}', [VideosController::class, 'increaseViews'])->name('video.increaseViews');
Route::get('/my-videos', [VideosController::class, 'myVideos'])->name('videos.ordered');
Route::get('/videos-manager', [VideosController::class, 'videosManager'])->name('videos.list');
Route::get('/upload-videos', [VideosController::class, 'uploadVideos'])->name('videos.upload');
Route::get('/edit-video/{video}', [VideosController::class, 'editVideo'])->name('videos.edit');
Route::post('/update-video/{video}', [VideosController::class, 'updateVideo'])->name('videos.update');
Route::post('/save', [VideosController::class, 'save'])->name('videos-data.save');
Route::post('/delete', [VideosController::class, 'delete'])->name('videos.delete');
Route::post('/refresh', [VideosController::class, 'refresh'])->name('videos.refresh');
Route::post('/save', [VideosController::class, 'save'])->name('videos.save');



// photo gallery (Premium Feature - Level 2+ for management/upload)
Route::get('/browse-gallery/{videocategory?}{slug?}', [GalleryController::class, 'browse'])->name('gallery.browse');
Route::get('/gallery/{gallery}-{slug}', [GalleryController::class, 'galleryPage'])->name('gallery.page');
Route::get('/gallery/unlock/{gallery}', [GalleryController::class, 'unlockGallery'])->name('gallery.unlock');
Route::post('/gallery/purchase/{gallery}', [GalleryController::class, 'purchaseGallery'])->name('gallery.purchase');
Route::post('increase-views/{gallery}', [GalleryController::class, 'increaseViews'])->name('gallery.increaseViews');

Route::get('/my-gallery', [GalleryController::class, 'myGallery'])->name('gallery.ordered');

// Protected gallery management routes (Premium+ only, but streamers bypass this requirement)
Route::middleware(['auth', 'subscription.level:premium'])->group(function () {
    Route::get('/gallery-manager', [GalleryController::class, 'galleryManager'])->name('gallery.list');
    Route::get('/upload-gallery', [GalleryController::class, 'uploadGallery'])->name('gallery.upload');
    Route::get('/edit-gallery/{gallery}', [GalleryController::class, 'editGallery'])->name('gallery.edit');
    Route::post('/update-gallery/{gallery}', [GalleryController::class, 'updateGallery'])->name('gallery.update');
    Route::post('/gallery-save', [GalleryController::class, 'save'])->name('gallery.save');
    Route::post('/gallery-delete', [GalleryController::class, 'delete'])->name('gallery.delete');
    Route::post('/gallery-refresh', [GalleryController::class, 'refresh'])->name('gallery.refresh');
});


// Contact
Route::get('/get-in-touch', [ContactController::class, 'form'])->name('contact.form');
Route::post('/get-in-touch/process', [ContactController::class, 'processForm'])->name('contact.process');

// Notifications
Route::get('notifications', [NotificationsController::class, 'inbox'])->name('notifications.inbox');

// Admin login
Route::any('admin/login', [Admin::class, 'login'])->name('admin.login');

// Banned ip
Route::get('banned', [BannedController::class, 'banned'])->name('banned-ip');

Route::get('/cron/jobs', [Admin::class, 'subsCronJob']);
Route::get('/image/detection', [Admin::class, 'handleDetectionResult'])->name('image.detection');
// Pages Routes
Route::get('p/{page}', PageController::class)->name('page');


// Auth Routes (login/register/etc.)
require __DIR__ . '/auth.php';

// Add new MercadoPlanController routes
Route::group(['prefix' => 'mercado', 'as' => 'mercado.', 'middleware' => ['auth']], function () {
    Route::get('plan/purchase/{plan}', [MercadoPlanController::class, 'purchase'])->name('planPurchase');
    Route::post('plan/process-payment', [MercadoPlanController::class, 'processPayment'])->name('processPayment');
    Route::get('plan/success', [MercadoPlanController::class, 'purchaseSuccess'])->name('subscriptionSuccess');
    Route::post('webhook', [MercadoPlanController::class, 'handleWebhook'])->name('webhook')->withoutMiddleware(['auth']);
    Route::get('verify-payment/{paymentId}', [MercadoPlanController::class, 'manualVerification'])->name('verifyPayment');
    Route::get('verify-preapproval/{preapprovalId}', [MercadoPlanController::class, 'manualPreapprovalVerification'])->name('verifyPreapproval');
    Route::post('create-test-user', [MercadoPlanController::class, 'createTestUser'])->name('createTestUser');
    Route::get('cancel-subscription', [MercadoPlanController::class, 'cancelSubscription'])->name('cancelSubscription');
});

// Mercado Account Connect Routes
Route::prefix('mercado/account')->name('mercado.account.')->middleware(['auth'])->group(function () {
    Route::get('/connect', [MercadoAccountController::class, 'generateOAuthUrl'])->name('connect');
    Route::get('/oauth/callback', [MercadoAccountController::class, 'handleOAuthCallback'])->name('oauth.callback');
    Route::post('/disconnect', [MercadoAccountController::class, 'disconnect'])->name('disconnect');
    Route::get('/check', [MercadoAccountController::class, 'checkConnection'])->name('check');
    Route::get('/debug', [MercadoAccountController::class, 'debugMercadoConnection'])->name('debug');
});

// Add secure video streaming route
Route::get('/video/stream/{id}', [App\Http\Controllers\VideosController::class, 'streamVideo'])
    ->name('video.stream')
    ->middleware(['signed']);

// Add public thumbnail serving route (accessible to everyone)
Route::get('/video/thumbnail/{id}', [App\Http\Controllers\VideosController::class, 'serveThumbnail'])
    ->name('video.thumbnail');

Route::get('/storage/gallery/{gallery}/thumbnail', [GalleryController::class, 'serveGalleryThumbnail'])->name('media.gallery.thumbnail');

// Private Stream Request Routes (Premium Feature - Level 2+)
Route::prefix('private-stream')->name('private-stream.')->middleware(['auth', 'subscription.level:premium'])->group(function () {
    Route::get('/dates/{streamerId}', [PrivateStreamRequestController::class, 'getAvailableDates'])->name('dates');
    Route::post('/time-slots/{streamerId}', [PrivateStreamRequestController::class, 'getAvailableTimeSlots'])->name('time-slots');
    Route::post('/create', [PrivateStreamRequestController::class, 'createRequest'])->name('create');
    Route::post('/payment-options', [PrivateStreamRequestController::class, 'getPaymentOptions'])->name('payment-options');
    Route::post('/confirm-payment', [PrivateStreamRequestController::class, 'confirmPayment'])->name('confirm-payment');
    Route::post('/cancel-pending-payment', [PrivateStreamRequestController::class, 'cancelPendingPayment'])->name('cancel-pending-payment');
    Route::post('/accept/{id}', [PrivateStreamRequestController::class, 'acceptRequest'])->name('accept');
    Route::post('/reject/{id}', [PrivateStreamRequestController::class, 'rejectRequest'])->name('reject');
    Route::post('/complete/{id}', [PrivateStreamRequestController::class, 'completeStream'])->name('complete');
    Route::post('/cancel/{id}', [PrivateStreamRequestController::class, 'cancelStream'])->name('cancel');
    Route::post('/no-show/{id}', [PrivateStreamRequestController::class, 'markNoShow'])->name('no-show');
    Route::get('/pending-requests', [PrivateStreamRequestController::class, 'listPendingRequests'])->name('pending-requests');
    Route::get('/pending-requests-json', [PrivateStreamRequestController::class, 'getPendingRequestsJson'])->name('pending-requests-json');
    Route::get('/upcoming-streams', [PrivateStreamRequestController::class, 'getUpcomingStreamsJson'])->name('upcoming-streams');
    Route::get('/my-requests', [PrivateStreamRequestController::class, 'getMyRequestsJson'])->name('my-requests');
    Route::get('/my-bookings', [PrivateStreamRequestController::class, 'listMyBookings'])->name('my-bookings');
    Route::get('/user-requests/{streamerId}', [PrivateStreamRequestController::class, 'getUserRequestsForStreamer'])->name('user-requests');
    Route::get('/session/{id}', [PrivateStreamRequestController::class, 'streamingSession'])->name('session');
    Route::get('/dashboard', [PrivateStreamRequestController::class, 'listPendingRequests'])->name('dashboard');

    // Chat routes for private streaming sessions
    Route::post('/{id}/chat/send', [PrivateStreamRequestController::class, 'sendChatMessage'])->name('chat.send');
    Route::get('/{id}/chat/messages', [PrivateStreamRequestController::class, 'getChatMessages'])->name('chat.messages');

    // Tip route for private streaming sessions
    Route::post('/{id}/tip/send', [PrivateStreamRequestController::class, 'sendTip'])->name('tip.send');
    
    // Stream session management routes
    Route::post('/{id}/start-countdown', [PrivateStreamRequestController::class, 'startCountdown'])->name('start-countdown');
    Route::post('/{id}/mark-user-joined', [PrivateStreamRequestController::class, 'markUserJoined'])->name('mark-user-joined');
    Route::post('/{id}/start-stream', [PrivateStreamRequestController::class, 'startStream'])->name('start-stream');
    Route::post('/{id}/end-stream', [PrivateStreamRequestController::class, 'endStream'])->name('end-stream');
    
    // Feedback and dispute routes
    Route::post('/{id}/feedback', [PrivateStreamRequestController::class, 'submitFeedback'])->name('feedback.submit');
    Route::get('/{id}/feedback/check', [PrivateStreamRequestController::class, 'checkExistingFeedback'])->name('feedback.check');
    Route::post('/{id}/dispute', [PrivateStreamRequestController::class, 'createDispute'])->name('dispute.create');
    Route::get('/{id}/feedback-details', [PrivateStreamRequestController::class, 'getFeedbackDetails'])->name('feedback.details');
});

// Token balance endpoint
Route::get('/user/token-balance', function () {
    return response()->json([
        'status' => true,
        'tokens' => auth()->user()->tokens
    ]);
})->middleware(['auth'])->name('user.token-balance');

// Room rental fee endpoint
Route::get('/private-room-settings', function () {
    return response()->json([
        'status' => true,
        'tokens_per_minute' => opt('private_room_rental_tokens_per_minute', 5)
    ]);
})->name('private-room.settings');

// Private Stream Calendar Integration
Route::get('/private-stream/{streamRequest}/calendar.ics', [PrivateStreamRequestController::class, 'generateCalendarICS'])
    ->name('private-stream.calendar.ics')
    ->middleware(['auth']);

// SNS Webhook
Route::post('/sns-webhook', [App\Http\Controllers\SNSWebhookController::class, 'handle'])->name('sns.webhook');






