<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name' => 'KukiTales',
            'site_tagline' => 'Voices of the Hills',
            'site_description' => "Northeast India's premier cultural platform — preserving Kuki stories, history, folktales, and traditions.",
            'social_facebook' => 'https://facebook.com/kukitales',
            'social_twitter' => 'https://twitter.com/kukitales',
            'social_instagram' => 'https://instagram.com/kukitales',
            'social_youtube' => 'https://youtube.com/@kukitales',
            'breaking_news_text' => "Kuki Cultural Festival 2026 announced — Imphal | New folktale collection 'Whispers of the Hills' released | Episode 12 of Kuki Legends now live | Anglo-Kuki War centenary lectures begin next month",
            'footer_text' => "Northeast India's Premier Cultural Platform — preserving Kuki heritage for future generations.",
            'topbar_text' => "Northeast India's Cultural Platform",
            'footer_about' => "KukiTales is a community-built cultural media platform — preserving the stories, history, and songs of the Kuki people for generations to come.",
            'contact_email' => 'hello@kukitales.com',
            'contact_phone' => null,
            'contact_address' => null,
            // Mobile apps
            'app_store_url' => null,
            'play_store_url' => null,
            // Extra social
            'social_linkedin' => null,
            'social_whatsapp' => null,
            // Social login
            'social_google_enabled' => '0',
            'social_google_client_id' => null,
            'social_google_client_secret' => null,
            'social_facebook_enabled' => '0',
            'social_facebook_client_id' => null,
            'social_facebook_client_secret' => null,
            // Breaking news
            'breaking_news_enabled' => '1',
            // SEO
            'site_language' => 'en',
            'twitter_handle' => '@kukitales',
            'default_og_image' => null,
            'publisher_logo' => null,
            'google_analytics_id' => null,
            'google_tag_manager_id' => null,
            'gsc_verification' => null,
            'bing_verification' => null,
            'facebook_app_id' => null,
            'auto_ping_search_engines' => '1',
            // Donations — payment methods (admin fills these in)
            'donation_upi_id' => null,            // e.g. kukitales@upi
            'donation_bank_name' => null,
            'donation_bank_account' => null,
            'donation_bank_ifsc' => null,
            'donation_razorpay_link' => null,     // razorpay.me/kukitales
            'donation_stripe_link' => null,
            'donation_paypal_link' => null,
        ];

        // Only seed keys that don't exist yet.
        // Preserves admin-edited values when this seeder is re-run after deploys.
        $existing = SiteSetting::pluck('key')->all();
        foreach ($settings as $key => $value) {
            if (! in_array($key, $existing, true)) {
                SiteSetting::set($key, $value);
            }
        }
    }
}
