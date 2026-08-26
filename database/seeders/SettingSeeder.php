<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name',         'value' => 'Můj web',            'group' => 'general'],
            ['key' => 'site_tagline',      'value' => 'Slogan webu',        'group' => 'general'],
            ['key' => 'site_email',        'value' => 'info@example.com',   'group' => 'general'],
            ['key' => 'site_phone',        'value' => '+420 123 456 789',   'group' => 'general'],
            ['key' => 'site_address',      'value' => 'Ulice 1, 100 00 Praha', 'group' => 'general'],
            ['key' => 'site_ic',           'value' => '',                   'group' => 'general'],
            ['key' => 'site_dic',          'value' => '',                   'group' => 'general'],
            ['key' => 'site_bank_account', 'value' => '',                   'group' => 'general'],

            ['key' => 'cookie_text',        'value' => 'Tento web používá cookies. Nezbytné cookies jsou nutné pro správné fungování webu a nepotřebují váš souhlas. Analytické cookies nám pomáhají zlepšovat obsah – použijeme je pouze s vaším souhlasem.', 'group' => 'gdpr'],
            ['key' => 'cookie_policy_url',  'value' => '/zasady-cookies',   'group' => 'gdpr'],
            ['key' => 'privacy_policy_url', 'value' => '/zasady-ochrany-osobnich-udaju', 'group' => 'gdpr'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group']]
            );
        }
    }
}
