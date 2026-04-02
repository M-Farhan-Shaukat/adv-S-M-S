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
                $table->foreignId('class_id')->constrained('school_classes')->cascadeOnDelete();

                $table->string('name'); // Tuition Fee
                $table->decimal('amount', 10, 2);

                $table->timestamps();
                $table->softDeletes();
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
