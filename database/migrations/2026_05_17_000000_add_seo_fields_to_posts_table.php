<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('focus_keyword')->nullable()->after('meta_description');
            $table->string('og_image')->nullable()->after('focus_keyword');
            $table->string('canonical_url')->nullable()->after('og_image');
            $table->boolean('noindex')->default(false)->after('canonical_url');
            $table->timestamp('updated_at_seo')->nullable()->after('noindex'); // for sitemap lastmod tracking on content-only edits
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['focus_keyword', 'og_image', 'canonical_url', 'noindex', 'updated_at_seo']);
        });
    }
};
