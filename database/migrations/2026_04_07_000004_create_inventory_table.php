<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('min_quantity')->default(0); // alert threshold
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->string('unit')->nullable(); // pcs, kg, litre
            $table->enum('status', ['available', 'low_stock', 'out_of_stock'])->default('available');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // who did it
            $table->enum('type', ['in', 'out', 'adjustment'])->default('in');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->date('transaction_date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('inventory_categories');
    }
};
