<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id', 'inventory_category_id', 'name', 'code',
        'description', 'quantity', 'min_quantity', 'unit_price', 'unit', 'status',
    ];

    public function category() { return $this->belongsTo(InventoryCategory::class, 'inventory_category_id'); }
    public function transactions() { return $this->hasMany(InventoryTransaction::class); }

    public function updateStatus(): void
    {
        if ($this->quantity <= 0) {
            $this->status = 'out_of_stock';
        } elseif ($this->quantity <= $this->min_quantity) {
            $this->status = 'low_stock';
        } else {
            $this->status = 'available';
        }
        $this->save();
    }
}
