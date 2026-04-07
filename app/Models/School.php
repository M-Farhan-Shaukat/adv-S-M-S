<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use SoftDeletes;

    protected $table = 'schools';
    protected $fillable = [
        'name', 'slug', 'email', 'phone', 'address',
        'is_active', 'fee_voucher_day', 'sms_api_key', 'logo', 'description',
    ];

    public function sessions()      { return $this->hasMany(SchoolSession::class); }
    public function classes()       { return $this->hasMany(SchoolClass::class); }
    public function sections()      { return $this->hasMany(Section::class); }
    public function students()      { return $this->hasMany(Student::class); }
    public function teachers()      { return $this->hasMany(Teacher::class); }
    public function staff()         { return $this->hasMany(Staff::class); }
    public function subjects()      { return $this->hasMany(Subject::class); }
    public function feeTypes()      { return $this->hasMany(FeeType::class); }
    public function feeStructures() { return $this->hasMany(FeeStructure::class); }
    public function feeVouchers()   { return $this->hasMany(FeeVoucher::class); }
    public function payments()      { return $this->hasMany(Payment::class); }
    public function expenses()      { return $this->hasMany(Expense::class); }
    public function users()         { return $this->hasMany(User::class); }
    public function exams()         { return $this->hasMany(Exam::class); }
    public function complaints()    { return $this->hasMany(Complaint::class); }
    public function meetings()      { return $this->hasMany(MeetingSchedule::class); }
    public function inventory()     { return $this->hasMany(InventoryItem::class); }

    public function currentSession()
    {
        return $this->hasOne(SchoolSession::class)->whereIn('status', ['active', 'exam']);
    }

    public function activeSession()
    {
        return $this->hasOne(SchoolSession::class)->where('status', 'active');
    }
}
