<?php

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
        Schema::create('copouns', function (Blueprint $table) {
            $table->id();
            $table->text('copoun')->unique();
            $table->time('start_date')->nullable();
            $table->time('ena_date')->nullable();
            $table->integer('limit')->nullable();
            $table->enum('type',['value','percentage']);
            $table->enum('status',['active','inactive']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('copouns');
    }
};
