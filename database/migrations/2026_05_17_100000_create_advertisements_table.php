<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type');        // banner_image | video | custom_html
            $table->string('placement');   // homepage_hero, homepage_middle, sidebar, article_top, article_middle, article_bottom, footer
            $table->string('image_path')->nullable();
            $table->string('video_url')->nullable();      // YouTube/Vimeo embed URL or self-hosted mp4
            $table->text('custom_html')->nullable();      // raw HTML/iframe for ad networks (AdSense etc.)
            $table->string('link_url')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('label')->nullable();          // "Sponsored", "Promotion", etc.
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);      // higher = shown first
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedBigInteger('view_count')->default(0);
            $table->unsignedBigInteger('click_count')->default(0);
            $table->timestamps();

            $table->index(['placement', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
