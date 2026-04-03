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
            Schema::create('fee_structures', function (Blueprint $table) {
                $table->id();

                $table->foreignId('school_id')->constrained()->cascadeOnDelete();
                $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
                $table->string('name'); // Tuition Fee
                $table->foreignId('fee_type_id')->constrained()->cascadeOnDelete();
                $table->decimal('amount', 10, 2);

                $table->timestamps();
                $table->softDeletes();
                $table->unique(['school_class_id', 'fee_type_id', 'school_id']);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
};
