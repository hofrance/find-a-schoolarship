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
        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->longText('requirements')->nullable();
            $table->longText('skills')->nullable();
            $table->text('salary_range')->nullable();
            $table->json('education_levels')->nullable(); // licence, master, doctorat
            $table->json('sectors')->nullable(); // informatique, medecine, ingenierie, etc.
            $table->text('career_prospects')->nullable();
            $table->string('featured_image')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('locale', 2)->default('fr');
            $table->integer('views_count')->default(0);
            $table->timestamps();

            $table->index(['is_featured', 'locale']);
            $table->index('locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('careers');
    }
};
