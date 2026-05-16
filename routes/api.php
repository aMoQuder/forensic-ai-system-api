<?php

use App\Http\Controllers\Api\AdminPanalController;
use Illuminate\Support\Facades\Route;;
use App\Http\Controllers\Api\EvidenceController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CaseMangment;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\DeepFakeController;
use App\Http\Controllers\Api\DnaController;
use App\Http\Controllers\Api\FacePredictionController;
use App\Http\Controllers\Api\FaceRecogController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\GoogleAuthController;

use Illuminate\Http\Request;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/password/forgot', [AuthController::class, 'sendResetCode']);
Route::post('/password/verify-code', [AuthController::class, 'verifyCode']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleLogin']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('contacts', ContactController::class);
});


Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
// 1. Dashboard Page
    Route::get('admin/dashboard', [AdminPanalController::class, 'dashboard']);

    // 2. Doctor Hub Page (All Doctors)
    Route::get('admin/doctors', [AdminPanalController::class, 'doctors']);

    // 3. Doctor Profile Page (With Modals Data)
    Route::get('admin/doctors/{id}', [AdminPanalController::class, 'doctorProfile']);

    // 4. Case Audit Page
    Route::get('admin/cases', [AdminPanalController::class, 'casesAudit']);

    // 5. Community Page
    Route::get('admin/community', [AdminPanalController::class, 'community']);

    // 6. Chat mangement Page
    Route::get('admin/chat-mangement', [AdminPanalController::class, 'chatMange']);

    // 7. make active or block
    Route::get('admin/toggle/active/{id}', [AdminPanalController::class, 'toggleUser']);

    // 8. System log  Hub Page (All admin activation)
    Route::get('admin/system-log', [AdminPanalController::class, 'SystwmLog']);

    // 9. export data in file system
    Route::get('admin/get-global-report-data', [AdminPanalController::class, 'getGlobalReportData']);

    // 10.  assign admin to doctor
    Route::get('admin/doctors/assign/admin/{id}', [AdminPanalController::class, 'assignAdmin']);

});


Route::middleware(['auth:sanctum', 'role:doctor'])->group(function () {
    // flutter

    Route::get('/doctor/dashboard-flutter', [DashboardController::class, 'DashboardFlutter']);
    Route::get('/doctor/dashboard-flutter/active-case', [DashboardController::class, 'ACtiveCasesList']);
    Route::get('/doctor/dashboard-flutter/active-case', [DashboardController::class, 'CompletedCasesList']);
    Route::get('/doctor/dashboard-flutter/evidence-list', [DashboardController::class, 'EvidenceList']);

    // dashboard web
    Route::get('/doctor/dashboard', [DashboardController::class, 'index']);

    // use-case
    Route::get('all-cases', [CaseMangment::class, 'index']);
    Route::post('add/use-case', [CaseMangment::class, 'store']);
    Route::put('update/use-case/{usecase}', [CaseMangment::class, 'update']);
    Route::delete('delete/use-case/{usecase}', [CaseMangment::class, 'destroy']);
    Route::get('show/use-case/{usecase}', [CaseMangment::class, 'show']);
    Route::get('toggle-active/use-case/{usecase}', [CaseMangment::class, 'toggle']);

    // evidence
    Route::post('save-as-evidence', [EvidenceController::class, 'store']);
    Route::put('update-evidence/{evidence}/use-case/{usecase}', [EvidenceController::class, 'update']);
    Route::delete('delete-evidence/{evidence}/use-case/{usecase}', [EvidenceController::class, 'destroy']);

    //models AI
    Route::post('/face-recognation', [FaceRecogController::class, 'store']);
    Route::post('/deep-fake', [DeepFakeController::class, 'store']);
    Route::post('/dna-analysis', [DnaController::class, 'processSequence']);
    Route::post('/face-reconstruct', [FacePredictionController::class, 'processFace']);

    // chat
    Route::get('/chat', [ChatController::class, 'getConversations']);
    Route::get('/conversations/{conversation}/messages', [ChatController::class, 'getMessages']);
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);


    // contact
    Route::apiResource('contacts', ContactController::class);

    // setting
    Route::get('/setting', [UserController::class, 'me']);
    Route::post('/upload/image-user', [AuthController::class, 'uploadImage']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/save-change', [AuthController::class, 'ChangeData']);

    //community
    Route::get('/feed', [PostController::class, 'feed']);

    // article
    Route::post('add/new-article', [PostController::class, 'store']);
    Route::post('update-article/{post_id}', [PostController::class, 'update']);
    Route::delete('delete-article/{post_id}', [PostController::class, 'destroy']);
    Route::get('view-article/{post_id}', [PostController::class, 'show']);
    Route::get('share-article/{post_id}', [PostController::class, 'share']);
    Route::post('/add-comments-article/{post_id}', [CommentController::class, 'store']);
    Route::post('/toggle-like/article/{post_id}', [LikeController::class, 'toggle']);

    // feed
    Route::post('add/new-feed', [PostController::class, 'storefeed']);
    Route::post('update-feed/{post_id}', [PostController::class, 'update']);
    Route::delete('delete-feed/{post_id}', [PostController::class, 'destroy']);
    Route::get('view-feed/{post_id}', [PostController::class, 'show']);
    Route::get('share-feed/{post_id}', [PostController::class, 'share']);
    Route::post('/add-comments-feed/{post_id}', [CommentController::class, 'store']);
    Route::post('/toggle-like/feed/{post_id}', [LikeController::class, 'toggle']);


    // comments
    Route::delete('/delete-comment/{id}', [CommentController::class, 'destroy']);
    Route::put('/update-comment/{id}', [CommentController::class, 'update']);

});

