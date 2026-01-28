<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\BrandKit;
use App\Models\ShareLink;
use App\Models\Color;
use App\Models\FontSelection;
use App\Models\BrandAsset;
use App\Services\PlanService;
use App\Services\ExportService;
use App\Models\Subscription;
use App\Models\Workspace;

class PublicController extends Controller
{
    public function landing(): void
    {
        $this->view('public/landing', [
            'plans' => PlanService::PLANS,
        ]);
    }

    public function pricing(): void
    {
        $this->view('public/pricing', [
            'plans' => PlanService::PLANS,
        ]);
    }

    public function faq(): void
    {
        $this->view('public/faq');
    }

    public function share(string $token): void
    {
        $link = ShareLink::findByToken($token);
        if (!$link) {
            http_response_code(404);
            echo 'Share link not found.';
            return;
        }
        $kit = BrandKit::findById((int) $link['brand_kit_id']);
        if (!$kit) {
            http_response_code(404);
            echo 'Kit not found.';
            return;
        }
        $subscription = Subscription::findByWorkspace((int) $kit['workspace_id']);
        $plan = PlanService::byPlan($subscription['plan'] ?? 'free');
        if (($this->request->query['download'] ?? '') === 'zip') {
            if (empty($plan['zip_export'])) {
                http_response_code(403);
                echo 'ZIP export requires a Pro plan.';
                return;
            }
            $exporter = new ExportService();
            $path = $exporter->zip((int) $kit['id']);
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename=\"' . $kit['name'] . '-assets.zip\"');
            readfile($path);
            exit;
        }
        $colors = Color::forKit((int) $link['brand_kit_id']);
        $fonts = FontSelection::forKit((int) $link['brand_kit_id']);
        $assets = BrandAsset::forKit((int) $link['brand_kit_id']);
        $this->view('public/share', [
            'kit' => $kit,
            'colors' => $colors,
            'fonts' => $fonts,
            'assets' => $assets,
            'plan' => $plan,
            'token' => $token,
        ]);
    }
}
