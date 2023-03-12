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
        Schema::create('calendar_api_keys', function (Blueprint $table) {
            $table->foreignIdFor(config('usergems.calendar.owner.model_class_fqn'))->unique()->constrained();
            $table->string('key')->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_api_keys');
    }
};
