<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_tag', function (Blueprint $table) {
            $table->foreignId('post_id')->comment('記事ID')->constrained('posts')->onDelete('cascade');
            $table->foreignId('tag_id')->comment('タグID')->constrained('tags')->onDelete('cascade');
            $table->timestamps();
            $table->comment('記事タグ中間テーブル');
            $table->primary(['post_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_tag');
    }
};