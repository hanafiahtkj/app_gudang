<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Sale;
use App\Models\SaleDetails;
use App\Models\Warehouse;
use App\Models\Product;
use DataTables;
use Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Sales/Index');
    }

    public function loadDatatables()
    {
        $model = Sale::with('warehouse');

        return DataTables::of($model)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Sales/Create', [
            'warehouses' => Warehouse::all(),
            'products' => Product::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->except(['created_at', 'updated_at']);
        $rules = [
            'sale_date' => 'required',
            'warehouse_id' => 'required',
            'total_products' => 'required|numeric|not_in:0',
            'total' => 'required|numeric|not_in:0',
        ];
        if ($input['sale_details']) {
            foreach ($input['sale_details'] as $key => $value) {
                $rules['sale_details.'.$key.'.quantity'] = 'required|not_in:0';
                $rules['sale_details.'.$key.'.unitcost'] = 'required|not_in:0';
                $rules['sale_details.'.$key.'.total'] = 'required|not_in:0';
            }
        }
        $request->validate($rules);

        $sale = Sale::create([
            "sale_date" => Carbon::createFromFormat('d/m/Y', $request->sale_date)->format('Y-m-d'),
            "warehouse_id" => $request->warehouse_id,
            'total_products' => $request->total_products,
            'total' => $request->total
        ]);

        if ($request->sale_details) {
            foreach ($request->sale_details as $key => $value) {
                $saleDetails = [
                    'sale_id' => $sale->id,
                    'product_id' => $value['product_id'],
                    'quantity' => $value['quantity'],
                    'unitcost' => $value['unitcost'],
                    'total' => $value['total'],
                ];
                SaleDetails::create($saleDetails);
            }
        }

        return Redirect::route('sales.index');
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
        $data = Sale::with(['saleDetails', 'saleDetails.product'])->where('id', $id)->first();
        return Inertia::render('Sales/Edit', [
                'data' => $data,
                'warehouses' => Warehouse::all(),
                'products' => Product::all(),
            ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $input = $request->except(['created_at', 'updated_at']);
        $rules = [
            'sale_date' => 'required',
            'warehouse_id' => 'required',
            'total_products' => 'required|numeric|not_in:0',
            'total' => 'required|numeric|not_in:0',
        ];
        if ($input['sale_details']) {
            foreach ($input['sale_details'] as $key => $value) {
                $rules['sale_details.'.$key.'.quantity'] = 'required|not_in:0';
                $rules['sale_details.'.$key.'.unitcost'] = 'required|not_in:0';
                $rules['sale_details.'.$key.'.total'] = 'required|not_in:0';
            }
        }
        $request->validate($rules);

        $sale = Sale::findOrFail($id);
        $input = $request->except(['created_at', 'updated_at']);
        $sale->update([
            // "sale_date" => Carbon::createFromFormat('d/m/Y', $request->sale_date)->format('Y-m-d'),
            "warehouse_id" => $request->warehouse_id,
            'total_products' => $request->total_products,
            'total' => $request->total
        ]);

        $sale_details = $request->sale_details;
        $arr_id = array_filter(array_column($sale_details, 'id'));
        $query = SaleDetails::where('sale_id', $id);
        if (!empty($arr_id)) {
            $query->whereNotIn('id', $arr_id);
        }
        $query->delete();

        if ($request->sale_details) {
            foreach ($request->sale_details as $key => $value) {
                $saleDetails = [
                    'sale_id' => $sale->id,
                    'product_id' => $value['product_id'],
                    'quantity' => $value['quantity'],
                    'unitcost' => $value['unitcost'],
                    'total' => $value['total'],
                ];

                if (array_key_exists('id', $value)) {
                    SaleDetails::find($value['id'])
                        ->update($saleDetails);
                }
                else {
                    SaleDetails::create($saleDetails);
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Sale::findOrFail($id);
        $data->delete();

        $dataDetails = SaleDetails::where('sale_id', $id);
        $dataDetails->delete();
    }

    public function modalProducts(Request $request)
    {
        return Inertia::modal('Sales/Modal')
            ->baseRoute('sales.index');
    }
}