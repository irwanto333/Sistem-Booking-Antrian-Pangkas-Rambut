<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tukang_cukur_id')->constrained('tukang_cukurs')->onDelete('cascade');
            $table->tinyInteger('day_of_week')->comment('0=Minggu, 1=Senin, ..., 6=Sabtu');
            $table->time('open_time');
            $table->time('close_time');
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->unique(['tukang_cukur_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
