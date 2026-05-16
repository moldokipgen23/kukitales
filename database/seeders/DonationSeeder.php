<?php

namespace Database\Seeders;

use App\Models\DonationCampaign;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder
{
    public function run(): void
    {
        DonationCampaign::firstOrCreate(
            ['title' => 'Preserve Kuki Folktales — Field Recording Drive'],
            [
                'short_description' => 'Help us travel to 12 villages across Manipur and Mizoram to record elder storytellers before these tales are lost.',
                'description' => '<p>Across the Kuki belt, there are still elders who carry whole oral libraries in their memory — folktales, songs, genealogies, ritual chants. Most have never been recorded. When they are gone, those stories are gone.</p><p>This campaign funds a year of fieldwork: travel to 12 villages, an audio recordist, transcription, and translation into English. Every story we record becomes a free, permanently archived audio + transcript on KukiTales.</p><p><strong>Where your money goes:</strong></p><ul><li>Travel & lodging for the field team — ₹120,000</li><li>Audio equipment + storage — ₹60,000</li><li>Stipends for elder storytellers (a thank-you) — ₹40,000</li><li>Transcription & translation — ₹80,000</li></ul>',
                'goal_amount' => 300000,
                'currency' => 'INR',
                'beneficiary' => 'KukiTales Editorial Trust',
                'is_active' => true,
                'is_featured' => true,
            ]
        );

        DonationCampaign::firstOrCreate(
            ['title' => 'Anglo-Kuki War Centenary Lecture Series'],
            [
                'short_description' => 'A free public lecture series marking the centenary of the Anglo-Kuki War (1917–1919).',
                'description' => '<p>To honour the chiefs and villagers who resisted forced labour in 1917, we are organising a six-month public lecture series across Imphal, Aizawl, and Churachandpur. Speakers include historians, descendants of chiefs, and oral historians. All lectures are free to attend and live-streamed.</p>',
                'goal_amount' => 150000,
                'currency' => 'INR',
                'beneficiary' => 'Anglo-Kuki War Centenary Committee',
                'is_active' => true,
                'is_featured' => false,
            ]
        );
    }
}
