<?php
namespace App\Services;

class PlanService
{
    public const PLANS = [
        'free' => [
            'name' => 'StudioKit Free',
            'kits' => 1,
            'share_links' => false,
            'zip_export' => false,
            'watermark_pdf' => true,
            'templates' => ['social_profile'],
        ],
        'starter' => [
            'name' => 'StudioKit Starter',
            'kits' => 3,
            'share_links' => false,
            'zip_export' => false,
            'watermark_pdf' => false,
            'templates' => ['social_profile', 'social_banner', 'favicon', 'email_signature'],
        ],
        'pro' => [
            'name' => 'StudioKit Pro',
            'kits' => PHP_INT_MAX,
            'share_links' => true,
            'zip_export' => true,
            'watermark_pdf' => false,
            'templates' => ['social_profile', 'social_banner', 'favicon', 'email_signature'],
        ],
        'agency' => [
            'name' => 'StudioKit Agency',
            'kits' => PHP_INT_MAX,
            'share_links' => true,
            'zip_export' => true,
            'watermark_pdf' => false,
            'templates' => ['social_profile', 'social_banner', 'favicon', 'email_signature'],
            'white_label' => true,
            'invites' => true,
        ],
    ];

    public static function defaults(): array
    {
        return self::PLANS['free'];
    }

    public static function byPlan(?string $plan): array
    {
        return self::PLANS[$plan ?? 'free'] ?? self::defaults();
    }
}
