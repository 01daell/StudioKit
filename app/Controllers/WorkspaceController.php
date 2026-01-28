<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Workspace;
use App\Models\Invite;
use App\Models\Subscription;
use App\Services\PlanService;
use App\Services\Mailer;

class WorkspaceController extends Controller
{
    private function currentWorkspaceId(): int
    {
        return (int) Session::get('workspace_id');
    }

    private function currentPlan(): array
    {
        $subscription = Subscription::findByWorkspace($this->currentWorkspaceId());
        return PlanService::byPlan($subscription['plan'] ?? 'free');
    }

    public function members(): void
    {
        $user = $this->requireAuth();
        $workspaceId = $this->currentWorkspaceId();
        $members = Workspace::forUser($user['id']);
        $invites = Invite::forWorkspace($workspaceId);
        $this->view('app/workspace-members', [
            'memberships' => $members,
            'invites' => $invites,
            'plan' => $this->currentPlan(),
        ]);
    }

    public function invite(): void
    {
        $this->requireAuth();
        if (!Session::verifyCsrf($this->request->input('_csrf'))) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/app/workspace/members');
        }
        $plan = $this->currentPlan();
        if (empty($plan['invites'])) {
            Session::flash('error', 'Invites are available on Agency plans.');
            $this->redirect('/app/workspace/members');
        }
        $email = trim($this->request->input('email', ''));
        $role = $this->request->input('role', 'MEMBER');
        $token = bin2hex(random_bytes(16));
        Invite::create([
            'workspace_id' => $this->currentWorkspaceId(),
            'email' => $email,
            'role' => $role,
            'token' => $token,
            'status' => 'pending',
            'expires_at' => date('Y-m-d H:i:s', time() + 86400 * 7),
        ]);
        $link = url('/sign-up?invite=' . $token);
        (new Mailer())->send($email, 'You are invited to StudioKit', 'Accept your invite: <a href="' . $link . '">' . $link . '</a>');
        Session::flash('message', 'Invite sent.');
        $this->redirect('/app/workspace/members');
    }

    public function settings(): void
    {
        $this->requireAuth();
        $workspace = Workspace::findById($this->currentWorkspaceId());
        $this->view('app/settings', [
            'workspace' => $workspace,
            'plan' => $this->currentPlan(),
        ]);
    }
}
