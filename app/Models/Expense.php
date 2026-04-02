<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use  BelongsToSchool;
    protected $table = 'expenses';
    protected $fillable = [
        'school_id',
        'title',
        'amount',
        'date',
        'description',
        'category_id',
    ];
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }
}
