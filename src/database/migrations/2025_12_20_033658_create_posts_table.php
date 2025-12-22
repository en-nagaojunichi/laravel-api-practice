<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id()->comment('主キー');
            $table->foreignId('user_id')->comment('投稿者ID')->constrained('users')->onDelete('cascade');
            $table->string('title', 255)->comment('記事タイトル');
            $table->string('slug', 255)->unique()->comment('URLスラッグ');
            $table->text('body')->nullable()->comment('本文');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->comment('公開状態（draft=下書き, published=公開, archived=アーカイブ）');
            $table->timestamp('published_at')->nullable()->comment('公開日時');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('投稿記事テーブル');
            $table->index(['user_id', 'status']);
            $table->index(['published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
