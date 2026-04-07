<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->string('name'); // Mid Term, Final Term, Quiz 1
            $table->enum('type', ['quiz', 'mid_term', 'final_term', 'annual', 'other'])->default('other');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('total_marks')->default(100);
            $table->integer('passing_marks')->default(40);
            $table->string('room')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('student_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->decimal('obtained_marks', 6, 2)->default(0);
            $table->boolean('is_absent')->default(false);
            $table->text('remarks')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['exam_schedule_id', 'student_id']);
        });

        Schema::create('recheck_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_marks_id')->constrained('student_marks')->cascadeOnDelete();
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('admin_remarks')->nullable();
            $table->decimal('revised_marks', 6, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recheck_requests');
        Schema::dropIfExists('student_marks');
        Schema::dropIfExists('exam_schedules');
        Schema::dropIfExists('exams');
    }
};
