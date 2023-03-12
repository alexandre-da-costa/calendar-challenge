<?php

use App\Models\Meeting;
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
        Schema::create('attendee_meeting', function (Blueprint $table) {
            $table->morphs('attendee');
            $table->foreignIdFor(Meeting::class)->constrained();
            $table->boolean('is_accepted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_attendees');
    }
};
