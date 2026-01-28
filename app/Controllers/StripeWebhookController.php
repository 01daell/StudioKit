<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Subscription;
use App\Services\StripeService;

class StripeWebhookController extends Controller
{
    public function handle(): void
    {
        $payload = file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $secret = config('stripe.webhook_secret', '');
        $stripe = new StripeService();
        $event = $stripe->verifyWebhook($payload, $sigHeader, $secret);
        if (!$event) {
            http_response_code(400);
            echo 'Invalid signature';
            return;
        }

        $type = $event['type'] ?? '';
        $object = $event['data']['object'] ?? [];
        if (in_array($type, ['checkout.session.completed', 'customer.subscription.updated', 'customer.subscription.created', 'customer.subscription.deleted'], true)) {
            $customerId = $object['customer'] ?? ($object['customer_details']['id'] ?? null);
            $subscriptionId = $object['subscription'] ?? $object['id'] ?? null;
            $status = $object['status'] ?? 'active';
            $currentPeriodEnd = $object['current_period_end'] ?? null;
            $plan = $this->planFromObject($object);
            $workspaceId = $this->workspaceFromMetadata($object);
            if ($workspaceId) {
                Subscription::upsert([
                    'workspace_id' => $workspaceId,
                    'stripe_customer_id' => $customerId,
                    'stripe_subscription_id' => $subscriptionId,
                    'status' => $status,
                    'plan' => $plan ?: 'free',
                    'current_period_end' => $currentPeriodEnd ? date('Y-m-d H:i:s', $currentPeriodEnd) : null,
                ]);
            }
        }

        http_response_code(200);
        echo 'ok';
    }

    private function planFromObject(array $object): ?string
    {
        $priceIds = config('stripe.price_ids', []);
        $items = $object['items']['data'] ?? [];
        foreach ($items as $item) {
            $price = $item['price']['id'] ?? null;
            foreach ($priceIds as $plan => $id) {
                if ($id === $price) {
                    return $plan;
                }
            }
        }
        return null;
    }

    private function workspaceFromMetadata(array $object): ?int
    {
        $metadata = $object['metadata'] ?? [];
        if (isset($metadata['workspace_id'])) {
            return (int) $metadata['workspace_id'];
        }
        return null;
    }
}
