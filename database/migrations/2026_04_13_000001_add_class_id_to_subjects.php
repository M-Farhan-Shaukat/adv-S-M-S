<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->foreignId('school_class_id')
                ->nullable()
                ->after('school_id')
                ->constrained('school_classes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\SchoolClass::class, 'school_class_id');
            $table->dropColumn('school_class_id');
        });
    }
};
