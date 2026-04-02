<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use BelongsToSchool;
    protected $table = 'expense_categories';
    protected $fillable = [
        'name',        'school_id',

    ];
}
