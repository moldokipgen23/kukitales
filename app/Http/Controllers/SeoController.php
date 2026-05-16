<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Series;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class SeoController extends Controller
{
    /**
     * Sitemap index — lists all sub-sitemaps.
     * Google fetches this URL first, then the children.
     */
    public function sitemapIndex()
    {
        $now = now()->toAtomString();
        $base = url('/');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            . "<sitemap><loc>$base/sitemap-posts.xml</loc><lastmod>$now</lastmod></sitemap>"
            . "<sitemap><loc>$base/sitemap-categories.xml</loc><lastmod>$now</lastmod></sitemap>"
            . "<sitemap><loc>$base/sitemap-series.xml</loc><lastmod>$now</lastmod></sitemap>"
            . "<sitemap><loc>$base/sitemap-news.xml</loc><lastmod>$now</lastmod></sitemap>"
            . '</sitemapindex>';

        return Response::make($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Standard sitemap of all published posts.
     */
    public function sitemapPosts()
    {
        return Cache::remember('sitemap.posts', 1800, function () {
            $xml = '<?xml version="1.0" encoding="UTF-8"?>'
                . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

            // Homepage
            $xml .= '<url>'
                . '<loc>' . url('/') . '</loc>'
                . '<changefreq>hourly</changefreq>'
                . '<priority>1.0</priority>'
                . '</url>';

            Post::published()->where('noindex', false)->chunk(500, function ($posts) use (&$xml) {
                foreach ($posts as $post) {
                    $xml .= '<url>'
                        . '<loc>' . e($post->url) . '</loc>'
                        . '<lastmod>' . $post->updated_at->toAtomString() . '</lastmod>'
                        . '<changefreq>' . ($post->type === 'news' ? 'hourly' : 'weekly') . '</changefreq>'
                        . '<priority>' . ($post->is_featured ? '0.9' : '0.7') . '</priority>';

                    if ($img = $post->og_image_url) {
                        $xml .= '<image:image>'
                            . '<image:loc>' . e($img) . '</image:loc>'
                            . '<image:title>' . e($post->title) . '</image:title>'
                            . '</image:image>';
                    }
                    $xml .= '</url>';
                }
            });

            $xml .= '</urlset>';
            return Response::make($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
        });
    }

    /**
     * Sitemap for category landing pages.
     */
    public function sitemapCategories()
    {
        return Cache::remember('sitemap.categories', 3600, function () {
            $xml = '<?xml version="1.0" encoding="UTF-8"?>'
                . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

            foreach (Category::where('is_active', true)->get() as $cat) {
                $xml .= '<url>'
                    . '<loc>' . e(route('categories.show', $cat->slug)) . '</loc>'
                    . '<lastmod>' . $cat->updated_at->toAtomString() . '</lastmod>'
                    . '<changefreq>daily</changefreq>'
                    . '<priority>0.6</priority>'
                    . '</url>';
            }

            $xml .= '</urlset>';
            return Response::make($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
        });
    }

    /**
     * Sitemap for series landing pages.
     */
    public function sitemapSeries()
    {
        return Cache::remember('sitemap.series', 3600, function () {
            $xml = '<?xml version="1.0" encoding="UTF-8"?>'
                . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

            foreach (Series::all() as $series) {
                $xml .= '<url>'
                    . '<loc>' . e(route('series.show', $series->slug)) . '</loc>'
                    . '<lastmod>' . $series->updated_at->toAtomString() . '</lastmod>'
                    . '<changefreq>weekly</changefreq>'
                    . '<priority>0.5</priority>'
                    . '</url>';
            }

            $xml .= '</urlset>';
            return Response::make($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
        });
    }

    /**
     * Google News sitemap — only articles published in the last 48 hours.
     * Required for Google News surfacing. Spec:
     * https://developers.google.com/search/docs/crawling-indexing/sitemaps/news-sitemap
     */
    public function sitemapNews()
    {
        return Cache::remember('sitemap.news', 300, function () {
            $publisher = SiteSetting::get('site_name', 'KukiTales');
            $lang = SiteSetting::get('site_language', 'en');

            $xml = '<?xml version="1.0" encoding="UTF-8"?>'
                . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">';

            $news = Post::published()
                ->where('noindex', false)
                ->whereIn('type', ['news', 'story', 'history', 'folktale', 'blog'])
                ->where('published_at', '>=', now()->subHours(48))
                ->latest('published_at')
                ->limit(1000)
                ->get();

            foreach ($news as $post) {
                $xml .= '<url>'
                    . '<loc>' . e($post->url) . '</loc>'
                    . '<news:news>'
                    . '<news:publication>'
                    . '<news:name>' . e($publisher) . '</news:name>'
                    . '<news:language>' . e($lang) . '</news:language>'
                    . '</news:publication>'
                    . '<news:publication_date>' . $post->published_at->toAtomString() . '</news:publication_date>'
                    . '<news:title>' . e($post->title) . '</news:title>'
                    . '</news:news>'
                    . '</url>';
            }

            $xml .= '</urlset>';
            return Response::make($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
        });
    }

    /**
     * robots.txt — tells crawlers what to index.
     */
    public function robots()
    {
        $body = "User-agent: *\n"
            . "Allow: /\n"
            . "Disallow: /admin\n"
            . "Disallow: /admin/*\n"
            . "Disallow: /login\n"
            . "Disallow: /register\n"
            . "Disallow: /dashboard\n"
            . "Disallow: /submit\n"
            . "Disallow: /bookmarks\n"
            . "\n"
            . "Sitemap: " . url('/sitemap.xml') . "\n"
            . "Sitemap: " . url('/sitemap-news.xml') . "\n";

        return Response::make($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /**
     * RSS 2.0 feed of latest published posts.
     */
    public function rss()
    {
        return Cache::remember('rss.feed', 600, function () {
            $siteName = SiteSetting::get('site_name', 'KukiTales');
            $tagline = SiteSetting::get('site_tagline', 'Voices of the Hills');
            $description = SiteSetting::get('site_description', '');
            $now = now()->toRfc2822String();

            $xml = '<?xml version="1.0" encoding="UTF-8"?>'
                . '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:dc="http://purl.org/dc/elements/1.1/">'
                . '<channel>'
                . '<title>' . e($siteName . ' — ' . $tagline) . '</title>'
                . '<link>' . url('/') . '</link>'
                . '<atom:link href="' . url('/feed.xml') . '" rel="self" type="application/rss+xml"/>'
                . '<description>' . e($description) . '</description>'
                . '<language>' . e(SiteSetting::get('site_language', 'en')) . '</language>'
                . '<lastBuildDate>' . $now . '</lastBuildDate>'
                . '<ttl>30</ttl>';

            $posts = Post::published()->where('noindex', false)
                ->with('user', 'category')
                ->latest('published_at')->limit(30)->get();

            foreach ($posts as $post) {
                $xml .= '<item>'
                    . '<title>' . e($post->title) . '</title>'
                    . '<link>' . e($post->url) . '</link>'
                    . '<guid isPermaLink="true">' . e($post->url) . '</guid>'
                    . '<pubDate>' . $post->published_at->toRfc2822String() . '</pubDate>'
                    . '<dc:creator>' . e(optional($post->user)->name ?? 'KukiTales') . '</dc:creator>'
                    . ($post->category ? '<category>' . e($post->category->name) . '</category>' : '')
                    . '<description>' . e($post->short_excerpt) . '</description>'
                    . '<content:encoded><![CDATA[' . $post->content . ']]></content:encoded>'
                    . '</item>';
            }

            $xml .= '</channel></rss>';
            return Response::make($xml, 200, ['Content-Type' => 'application/rss+xml; charset=UTF-8']);
        });
    }
}
