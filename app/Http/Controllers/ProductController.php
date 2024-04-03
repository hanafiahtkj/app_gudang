<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;
use DataTables;
use Str;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Products/Index');
    }

    public function loadDatatables(Request $request)
    {
        $latestUnitCostSubQuery = DB::table('purchase_details as pd')
            ->select('pd.product_id', 'pd.unitcost')
            ->whereRaw('pd.id IN (SELECT MAX(id) FROM purchase_details WHERE product_id = pd.product_id)')
            ->groupBy('pd.product_id', 'pd.unitcost');

        $purchaseQuery = DB::table('purchase_details')
            ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
            ->select('purchase_details.product_id', 'purchases.warehouse_id', DB::raw('SUM(purchase_details.quantity) as total_purchase'))
            ->groupBy('purchase_details.product_id', 'purchases.warehouse_id');

        $saleQuery = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->select('sale_details.product_id', 'sales.warehouse_id', DB::raw('SUM(sale_details.quantity) as total_sale'))
            ->groupBy('sale_details.product_id', 'sales.warehouse_id');

        $reductionQuery = DB::table('stock_reduction_details')
            ->join('stock_reductions', 'stock_reduction_details.stock_reduction_id', '=', 'stock_reductions.id')
            ->select('stock_reduction_details.product_id', 'stock_reductions.warehouse_id', DB::raw('SUM(stock_reduction_details.quantity) as total_stock_reductions'))
            ->groupBy('stock_reduction_details.product_id', 'stock_reductions.warehouse_id');

        $model = Product::leftJoinSub($purchaseQuery, 'purchases', function ($join) {
                $join->on('products.id', '=', 'purchases.product_id');
            })
            ->leftJoinSub($saleQuery, 'sales', function ($join) {
                $join->on('products.id', '=', 'sales.product_id');
            })
            ->leftJoinSub($reductionQuery, 'stockreductions', function ($join) {
                $join->on('products.id', '=', 'stockreductions.product_id');
            })
            ->leftJoinSub($latestUnitCostSubQuery, 'unitcosts', function ($join) {
                $join->on('products.id', '=', 'unitcosts.product_id');
            })
            ->select('products.*',
                    DB::raw('COALESCE(purchases.total_purchase, 0) - COALESCE(sales.total_sale, 0) - COALESCE(stockreductions.total_stock_reductions, 0) as stock'),
                    'unitcosts.unitcost as unitcost');

        if ($request->has('warehouse_id')) {
            $model->where(function ($query) use ($request) {
                $query->where('purchases.warehouse_id', $request->warehouse_id)
                    ->orWhere('sales.warehouse_id', $request->warehouse_id);
            });
        }

        if ($request->has('product_ids')) {
            $model->whereNotIn('id', $request->product_ids);
        }

        return DataTables::of($model)->toJson();
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::modal('Products/Create')
            ->baseRoute('products.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'unit' => 'required',
            // 'description' => 'required',
        ]);

        Product::create([
            "name" => $request->name,
            "unit" => $request->unit,
            "description" => $request->description
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Product::find($id);
        return Inertia::modal('Products/Edit', ['data' => $data])
            ->baseRoute('products.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'unit' => 'required',
            // 'description' => 'required',
        ]);

        $data = Product::findOrFail($id);
        $input = $request->except(['created_at', 'updated_at']);
        $data->update($input);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Product::findOrFail($id);
        $data->delete();
    }
}
