<?php

use App\Http\Controllers\API\LiveKitController;
use App\Http\Controllers\CCBillController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\VideosController;
use App\Http\Controllers\MercodaController;
use App\Http\Controllers\PagarmeController;
use App\Http\Controllers\MercadoPlanController;
use App\Http\Controllers\WebsiteNotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// channel is subscribed
Route::get('subscriptions/is-subscribed/{user}', [SubscriptionController::class, 'isSubscribedTo'])->name('subscription.isSubscribed');


// streaming
Route::get('stream/tiers/{user}', [ChannelController::class, 'getTiers'])->name('streaming.getTiers');
Route::post('streaming/update-status', [LiveKitController::class, 'updateOwnStatus'])->name('streaming.updateOwnStatus');
Route::post('streaming/validate-key', [LiveKitController::class, 'validateKey'])->name('streaming.validateKey');

// chat
Route::get('chat/latest-messages/{roomName}', [ChatController::class, 'latestMessages'])->name('chat.latestMessages');
Route::post('chat/send-message/{user}', [ChatController::class, 'sendMessage'])->name('chat.sendMessage');
// Route::post('chat/send-private-request', [ChatController::class, 'sendPrivateRequest'])->name('chat.privateRequest');

Route::post('chat/send-private-request', [MercodaController::class, 'mercadoPurchasePrivateStream'])->name('chat.privateRequest'); //send request from user side
Route::post('chat/start-private-chat', [ChatController::class, 'privateChatStart'])->name('chat.start');
Route::get('chat/get-private-request', [ChatController::class, 'getPrivateRequest'])->name('chat.getPrivateRequest');
Route::get('chat/cancel-streaming/{id}', [ChatController::class, 'cancelStreaming'])->name('chat.requestCacel');
Route::get('chat/accept-streaming/{id}', [ChatController::class, 'acceptStreaming'])->name('chat.requestAccept');
Route::post('chat/finished-streaming-chat', [ChatController::class, 'finishedStreamingChat'])->name('chat.finished-streaming-chat');
Route::get('chat/re-start-streaming', [ChatController::class, 'reStartStreaming'])->name('chat.re-start-streaming');
Route::get('chat/stop-streaming', [ChatController::class, 'stopStreaming'])->name('chat.stope-streaming');

// schedule
Route::get('schedule/for-channel/{user}', [ScheduleController::class, 'getSchedule'])->name('schedule.get');
Route::get('schedule/info/{user}', [ScheduleController::class, 'getScheduleInfo'])->name('schedule.getInfo');
Route::post('schedule/save', [ScheduleController::class, 'saveSchedule'])->name('schedule.save');

// follow
Route::get('follow/{user}', [ProfileController::class, 'toggleFollow'])->name('follow');

// search
Route::get('search', [ChannelController::class, 'search'])->name('channel.search');

// notifications
Route::post('notifications/mark-as-read/{notification}', [NotificationsController::class, 'markAsRead'])->name('notifications.markAsRead');
Route::post('notifications/mark-all-read', [NotificationsController::class, 'markAllRead'])->name('notifications.markAllRead');

// Direct Notifications via AWS SNS (no subscriptions)
Route::prefix('notifications')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // Push notifications
    Route::post('send-to-all', [WebsiteNotificationController::class, 'sendToAll'])->name('notify.send-all');
    Route::post('maintenance', [WebsiteNotificationController::class, 'sendMaintenanceNotification'])->name('notify.maintenance');
    Route::post('feature-announcement', [WebsiteNotificationController::class, 'sendFeatureAnnouncement'])->name('notify.feature');
    Route::post('security-alert', [WebsiteNotificationController::class, 'sendSecurityAlert'])->name('notify.security');

    // SMS notifications
    Route::post('send-sms', [WebsiteNotificationController::class, 'sendSMS'])->name('notify.sms');
    Route::post('subscribe-sms', [WebsiteNotificationController::class, 'subscribeSMS'])->name('notify.subscribe-sms');

    // Email notifications
    Route::post('send-email', [WebsiteNotificationController::class, 'sendEmail'])->name('notify.email');
    Route::post('subscribe-email', [WebsiteNotificationController::class, 'subscribeEmail'])->name('notify.subscribe-email');

    // Topic management
    Route::get('topics', [WebsiteNotificationController::class, 'getTopics'])->name('notify.topics');
    Route::post('create-topic', [WebsiteNotificationController::class, 'createTopic'])->name('notify.create-topic');
});

// video
Route::post('upload-video-chunks', [VideosController::class, 'uploadChunkedVideo'])->name('video.uploadChunks');
Route::post('upload-message-video-chunks', [VideosController::class, 'uploadMessageVideo'])->name('video.MessageVideo');

// paypal IPN
Route::any('paypal/redirect-to-processing', [PayPalController::class, 'redirect'])->name('paypal.redirect-to-processing');
Route::post('paypal/ipn', [PayPalController::class, 'ipn'])->name('paypal.ipn');

// ccbill webhooks
Route::any('ccbill/webhooks', [CCBillController::class, 'webhooks'])->name('ccbill.webhooks');

// user api modal info
Route::post('profile/modal-user-info/{user}', [ProfileController::class, 'modalUserInfo'])->name('profile.modalUserInfo');

//mercoda payment
Route::post('mercoda/payment-success', [MercodaController::class, 'mercodaPayment'])->name('mercoda.mercodaPayment');
Route::post('mercoda/video/payment-success', [MercodaController::class, 'videoMercodaPayment'])->name('mercoda.videoMercodaPayment');

// pagar payment
Route::post('pagar/pix-payment', [PagarmeController::class, 'purchaseToken'])->name('pagar.payment');
Route::post('pagar/card-payment', [PagarmeController::class, 'PagarCardPaymnet'])->name('pagar.card');
Route::post('pagar/card-checkout', [PagarmeController::class, 'PagarTransparentCheckout'])->name('pagar.checkout');
//mercado subspayment
Route::post('mercoda/subs-payment-success', [MercodaController::class, 'mercodaSubsPlanPayment'])->name('mercoda.mercodaSubsPlanPayment');
Route::post('mercoda/tier-payment-success', [MercodaController::class, 'mercodaTierPayment'])->name('mercoda.mercodaTierPayment');
Route::post('mercoda/private-stream-payment-success', [MercodaController::class, 'mercodaPrvateStreamPayment'])->name('mercoda.mercodaPrvateStreamPayment');

// Mercado Pago verification API
Route::middleware('auth')->post('verify-payment', [MercadoPlanController::class, 'apiVerifyPayment'])->name('api.verify-payment');
