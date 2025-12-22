<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id()->comment('主キー');
            $table->foreignId('post_id')->comment('記事ID')->constrained('posts')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->comment('コメント者ID（ゲスト可）')->constrained('users')->onDelete('set null');
            $table->string('author_name', 100)->nullable()->comment('ゲストコメント者名');
            $table->text('body')->comment('コメント本文');
            $table->boolean('is_approved')->default(false)->comment('承認フラグ（true=承認済み）');
            $table->timestamps();
            $table->comment('コメントテーブル');
            $table->index(['post_id', 'is_approved']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
