<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->string('region', 50);           // 地域
            $table->string('facility_code', 10);    // 施設コード
            $table->string('room_number', 10);      // 部屋番号
            $table->string('name');                 // 部屋名
            $table->integer('capacity');            // 定員
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['region', 'facility_code', 'room_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
