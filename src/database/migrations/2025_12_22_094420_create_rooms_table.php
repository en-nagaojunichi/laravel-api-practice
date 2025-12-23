<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('rooms')) {
            return;  // 既に存在する場合はスキップ
        }

        Schema::create('rooms', function (Blueprint $table) { // 部屋管理テーブル
            $table->string('region', 50)->comment('地域コード');
            $table->string('facility_code', 10)->comment('施設コード');
            $table->string('room_number', 10)->comment('部屋番号');
            $table->string('name', 100)->comment('部屋名');
            $table->integer('capacity')->comment('定員');
            $table->boolean('is_active')->default(true)->comment('有効フラグ');
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['region', 'facility_code', 'room_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
