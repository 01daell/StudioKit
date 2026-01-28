<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\RateLimiter;
use App\Models\BrandKit;
use App\Models\BrandAsset;
use App\Models\Color;
use App\Models\FontSelection;
use App\Models\TemplateAsset;
use App\Models\ShareLink;
use App\Models\Subscription;
use App\Models\Workspace;
use App\Services\PlanService;
use App\Services\TemplateGenerator;
use App\Services\ExportService;

class KitController extends Controller
{
    private function currentWorkspaceId(): int
    {
        return (int) Session::get('workspace_id');
    }

    private function authorizeKit(int $kitId): array
    {
        $kit = BrandKit::findById($kitId);
        if (!$kit || (int) $kit['workspace_id'] !== $this->currentWorkspaceId()) {
            http_response_code(403);
            exit('Unauthorized');
        }
        return $kit;
    }

    private function currentPlan(): array
    {
        $subscription = Subscription::findByWorkspace($this->currentWorkspaceId());
        return PlanService::byPlan($subscription['plan'] ?? 'free');
    }

    public function create(): void
    {
        $this->requireAuth();
        $plan = $this->currentPlan();
        $kits = BrandKit::forWorkspace($this->currentWorkspaceId());
        if (count($kits) >= $plan['kits']) {
            Session::flash('error', 'You have reached your kit limit for this plan.');
            $this->redirect('/app');
        }
        $this->view('app/kits-new');
    }

    public function store(): void
    {
        $this->requireAuth();
        if (!Session::verifyCsrf($this->request->input('_csrf'))) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/app');
        }
        $plan = $this->currentPlan();
        $kits = BrandKit::forWorkspace($this->currentWorkspaceId());
        if (count($kits) >= $plan['kits']) {
            Session::flash('error', 'You have reached your kit limit for this plan.');
            $this->redirect('/app');
        }
        $kitId = BrandKit::create([
            'workspace_id' => $this->currentWorkspaceId(),
            'name' => trim($this->request->input('name', 'Untitled Kit')),
            'tagline' => trim($this->request->input('tagline', '')),
            'description' => trim($this->request->input('description', '')),
            'voice_keywords' => json_encode(array_filter(array_map('trim', explode(',', $this->request->input('voice_keywords', ''))))),
            'usage_do' => trim($this->request->input('usage_do', '')),
            'usage_dont' => trim($this->request->input('usage_dont', '')),
        ]);
        $this->redirect('/app/kits/' . $kitId);
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $kit = $this->authorizeKit((int) $id);
        $colors = Color::forKit((int) $id);
        $fonts = FontSelection::forKit((int) $id);
        $assets = BrandAsset::forKit((int) $id);
        $templates = TemplateAsset::forKit((int) $id);
        $this->view('app/kit-show', [
            'kit' => $kit,
            'colors' => $colors,
            'fonts' => $fonts,
            'assets' => $assets,
            'templates' => $templates,
            'plan' => $this->currentPlan(),
        ]);
    }

    public function update(string $id): void
    {
        $this->requireAuth();
        if (!Session::verifyCsrf($this->request->input('_csrf'))) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/app/kits/' . $id);
        }
        $this->authorizeKit((int) $id);
        BrandKit::update((int) $id, [
            'name' => trim($this->request->input('name', '')),
            'tagline' => trim($this->request->input('tagline', '')),
            'description' => trim($this->request->input('description', '')),
            'voice_keywords' => json_encode(array_filter(array_map('trim', explode(',', $this->request->input('voice_keywords', ''))))),
            'usage_do' => trim($this->request->input('usage_do', '')),
            'usage_dont' => trim($this->request->input('usage_dont', '')),
        ]);
        Session::flash('message', 'Kit updated.');
        $this->redirect('/app/kits/' . $id);
    }

    public function destroy(string $id): void
    {
        $this->requireAuth();
        if (!Session::verifyCsrf($this->request->input('_csrf'))) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/app');
        }
        $this->authorizeKit((int) $id);
        BrandKit::delete((int) $id);
        Session::flash('message', 'Kit deleted.');
        $this->redirect('/app');
    }

    public function uploadAsset(string $id): void
    {
        $this->requireAuth();
        if (!Session::verifyCsrf($this->request->input('_csrf'))) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/app/kits/' . $id);
        }
        $this->authorizeKit((int) $id);
        if (!isset($this->request->files['asset'])) {
            Session::flash('error', 'No file uploaded.');
            $this->redirect('/app/kits/' . $id);
        }
        $file = $this->request->files['asset'];
        $allowed = ['image/png', 'image/jpeg', 'image/svg+xml'];
        if (!in_array($file['type'], $allowed, true)) {
            Session::flash('error', 'Invalid file type.');
            $this->redirect('/app/kits/' . $id);
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            Session::flash('error', 'File too large.');
            $this->redirect('/app/kits/' . $id);
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'logo-' . bin2hex(random_bytes(8)) . '.' . $ext;
        $target = __DIR__ . '/../../storage/uploads/' . $filename;
        move_uploaded_file($file['tmp_name'], $target);
        BrandAsset::create([
            'brand_kit_id' => (int) $id,
            'type' => 'other',
            'path' => 'uploads/' . $filename,
            'mime' => $file['type'],
            'size' => $file['size'],
            'original_name' => $file['name'],
        ]);
        Session::flash('message', 'Asset uploaded.');
        $this->redirect('/app/kits/' . $id);
    }

    public function setAssetType(string $id, string $assetId): void
    {
        $this->requireAuth();
        if (!Session::verifyCsrf($this->request->input('_csrf'))) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/app/kits/' . $id);
        }
        $this->authorizeKit((int) $id);
        $type = $this->request->input('type', 'other');
        BrandAsset::updateType((int) $assetId, $type);
        Session::flash('message', 'Asset type updated.');
        $this->redirect('/app/kits/' . $id);
    }

    public function deleteAsset(string $id, string $assetId): void
    {
        $this->requireAuth();
        if (!Session::verifyCsrf($this->request->input('_csrf'))) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/app/kits/' . $id);
        }
        $this->authorizeKit((int) $id);
        BrandAsset::delete((int) $assetId);
        Session::flash('message', 'Asset deleted.');
        $this->redirect('/app/kits/' . $id);
    }

    public function saveColors(string $id): void
    {
        $this->requireAuth();
        if (!Session::verifyCsrf($this->request->input('_csrf'))) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/app/kits/' . $id);
        }
        $this->authorizeKit((int) $id);
        $names = $this->request->input('color_name', []);
        $hexes = $this->request->input('color_hex', []);
        $colors = [];
        foreach ($names as $index => $name) {
            $hex = $hexes[$index] ?? '';
            if ($name && $hex) {
                $colors[] = [
                    'name' => trim($name),
                    'hex' => trim($hex),
                    'locked' => false,
                ];
            }
        }
        Color::replaceForKit((int) $id, $colors);
        Session::flash('message', 'Colors saved.');
        $this->redirect('/app/kits/' . $id);
    }

    public function reorderColors(string $id): void
    {
        $this->requireAuth();
        if (!Session::verifyCsrf($this->request->input('_csrf'))) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/app/kits/' . $id);
        }
        $this->authorizeKit((int) $id);
        $order = explode(',', (string) $this->request->input('order', ''));
        $colors = Color::forKit((int) $id);
        $ordered = [];
        foreach ($order as $colorId) {
            foreach ($colors as $color) {
                if ((string) $color['id'] === $colorId) {
                    $ordered[] = [
                        'name' => $color['name'],
                        'hex' => $color['hex'],
                        'locked' => (bool) $color['locked'],
                    ];
                }
            }
        }
        if ($ordered) {
            Color::replaceForKit((int) $id, $ordered);
        }
        Session::flash('message', 'Colors reordered.');
        $this->redirect('/app/kits/' . $id);
    }

    public function saveFonts(string $id): void
    {
        $this->requireAuth();
        if (!Session::verifyCsrf($this->request->input('_csrf'))) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/app/kits/' . $id);
        }
        $this->authorizeKit((int) $id);
        FontSelection::upsert((int) $id, [
            'heading_font' => $this->request->input('heading_font', 'Inter'),
            'body_font' => $this->request->input('body_font', 'Inter'),
            'heading_weights' => json_encode(['400', '600']),
            'body_weights' => json_encode(['400']),
            'source' => 'google',
        ]);
        Session::flash('message', 'Fonts saved.');
        $this->redirect('/app/kits/' . $id);
    }

    public function generateTemplates(string $id): void
    {
        $this->requireAuth();
        if (!Session::verifyCsrf($this->request->input('_csrf'))) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/app/kits/' . $id);
        }
        if (!RateLimiter::hit('templates', 10, 300)) {
            Session::flash('error', 'Too many template generations.');
            $this->redirect('/app/kits/' . $id);
        }
        $kit = $this->authorizeKit((int) $id);
        $plan = $this->currentPlan();
        $generator = new TemplateGenerator();
        $primaryColor = $this->request->input('primary_color', '#4f46e5');
        $text = $kit['name'] ?? 'Brand';

        if (in_array('social_profile', $plan['templates'], true)) {
            $path = $generator->socialProfile($text, $primaryColor, 'profile-' . time() . '.png');
            TemplateAsset::create(['brand_kit_id' => (int) $id, 'type' => 'social_profile', 'path' => $path, 'meta' => json_encode(['color' => $primaryColor])]);
        }
        if (in_array('social_banner', $plan['templates'], true)) {
            $path = $generator->socialBanner($text, $primaryColor, 'banner-' . time() . '.png');
            TemplateAsset::create(['brand_kit_id' => (int) $id, 'type' => 'social_banner', 'path' => $path, 'meta' => json_encode(['color' => $primaryColor])]);
        }
        if (in_array('favicon', $plan['templates'], true)) {
            $paths = $generator->faviconPack(substr($text, 0, 2), $primaryColor, 'favicon-' . time());
            foreach ($paths as $path) {
                TemplateAsset::create(['brand_kit_id' => (int) $id, 'type' => 'favicon', 'path' => $path, 'meta' => json_encode(['color' => $primaryColor])]);
            }
        }
        if (in_array('email_signature', $plan['templates'], true)) {
            $signature = $generator->emailSignature($this->request->input('sig_name', 'Your Name'), $this->request->input('sig_title', 'Title'), $kit['name'] ?? 'StudioKit', $primaryColor);
            $filename = 'signature-' . time() . '.html';
            file_put_contents(__DIR__ . '/../../storage/uploads/' . $filename, $signature);
            TemplateAsset::create(['brand_kit_id' => (int) $id, 'type' => 'email_signature', 'path' => 'uploads/' . $filename, 'meta' => json_encode(['color' => $primaryColor])]);
        }
        Session::flash('message', 'Templates generated.');
        $this->redirect('/app/kits/' . $id);
    }

    public function exportOptions(string $id): void
    {
        $this->requireAuth();
        $kit = $this->authorizeKit((int) $id);
        $this->view('app/kit-export', [
            'kit' => $kit,
            'plan' => $this->currentPlan(),
        ]);
    }

    public function exportPdf(string $id): void
    {
        $this->requireAuth();
        if (!RateLimiter::hit('export_pdf', 5, 300)) {
            Session::flash('error', 'Too many exports.');
            $this->redirect('/app/kits/' . $id . '/export');
        }
        $kit = $this->authorizeKit((int) $id);
        $plan = $this->currentPlan();
        $workspace = Workspace::findById($this->currentWorkspaceId());
        $exporter = new ExportService();
        $path = $exporter->pdf((int) $id, $plan, $workspace);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $kit['name'] . '-brand-guide.pdf"');
        readfile($path);
        exit;
    }

    public function exportZip(string $id): void
    {
        $this->requireAuth();
        $plan = $this->currentPlan();
        if (empty($plan['zip_export'])) {
            Session::flash('error', 'ZIP exports require a Pro plan.');
            $this->redirect('/app/kits/' . $id . '/export');
        }
        if (!RateLimiter::hit('export_zip', 3, 300)) {
            Session::flash('error', 'Too many exports.');
            $this->redirect('/app/kits/' . $id . '/export');
        }
        $kit = $this->authorizeKit((int) $id);
        $exporter = new ExportService();
        $path = $exporter->zip((int) $id);
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $kit['name'] . '-assets.zip"');
        readfile($path);
        exit;
    }

    public function share(string $id): void
    {
        $this->requireAuth();
        $kit = $this->authorizeKit((int) $id);
        $link = ShareLink::activeForKit((int) $id);
        $this->view('app/kit-share', [
            'kit' => $kit,
            'plan' => $this->currentPlan(),
            'link' => $link,
        ]);
    }

    public function createShare(string $id): void
    {
        $this->requireAuth();
        if (!Session::verifyCsrf($this->request->input('_csrf'))) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/app/kits/' . $id . '/share');
        }
        $plan = $this->currentPlan();
        if (empty($plan['share_links'])) {
            Session::flash('error', 'Share links are available on the Pro plan.');
            $this->redirect('/app/kits/' . $id . '/share');
        }
        $this->authorizeKit((int) $id);
        $token = bin2hex(random_bytes(16));
        ShareLink::create((int) $id, $token);
        Session::flash('message', 'Share link created.');
        $this->redirect('/app/kits/' . $id . '/share');
    }

    public function revokeShare(string $id): void
    {
        $this->requireAuth();
        if (!Session::verifyCsrf($this->request->input('_csrf'))) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/app/kits/' . $id . '/share');
        }
        $this->authorizeKit((int) $id);
        ShareLink::revoke((int) $id);
        Session::flash('message', 'Share link revoked.');
        $this->redirect('/app/kits/' . $id . '/share');
    }
}
