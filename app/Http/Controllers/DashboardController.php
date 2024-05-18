<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use DB;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return Inertia::render('Dashboard', [
            'today' => \Carbon\Carbon::now()->isoFormat('dddd, LL'),
            'tot_products'  => DB::table('products')->count(),
            'tot_warehouses'=> DB::table('warehouses')->count(),
            'tot_purchases' => DB::table('purchases')
                ->join('purchase_details', 'purchases.id', '=', 'purchase_details.purchase_id')
                ->whereDate('purchases.date', '=', now()->toDateString())
                ->count(),
            'tot_sales' => DB::table('sales')
                ->join('sale_details', 'sales.id', '=', 'sale_details.sale_id')
                ->whereDate('sales.sale_date', '=', now()->toDateString())
                ->count(),
            'tot_stock_reductions'=> DB::table('stock_reductions')
                ->join('stock_reduction_details', 'stock_reductions.id', '=', 'stock_reduction_details.stock_reduction_id')
                ->whereDate('stock_reductions.stock_reduction_date', '=', now()->toDateString())
                ->count(),
        ]);
    }
}
