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
            Schema::create('teacher_payrolls', function (Blueprint $table) {
                $table->id();
                $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
                $table->foreignId('school_id')->constrained()->cascadeOnDelete();
                $table->foreignId('school_session_id')->constrained()->cascadeOnDelete();
                $table->integer('month');
                $table->integer('year');
                $table->integer('total_minutes')->default(0);
                $table->decimal('gross_salary', 10, 2)->default(0);
                $table->decimal('deduction', 10, 2)->default(0);
                $table->decimal('net_salary', 10, 2)->default(0);
                $table->integer('required_minutes')->default(0);
                $table->integer('short_minutes')->default(0);
                $table->timestamps();
                $table->softDeletes();
                $table->unique(['teacher_id', 'month', 'year']);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_payrolls');
    }
};
