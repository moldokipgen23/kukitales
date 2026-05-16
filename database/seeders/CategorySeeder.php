<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $top = [
            ['name' => 'Short Stories',    'type' => 'story',    'icon' => '📖', 'color' => '#d4a843'],
            ['name' => 'Episode Stories',  'type' => 'episode',  'icon' => '🎭', 'color' => '#3a2a6b'],
            ['name' => 'Kuki History',     'type' => 'history',  'icon' => '🏛️', 'color' => '#6b3a2a'],
            ['name' => 'Folktales',        'type' => 'folktale', 'icon' => '🌿', 'color' => '#2a6b4a'],
            ['name' => 'Community News',   'type' => 'news',     'icon' => '📰', 'color' => '#c0392b'],
            ['name' => 'Culture & Events', 'type' => 'news',     'icon' => '🎭', 'color' => '#c0392b'],
            ['name' => 'Education',        'type' => 'news',     'icon' => '🏫', 'color' => '#c0392b'],
            ['name' => 'Politics',         'type' => 'news',     'icon' => '📌', 'color' => '#c0392b'],
            ['name' => 'Sports',           'type' => 'news',     'icon' => '🏆', 'color' => '#c0392b'],
            ['name' => 'Blog',             'type' => 'blog',     'icon' => '✍️', 'color' => '#2a4a6b'],
            ['name' => 'Music & Songs',    'type' => 'music',    'icon' => '🎵', 'color' => '#7b241c'],
            ['name' => 'Gallery',          'type' => 'gallery',  'icon' => '🖼️', 'color' => '#922b21'],
        ];

        $createdTop = [];
        foreach ($top as $i => $row) {
            $createdTop[$row['name']] = Category::updateOrCreate(
                ['name' => $row['name']],
                array_merge($row, ['sort_order' => $i, 'is_active' => true])
            );
        }

        $folktales = $createdTop['Folktales'];

        $subFolk = [
            ['name' => 'Animal Tales',           'icon' => '🐯'],
            ['name' => 'Moon & Stars',           'icon' => '🌕'],
            ['name' => 'Chief & Warrior Tales',  'icon' => '👑'],
            ['name' => 'Nature & Forest Spirits','icon' => '🌿'],
            ['name' => 'Love & Romance',         'icon' => '💘'],
            ['name' => 'Magic & Supernatural',   'icon' => '🔮'],
            ['name' => 'Battle & Bravery',       'icon' => '⚔️'],
            ['name' => 'Origin of the Hills',    'icon' => '🏔️'],
        ];

        foreach ($subFolk as $i => $row) {
            Category::updateOrCreate(
                ['name' => $row['name']],
                [
                    'type' => 'folktale',
                    'icon' => $row['icon'],
                    'color' => '#2a6b4a',
                    'parent_id' => $folktales->id,
                    'sort_order' => $i,
                    'is_active' => true,
                ]
            );
        }
    }
}
