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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('deal_link');
            $table->string('image_path')->nullable();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->date('expiry_date');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->integer('vote_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->string('submitter_email')->nullable(); // For anonymous submissions
            $table->string('verification_token')->nullable(); // For email verification
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
