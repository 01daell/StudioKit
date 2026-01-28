<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Subscription;
use App\Services\PlanService;
use App\Services\StripeService;

class BillingController extends Controller
{
    private function currentWorkspaceId(): int
    {
        return (int) Session::get('workspace_id');
    }

    public function index(): void
    {
        $this->requireAuth();
        $subscription = Subscription::findByWorkspace($this->currentWorkspaceId());
        $plan = PlanService::byPlan($subscription['plan'] ?? 'free');
        $this->view('app/billing', [
            'subscription' => $subscription,
            'plan' => $plan,
            'priceIds' => config('stripe.price_ids', []),
        ]);
    }

    public function checkout(): void
    {
        $this->requireAuth();
        if (!Session::verifyCsrf($this->request->input('_csrf'))) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/app/billing');
        }
        $plan = $this->request->input('plan', 'starter');
        $priceIds = config('stripe.price_ids', []);
        if (empty($priceIds[$plan])) {
            Session::flash('error', 'Stripe price ID missing for this plan.');
            $this->redirect('/app/billing');
        }
        $stripe = new StripeService();
        $session = $stripe->createCheckoutSession([
            'mode' => 'subscription',
            'line_items[0][price]' => $priceIds[$plan],
            'line_items[0][quantity]' => 1,
            'success_url' => config('stripe.success_url') ?: url('/app/billing'),
            'cancel_url' => config('stripe.cancel_url') ?: url('/app/billing'),
            'metadata[workspace_id]' => (string) $this->currentWorkspaceId(),
        ]);
        if (isset($session['error'])) {
            Session::flash('error', 'Stripe error: ' . $session['error']);
            $this->redirect('/app/billing');
        }
        header('Location: ' . $session['url']);
        exit;
    }

    public function portal(): void
    {
        $this->requireAuth();
        $subscription = Subscription::findByWorkspace($this->currentWorkspaceId());
        if (!$subscription || empty($subscription['stripe_customer_id'])) {
            Session::flash('error', 'No Stripe customer found.');
            $this->redirect('/app/billing');
        }
        $stripe = new StripeService();
        $session = $stripe->createCustomerPortal($subscription['stripe_customer_id'], url('/app/billing'));
        if (isset($session['error'])) {
            Session::flash('error', 'Stripe error: ' . $session['error']);
            $this->redirect('/app/billing');
        }
        header('Location: ' . $session['url']);
        exit;
    }
}
