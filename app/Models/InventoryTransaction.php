<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryTransaction extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id', 'inventory_item_id', 'user_id',
        'type', 'quantity', 'unit_price', 'notes', 'transaction_date',
    ];

    public function item() { return $this->belongsTo(InventoryItem::class, 'inventory_item_id'); }
    public function user() { return $this->belongsTo(User::class); }
}
