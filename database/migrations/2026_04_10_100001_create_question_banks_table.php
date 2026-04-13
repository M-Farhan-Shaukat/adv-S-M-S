<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Question Bank - stores AI-generated questions per subject/class
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('chapter')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->string('language', 20)->default('english'); // english, urdu, arabic
            $table->string('source_image')->nullable(); // uploaded book page image
            $table->text('extracted_text')->nullable(); // OCR/AI extracted text
            $table->timestamps();
            $table->softDeletes();
        });

        // Individual Questions
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_bank_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['mcq', 'short', 'long']);
            $table->text('question_text');
            $table->text('option_a')->nullable();
            $table->text('option_b')->nullable();
            $table->text('option_c')->nullable();
            $table->text('option_d')->nullable();
            $table->string('correct_answer')->nullable(); // a, b, c, d for MCQ
            $table->text('answer_hint')->nullable();
            $table->integer('marks')->default(1);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // Generated Papers
        Schema::create('exam_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('language', 20)->default('english');
            $table->integer('total_marks');
            $table->integer('duration_minutes')->default(60);
            $table->integer('mcq_count')->default(0);
            $table->integer('short_count')->default(0);
            $table->integer('long_count')->default(0);
            $table->integer('mcq_marks')->default(1);
            $table->integer('short_marks')->default(3);
            $table->integer('long_marks')->default(5);
            $table->string('exam_date')->nullable();
            $table->text('instructions')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Paper Questions (which questions are in which paper)
        Schema::create('exam_paper_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_paper_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->integer('marks');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_paper_questions');
        Schema::dropIfExists('exam_papers');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('question_banks');
    }
};
