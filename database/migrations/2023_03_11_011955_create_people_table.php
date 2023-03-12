<?php

use App\Models\ClientCompany;
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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('title')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('linkedin_profile_url')->nullable();
            $table->foreignIdFor(ClientCompany::class)->nullable()->constrained();
            $table->dateTime('last_enriched_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
