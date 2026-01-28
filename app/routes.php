<?php
use App\Controllers\PublicController;
use App\Controllers\InstallController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\KitController;
use App\Controllers\WorkspaceController;
use App\Controllers\BillingController;
use App\Controllers\StripeWebhookController;

$router->get('/', [PublicController::class, 'landing']);
$router->get('/pricing', [PublicController::class, 'pricing']);
$router->get('/faq', [PublicController::class, 'faq']);
$router->get('/share/{token}', [PublicController::class, 'share']);

$router->get('/install', [InstallController::class, 'index']);
$router->post('/install', [InstallController::class, 'handle']);
$router->get('/install/complete', [InstallController::class, 'complete']);

$router->get('/sign-up', [AuthController::class, 'signup']);
$router->post('/sign-up', [AuthController::class, 'signupPost']);
$router->get('/sign-in', [AuthController::class, 'signin']);
$router->post('/sign-in', [AuthController::class, 'signinPost']);
$router->post('/sign-out', [AuthController::class, 'signout']);
$router->get('/forgot-password', [AuthController::class, 'forgot']);
$router->post('/forgot-password', [AuthController::class, 'forgotPost']);
$router->get('/reset-password', [AuthController::class, 'reset']);
$router->post('/reset-password', [AuthController::class, 'resetPost']);

$router->get('/app', [DashboardController::class, 'index']);
$router->get('/app/kits/new', [KitController::class, 'create']);
$router->get('/app/kits/{id}', [KitController::class, 'show']);
$router->post('/app/kits', [KitController::class, 'store']);
$router->post('/app/kits/{id}/update', [KitController::class, 'update']);
$router->post('/app/kits/{id}/delete', [KitController::class, 'destroy']);
$router->post('/app/kits/{id}/assets/upload', [KitController::class, 'uploadAsset']);
$router->post('/app/kits/{id}/assets/{assetId}/set-type', [KitController::class, 'setAssetType']);
$router->post('/app/kits/{id}/assets/{assetId}/delete', [KitController::class, 'deleteAsset']);
$router->post('/app/kits/{id}/colors/save', [KitController::class, 'saveColors']);
$router->post('/app/kits/{id}/colors/reorder', [KitController::class, 'reorderColors']);
$router->post('/app/kits/{id}/fonts/save', [KitController::class, 'saveFonts']);
$router->post('/app/kits/{id}/templates/generate', [KitController::class, 'generateTemplates']);
$router->get('/app/kits/{id}/export', [KitController::class, 'exportOptions']);
$router->get('/app/kits/{id}/export/pdf', [KitController::class, 'exportPdf']);
$router->get('/app/kits/{id}/export/zip', [KitController::class, 'exportZip']);
$router->get('/app/kits/{id}/share', [KitController::class, 'share']);
$router->post('/app/kits/{id}/share/create', [KitController::class, 'createShare']);
$router->post('/app/kits/{id}/share/revoke', [KitController::class, 'revokeShare']);
$router->get('/app/workspace/members', [WorkspaceController::class, 'members']);
$router->post('/app/workspace/invite', [WorkspaceController::class, 'invite']);
$router->get('/app/settings', [WorkspaceController::class, 'settings']);
$router->get('/app/billing', [BillingController::class, 'index']);
$router->post('/app/billing/checkout', [BillingController::class, 'checkout']);
$router->get('/app/billing/portal', [BillingController::class, 'portal']);

$router->post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
