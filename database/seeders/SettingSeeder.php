<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'UTB RedBricks', 'group' => 'general'],
            ['key' => 'site_email', 'value' => 'info@utbhockey.cz', 'group' => 'general'],
            ['key' => 'contact_form_email', 'value' => 'info@utbhockey.cz', 'group' => 'general'],
            ['key' => 'site_phone', 'value' => '+420 777 123 456', 'group' => 'general'],
            ['key' => 'marketing_email', 'value' => 'marketing@utbhockey.cz', 'group' => 'general'],
            ['key' => 'site_address', 'value' => "CCM Aréna\nBřeznická 4068\n760 01 Zlín", 'group' => 'general'],
            ['key' => 'office_hours', 'value' => 'Po – Pa: 9:00 – 17:00', 'group' => 'general'],
            ['key' => 'maintenance_mode', 'value' => false, 'group' => 'general'],

            ['key' => 'social_facebook', 'value' => '', 'group' => 'social'],
            ['key' => 'social_instagram', 'value' => '', 'group' => 'social'],

            ['key' => 'google_analytics_id', 'value' => '', 'group' => 'analytics'],
            ['key' => 'google_tag_manager_id', 'value' => '', 'group' => 'analytics'],

            ['key' => 'cookie_text', 'value' => 'Tento web používá cookies. Nezbytné cookies jsou nutné pro správné fungování webu a nepotřebují váš souhlas. Analytické cookies nám pomáhají zlepšovat obsah – použijeme je pouze s vaším souhlasem.', 'group' => 'gdpr'],
            ['key' => 'cookie_policy_url', 'value' => '/zasady-cookies', 'group' => 'gdpr'],
            ['key' => 'privacy_policy_url', 'value' => '/zasady-ochrany-osobnich-udaju', 'group' => 'gdpr'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group']],
            );
        }
    }
}
