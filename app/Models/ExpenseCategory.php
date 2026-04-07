<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $table = 'expense_categories';
    protected $fillable = ['name', 'school_id'];

    public function school()   { return $this->belongsTo(School::class); }
    public function expenses() { return $this->hasMany(Expense::class); }
}
