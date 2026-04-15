<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Custom roles scoped per school
        Schema::create('school_custom_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');           // e.g. "Vice Principal"
            $table->string('slug');           // e.g. "vice-principal"
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['school_id', 'slug']);
        });

        // Permissions assigned to each custom role
        Schema::create('school_custom_role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_custom_role_id')
                  ->constrained('school_custom_roles')
                  ->cascadeOnDelete();
            $table->string('permission');     // matches Spatie permission name
            $table->timestamps();

            $table->unique(['school_custom_role_id', 'permission'], 'scrp_unique');
        });

        // Users assigned to custom roles (school-scoped)
        Schema::create('school_custom_role_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_custom_role_id')
                  ->constrained('school_custom_roles')
                  ->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['school_custom_role_id', 'user_id'], 'scru_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_custom_role_users');
        Schema::dropIfExists('school_custom_role_permissions');
        Schema::dropIfExists('school_custom_roles');
    }
};
