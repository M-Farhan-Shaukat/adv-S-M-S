<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $table = 'expenses';
    protected $fillable = [
        'school_id', 'title', 'amount', 'date',
        'description', 'expense_category_id',
    ];

    public function school()   { return $this->belongsTo(School::class); }
    public function category() { return $this->belongsTo(ExpenseCategory::class, 'expense_category_id'); }
}
