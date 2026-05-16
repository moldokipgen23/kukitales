<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('type');
            $table->string('status')->default('draft');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('series_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('episode_number')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('audio_file')->nullable();
            $table->string('year_era')->nullable();
            $table->string('source')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_breaking')->default(false);
            $table->boolean('allow_tts')->default(true);
            $table->unsignedBigInteger('view_count')->default(0);
            $table->integer('read_time')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'status', 'published_at']);
            $table->index('is_featured');
            $table->index('is_breaking');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
