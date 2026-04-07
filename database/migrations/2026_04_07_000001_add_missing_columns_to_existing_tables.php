<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add school_id to users table for multi-tenancy
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete()->after('id');
        });

        // Add is_class_monitor to student_enrollments
        Schema::table('student_enrollments', function (Blueprint $table) {
            $table->boolean('is_class_monitor')->default(false)->after('is_current');
        });

        // Fix fee_voucher_items: rename amount to total_amount consistency
        Schema::table('fee_voucher_items', function (Blueprint $table) {
            $table->renameColumn('amount', 'total_amount');
        });

        // Add parent_id to students
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->after('school_id');
            $table->string('roll_number')->nullable()->after('name');
            $table->string('address')->nullable()->after('gender');
            $table->string('guardian_name')->nullable()->after('address');
            $table->string('guardian_phone')->nullable()->after('guardian_name');
            $table->string('photo')->nullable()->after('guardian_phone');
        });

        // Add user_id to teachers
        Schema::table('teachers', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->after('school_id');
            $table->string('address')->nullable()->after('phone');
            $table->string('qualification')->nullable()->after('address');
            $table->string('photo')->nullable()->after('qualification');
            $table->date('joining_date')->nullable()->after('photo');
        });

        // Add fee_voucher_send_day to schools
        Schema::table('schools', function (Blueprint $table) {
            $table->integer('fee_voucher_day')->default(1)->after('is_active'); // day of month to send vouchers
            $table->string('sms_api_key')->nullable()->after('fee_voucher_day');
            $table->string('logo')->nullable()->after('sms_api_key');
            $table->text('description')->nullable()->after('logo');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\School::class);
            $table->dropColumn('school_id');
        });
        Schema::table('student_enrollments', function (Blueprint $table) {
            $table->dropColumn('is_class_monitor');
        });
        Schema::table('fee_voucher_items', function (Blueprint $table) {
            $table->renameColumn('total_amount', 'amount');
        });
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class);
            $table->dropColumn(['user_id', 'roll_number', 'address', 'guardian_name', 'guardian_phone', 'photo']);
        });
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class);
            $table->dropColumn(['user_id', 'address', 'qualification', 'photo', 'joining_date']);
        });
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['fee_voucher_day', 'sms_api_key', 'logo', 'description']);
        });
    }
};
