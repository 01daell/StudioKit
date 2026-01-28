<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Workspace;
use App\Models\BrandKit;
use App\Models\Subscription;
use App\Services\PlanService;

class DashboardController extends Controller
{
    public function index(): void
    {
        $user = $this->requireAuth();
        $workspaces = Workspace::forUser($user['id']);
        if (!Session::get('workspace_id') && $workspaces) {
            Session::set('workspace_id', $workspaces[0]['id']);
        }
        $workspaceId = (int) Session::get('workspace_id');
        $kits = $workspaceId ? BrandKit::forWorkspace($workspaceId) : [];
        $subscription = $workspaceId ? Subscription::findByWorkspace($workspaceId) : null;
        $plan = PlanService::byPlan($subscription['plan'] ?? 'free');
        $this->view('app/dashboard', [
            'workspaces' => $workspaces,
            'kits' => $kits,
            'plan' => $plan,
            'subscription' => $subscription,
        ]);
    }
}
