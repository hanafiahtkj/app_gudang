<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Purchase;
use App\Models\PurchaseDetails;
use App\Models\Warehouse;
use App\Models\Product;
use DataTables;
use Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Purchases/Index');
    }

    public function loadDatatables()
    {
        $model = Purchase::with('warehouse');

        return DataTables::of($model)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Purchases/Create', [
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
            'date' => 'required',
            'warehouse_id' => 'required',
            'total_amount' => 'required|numeric|not_in:0',
        ];
        if ($input['purchase_details']) {
            foreach ($input['purchase_details'] as $key => $value) {
                $rules['purchase_details.'.$key.'.quantity'] = 'required|not_in:0';
                $rules['purchase_details.'.$key.'.unitcost'] = 'required|not_in:0';
                $rules['purchase_details.'.$key.'.total'] = 'required|not_in:0';
            }
        }
        $request->validate($rules);

        $purchase = Purchase::create([
            "date" => Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d'),
            "warehouse_id" => $request->warehouse_id,
            'total_amount' => $request->total_amount
        ]);

        if ($request->purchase_details) {
            foreach ($request->purchase_details as $key => $value) {
                $purchaseDetails = [
                    'purchase_id' => $purchase->id,
                    'product_id' => $value['product_id'],
                    'quantity' => $value['quantity'],
                    'unitcost' => $value['unitcost'],
                    'total' => $value['total'],
                ];
                PurchaseDetails::create($purchaseDetails);
            }
        }

        return Redirect::route('purchases.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Purchase::with(['warehouse', 'purchaseDetails', 'purchaseDetails.product'])->where('id', $id)->first();
        return Inertia::modal('Purchases/Show', [
            'data' => $data,
            'warehouses' => Warehouse::all(),
            'products' => Product::all(),
        ])
        ->baseRoute('purchases.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Purchase::with(['purchaseDetails', 'purchaseDetails.product'])->where('id', $id)->first();
        return Inertia::render('Purchases/Edit', [
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
            'date' => 'required',
            'warehouse_id' => 'required',
            'total_amount' => 'required|numeric|not_in:0',
        ];
        if ($input['purchase_details']) {
            foreach ($input['purchase_details'] as $key => $value) {
                $rules['purchase_details.'.$key.'.quantity'] = 'required|not_in:0';
                $rules['purchase_details.'.$key.'.unitcost'] = 'required|not_in:0';
                $rules['purchase_details.'.$key.'.total'] = 'required|not_in:0';
            }
        }
        $request->validate($rules);

        $purchase = Purchase::findOrFail($id);
        $input = $request->except(['created_at', 'updated_at']);
        $purchase->update([
            "date" => Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d'),
            "warehouse_id" => $request->warehouse_id,
            'total_amount' => $request->total_amount
        ]);

        $purchase_details = $request->purchase_details;
        $arr_id = array_filter(array_column($purchase_details, 'id'));
        $query = PurchaseDetails::where('purchase_id', $id);
        if (!empty($arr_id)) {
            $query->whereNotIn('id', $arr_id);
        }
        $query->delete();

        if ($request->purchase_details) {
            foreach ($request->purchase_details as $key => $value) {
                $purchaseDetails = [
                    'purchase_id' => $purchase->id,
                    'product_id' => $value['product_id'],
                    'quantity' => $value['quantity'],
                    'unitcost' => $value['unitcost'],
                    'total' => $value['total'],
                ];

                if (array_key_exists('id', $value)) {
                    PurchaseDetails::find($value['id'])
                        ->update($purchaseDetails);
                }
                else {
                    PurchaseDetails::create($purchaseDetails);
                }
            }
        }

        return Redirect::route('purchases.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Purchase::findOrFail($id);
        $data->delete();

        $dataDetails = PurchaseDetails::where('purchase_id', $id);
        $dataDetails->delete();
    }
}
