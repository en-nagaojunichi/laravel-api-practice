<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id()->comment('主キー');
            $table->string('name', 100)->unique()->comment('タグ名');
            $table->string('slug', 100)->unique()->comment('URLスラッグ');
            $table->timestamps();
            $table->comment('タグテーブル');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
