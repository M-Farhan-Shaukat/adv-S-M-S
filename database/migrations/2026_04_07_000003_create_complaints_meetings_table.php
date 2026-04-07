<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // who filed
            $table->string('subject');
            $table->text('description');
            $table->enum('type', ['academic', 'behavioral', 'facility', 'staff', 'other'])->default('other');
            $table->enum('status', ['pending', 'in_progress', 'resolved', 'rejected'])->default('pending');
            $table->text('resolution')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('meeting_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scheduled_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('meeting_date');
            $table->integer('duration_minutes')->default(30);
            $table->string('venue')->nullable();
            $table->enum('type', ['parent_teacher', 'staff', 'general', 'emergency'])->default('general');
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('meeting_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['invited', 'confirmed', 'declined', 'attended'])->default('invited');
            $table->timestamps();
        });

        Schema::create('course_remarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // parent who submitted
            $table->text('remark');
            $table->enum('type', ['positive', 'negative', 'suggestion', 'query'])->default('query');
            $table->text('teacher_response')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_remarks');
        Schema::dropIfExists('meeting_participants');
        Schema::dropIfExists('meeting_schedules');
        Schema::dropIfExists('complaints');
    }
};
