<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Series;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SampleContentSeeder extends Seeder
{
    public function run(): void
    {
        // Idempotency guard — skip if sample content has already been seeded.
        // Lets us safely re-run db:seed on every deploy without duplicating posts.
        if (Post::where('title', 'The Last Songkeeper of Sadar Hills')->exists()) {
            $this->command?->info('Sample content already seeded — skipping.');
            return;
        }

        $admin  = User::where('email', 'admin@kukitales.com')->first();
        $editor = User::where('email', 'editor@kukitales.com')->first();
        $author = User::where('email', 'author@kukitales.com')->first();

        $tags = collect([
            'tradition', 'folklore', 'hills', 'legacy', 'heritage',
            'manipur', 'mizoram', 'oral history', 'culture', 'elders',
        ])->map(fn ($name) => Tag::firstOrCreate(['name' => $name]));

        $catShort   = Category::where('type', 'story')->first();
        $catEpisode = Category::where('type', 'episode')->first();
        $catHist    = Category::where('type', 'history')->first();
        $catFolk    = Category::where('name', 'Folktales')->first();
        $catFolkAnimal = Category::where('name', 'Animal Tales')->first();
        $catFolkLove   = Category::where('name', 'Love & Romance')->first();
        $catNews    = Category::where('name', 'Community News')->first();
        $catCulture = Category::where('name', 'Culture & Events')->first();
        $catBlog    = Category::where('type', 'blog')->first();
        $catMusic   = Category::where('type', 'music')->first();

        $longBody = function (string $intro) {
            return "<p>{$intro}</p>"
                . "<p>Generations have passed since these words were first whispered around a hearth in the high hills of Manipur. The smoke curls upward as elders speak — voices low, reverent — passing fragments of a world that exists nowhere but in memory and song.</p>"
                . "<p>To listen is to inherit. To inherit is to carry. And to carry, in turn, is to one day pass it on. This is the discipline of every Kuki storyteller — to remember not only the tale but the breath behind it: the cadence, the silences, the pauses where listeners lean in.</p>"
                . "<p>The hills themselves remember. The ridges that cradled the old chiefs still cradle their grandchildren. The streams that ran red during the war years run clear again, but the names of those who fell are spoken still, not loudly, but with the quiet certainty of a people who refuse to forget.</p>"
                . "<p>What follows is one such telling — gathered, translated, and offered back to those who would listen.</p>";
        };

        // 1. Featured story
        $featuredStory = Post::create([
            'title' => 'The Last Songkeeper of Sadar Hills',
            'excerpt' => 'An elder in Sadar Hills keeps alive a vanishing dialect through nightly songs — and a young niece who is finally listening.',
            'content' => $longBody('In a village clinging to the slope above Saikul, an old man sings every night just after sundown.'),
            'type' => 'story',
            'status' => 'published',
            'user_id' => $editor->id,
            'category_id' => $catShort?->id,
            'cover_image' => null,
            'is_featured' => true,
            'allow_tts' => true,
            'view_count' => 4218,
            'published_at' => now()->subDays(2),
        ]);
        $featuredStory->tags()->sync($tags->whereIn('name', ['tradition', 'elders', 'heritage'])->pluck('id'));

        // More short stories
        $stories = [
            ['Across the River, a Lantern',        'A boy carries a lantern across the Tuivai for his sister waiting on the other bank.'],
            ['The Woman Who Counted Stars',         'A grandmother in Churachandpur teaches her granddaughter the old constellations.'],
            ['Salt, Smoke, and Sunday',             'A family recipe survives three migrations and one war.'],
            ['What the Hornbill Said',              'A hunter learns silence from a bird he came to kill.'],
        ];
        foreach ($stories as [$title, $excerpt]) {
            $p = Post::create([
                'title' => $title,
                'excerpt' => $excerpt,
                'content' => $longBody($excerpt),
                'type' => 'story',
                'status' => 'published',
                'user_id' => $author->id,
                'category_id' => $catShort?->id,
                'view_count' => rand(120, 2400),
                'published_at' => now()->subDays(rand(3, 30)),
            ]);
            $p->tags()->sync($tags->random(2)->pluck('id'));
        }

        // 2. Episode series
        $series = Series::create([
            'title' => 'Kuki Legends',
            'description' => 'A serialized retelling of the great legends of the Kuki people — chiefs, warriors, lovers, and the spirits of the hills.',
            'user_id' => $editor->id,
            'category_id' => $catEpisode?->id,
            'status' => 'ongoing',
            'is_featured' => true,
        ]);
        $epTitles = [
            'Chapter One — The Chief Who Could Not Sleep',
            'Chapter Two — The River and the Sword',
            'Chapter Three — A Drum at Midnight',
        ];
        foreach ($epTitles as $i => $title) {
            $p = Post::create([
                'title' => $title,
                'excerpt' => 'A new chapter in the Kuki Legends series, drawn from oral histories of the high hills.',
                'content' => $longBody('A new chapter begins.'),
                'type' => 'episode',
                'status' => 'published',
                'user_id' => $editor->id,
                'category_id' => $catEpisode?->id,
                'series_id' => $series->id,
                'episode_number' => $i + 1,
                'view_count' => rand(400, 1800),
                'published_at' => now()->subDays(rand(1, 14)),
            ]);
            $p->tags()->sync($tags->random(2)->pluck('id'));
        }

        $series2 = Series::create([
            'title' => 'Songs of the Tuivai',
            'description' => 'Episodic poems and short prose drawn from the riverlands of the Tuivai.',
            'user_id' => $author->id,
            'category_id' => $catEpisode?->id,
            'status' => 'ongoing',
            'is_featured' => false,
        ]);
        foreach (['First Light on the Water', 'The Boatman\'s Daughter'] as $i => $title) {
            Post::create([
                'title' => $title,
                'excerpt' => 'A new entry from the Songs of the Tuivai cycle.',
                'content' => $longBody('Down by the river.'),
                'type' => 'episode',
                'status' => 'published',
                'user_id' => $author->id,
                'category_id' => $catEpisode?->id,
                'series_id' => $series2->id,
                'episode_number' => $i + 1,
                'view_count' => rand(100, 800),
                'published_at' => now()->subDays(rand(2, 20)),
            ]);
        }

        // 3. History articles
        $histArticles = [
            ['Anglo-Kuki War, 1917–1919',          '1917', 'When the chiefs of the hills said no to forced labour, the empire answered with fire.'],
            ['The Chiefs of the Hills',            '1890', 'A survey of the chieftainship system that organised village life across the Kuki belt.'],
            ['Migrations Across the Range',        '1850', 'How Kuki communities moved, settled, and re-settled across what is now Northeast India.'],
            ['Missionary Schools and the Print Word', '1910', 'The arrival of the printing press and its slow, complicated revolution in the hills.'],
        ];
        foreach ($histArticles as $i => [$title, $year, $excerpt]) {
            $p = Post::create([
                'title' => $title,
                'excerpt' => $excerpt,
                'content' => $longBody($excerpt),
                'type' => 'history',
                'status' => 'published',
                'user_id' => $editor->id,
                'category_id' => $catHist?->id,
                'year_era' => $year,
                'source' => 'KukiTales Archive',
                'is_featured' => $i === 0,
                'view_count' => rand(800, 5000),
                'published_at' => now()->subDays(rand(5, 40)),
            ]);
            $p->tags()->sync($tags->whereIn('name', ['oral history', 'heritage'])->pluck('id'));
        }

        // 4. Folktales
        $folkTales = [
            ['Why the Tiger Walks Alone',         $catFolkAnimal, 'A long-ago quarrel between Tiger and his cousins.'],
            ['The Moon\'s Borrowed Bowl',         $catFolk,       'How the moon learned to fade and return again.'],
            ['Heron and the Drowning Boy',        $catFolkAnimal, 'A heron repays a kindness from a forgotten summer.'],
            ['The Girl Who Married the Wind',     $catFolkLove,   'A love story that crosses the high passes.'],
            ['The Stone That Whispered Names',    $catFolk,       'A boulder above the village remembers everyone who passed.'],
        ];
        foreach ($folkTales as [$title, $cat, $excerpt]) {
            $p = Post::create([
                'title' => $title,
                'excerpt' => $excerpt,
                'content' => $longBody($excerpt),
                'type' => 'folktale',
                'status' => 'published',
                'user_id' => $author->id,
                'category_id' => $cat?->id,
                'source' => 'Told by elders of Saikul village',
                'view_count' => rand(200, 3200),
                'published_at' => now()->subDays(rand(1, 25)),
            ]);
            $p->tags()->sync($tags->random(2)->pluck('id'));
        }

        // 5. News (with breaking)
        $news = [
            ['Kuki Cultural Festival 2026 dates announced', true,  $catCulture],
            ['New community library opens in Churachandpur', false, $catNews],
            ['Folktale collection wins regional award',      false, $catCulture],
            ['Hill schools to introduce mother-tongue period', false, $catNews],
            ['Heritage walk planned for Anglo-Kuki War centenary', true, $catCulture],
        ];
        foreach ($news as [$title, $breaking, $cat]) {
            Post::create([
                'title' => $title,
                'excerpt' => $title . ' — full coverage from KukiTales newsroom.',
                'content' => $longBody($title),
                'type' => 'news',
                'status' => 'published',
                'user_id' => $editor->id,
                'category_id' => $cat?->id,
                'is_breaking' => $breaking,
                'view_count' => rand(300, 4000),
                'published_at' => now()->subDays(rand(0, 7)),
            ]);
        }

        // 6. Blog
        $blogs = [
            'On Carrying a Language That Wants to Disappear',
            'Three Things My Grandfather Refused to Tell Me',
            'The Quiet Politics of a Village Funeral',
        ];
        foreach ($blogs as $title) {
            $p = Post::create([
                'title' => $title,
                'excerpt' => 'A personal essay from a KukiTales contributor.',
                'content' => $longBody('An essay begins with a small admission.'),
                'type' => 'blog',
                'status' => 'published',
                'user_id' => $author->id,
                'category_id' => $catBlog?->id,
                'view_count' => rand(150, 1800),
                'published_at' => now()->subDays(rand(2, 20)),
            ]);
            $p->tags()->sync($tags->random(2)->pluck('id'));
        }

        // 7. Music
        Post::create([
            'title' => 'Lhasem Pa — A Traditional Lament',
            'excerpt' => 'A lament sung during seasons of mourning. Lyrics and brief commentary.',
            'content' => $longBody('The song begins low, then climbs.'),
            'type' => 'music',
            'status' => 'published',
            'user_id' => $editor->id,
            'category_id' => $catMusic?->id,
            'view_count' => 612,
            'published_at' => now()->subDays(6),
        ]);
    }
}
