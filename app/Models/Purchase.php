<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchases';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetails::class, 'purchase_id');
    }
}
