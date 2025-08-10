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
        Schema::create('detections', function (Blueprint $table) {
            $table->id();
            // Core fields from web/detections.csv
            $table->string('source_name', 255)->nullable();
            $table->string('title', 1024);
            $table->string('country', 128)->nullable();
            $table->string('level', 128)->nullable();
            $table->string('language', 64)->nullable();
            $table->integer('score')->default(0)->index();
            $table->date('deadline')->nullable()->index();
            $table->string('amount', 128)->nullable();
            $table->string('item_url', 2048)->unique();
            $table->timestampTz('first_seen')->nullable();
            $table->timestampTz('last_seen')->nullable()->index();
            // Optional/extra fields (kept as strings for flexibility)
            $table->string('source_id', 128)->nullable();
            $table->string('provider', 255)->nullable();
            $table->string('category', 128)->nullable();
            $table->string('funding_type', 128)->nullable();
            $table->string('region', 128)->nullable();
            $table->string('fields', 255)->nullable();
            $table->string('tags', 255)->nullable();
            $table->string('source_url', 2048)->nullable();
            $table->text('summary')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detections');
    }
};
