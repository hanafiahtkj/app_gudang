<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockReduction extends Model
{
    use HasFactory;

    protected $table = 'stock_reductions';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function stockReductionDetails()
    {
        return $this->hasMany(StockReductionDetails::class, 'stock_reduction_id');
    }
}
