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
            'tot_products'  => DB::table('products')->count(),
            'tot_purchases' => DB::table('purchases')->count(),
            'tot_sales'     => DB::table('sales')->count(),
            'tot_warehouses'=> DB::table('warehouses')->count(),
        ]);
    }
}
