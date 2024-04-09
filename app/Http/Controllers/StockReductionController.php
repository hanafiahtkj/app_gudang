<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\StockReduction;
use App\Models\StockReductionDetails;
use App\Models\Warehouse;
use App\Models\Product;
use DataTables;
use Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Barryvdh\DomPDF\Facade\Pdf;
use DB;

class StockReductionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('StockReductions/Index');
    }

    public function loadDatatables(Request $request)
    {
        $bulantahun = explode("/", $request->bulantahun);
        $model = StockReduction::with('warehouse')
            ->whereYear('stock_reduction_date', '=', $bulantahun[1])
            ->whereMonth('stock_reduction_date', '=', $bulantahun[0]);

        return DataTables::of($model)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('StockReductions/Create', [
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
            'stock_reduction_date' => 'required',
            'warehouse_id' => 'required',
            'total_products' => 'required|numeric|not_in:0',
            // 'total' => 'required|numeric|not_in:0',
        ];
        if ($input['sale_details']) {
            foreach ($input['sale_details'] as $key => $value) {
                $rules['sale_details.'.$key.'.quantity'] = 'required|numeric|min:1|max:'.$value['product']['stock'];
                $rules['sale_details.'.$key.'.description'] = 'required|not_in:0';
                // $rules['sale_details.'.$key.'.total'] = 'required|not_in:0';
            }
        }
        $request->validate($rules);

        $stockReduction = StockReduction::create([
            "stock_reduction_date" => Carbon::createFromFormat('d/m/Y', $request->stock_reduction_date)->format('Y-m-d'),
            "warehouse_id" => $request->warehouse_id,
            'total_products' => $request->total_products,
            // 'total' => $request->total
        ]);

        if ($request->sale_details) {
            foreach ($request->sale_details as $key => $value) {
                $saleDetails = [
                    'stock_reduction_id' => $stockReduction->id,
                    'product_id' => $value['product_id'],
                    'quantity' => $value['quantity'],
                    'description' => $value['description'],
                    // 'total' => $value['total'],
                ];
                StockReductionDetails::create($saleDetails);
            }
        }

        return Redirect::route('stock-reduction.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = StockReduction::with(['warehouse', 'stockReductionDetails', 'stockReductionDetails.product'])->where('id', $id)->first();
        return Inertia::modal('StockReductions/Show', [
            'data' => $data,
            'warehouses' => Warehouse::all(),
            'products' => Product::all(),
        ])
        ->baseRoute('stock-reduction.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = StockReduction::with(['saleDetails', 'saleDetails.product'])->where('id', $id)->first();
        return Inertia::render('StockReductions/Edit', [
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
            // 'total' => 'required|numeric|not_in:0',
        ];
        if ($input['sale_details']) {
            foreach ($input['sale_details'] as $key => $value) {
                $rules['sale_details.'.$key.'.quantity'] = 'required|not_in:0';
                $rules['sale_details.'.$key.'.description'] = 'required|not_in:0';
                // $rules['sale_details.'.$key.'.total'] = 'required|not_in:0';
            }
        }
        $request->validate($rules);

        $sale = StockReduction::findOrFail($id);
        $input = $request->except(['created_at', 'updated_at']);
        $sale->update([
            // "sale_date" => Carbon::createFromFormat('d/m/Y', $request->sale_date)->format('Y-m-d'),
            "warehouse_id" => $request->warehouse_id,
            'total_products' => $request->total_products,
            // 'total' => $request->total
        ]);

        $sale_details = $request->sale_details;
        $arr_id = array_filter(array_column($sale_details, 'id'));
        $query = StockReductionDetails::where('sale_id', $id);
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
                    'description' => $value['description'],
                    // 'total' => $value['total'],
                ];

                if (array_key_exists('id', $value)) {
                    StockReductionDetails::find($value['id'])
                        ->update($saleDetails);
                }
                else {
                    StockReductionDetails::create($saleDetails);
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = StockReduction::findOrFail($id);
        $data->delete();

        $dataDetails = StockReductionDetails::where('stock_reduction_id', $id);
        $dataDetails->delete();
    }

    public function modalProducts(Request $request)
    {
        return Inertia::modal('StockReductions/Modal')
            ->baseRoute('stock-reduction.index');
    }

    function laporanPdf(Request $request)
    {
        $tanggal = Carbon::createFromFormat('d/m/Y', $request->today)->format('Y-m-d');
        $query = DB::table('stock_reduction_details')
            ->join('stock_reductions', 'stock_reduction_details.stock_reduction_id', '=', 'stock_reductions.id')
            ->join('products', 'stock_reduction_details.product_id', '=', 'products.id')
            ->where('stock_reductions.stock_reduction_date', $tanggal)
            ->select(
                'stock_reduction_details.product_id',
                'stock_reductions.warehouse_id',
                'products.name',
                DB::raw('SUM(stock_reduction_details.quantity) as total_quantity')
            )
            ->groupBy(
                'stock_reduction_details.product_id',
                'stock_reductions.warehouse_id',
                'products.name',
            );

        $pdf = Pdf::loadView('pdf.stock_reductions', [
            'tanggal' => $tanggal,
            'data' => $query->get(),
        ]);

        return $pdf->stream('penyusutan_'.str_replace("/", "_", $tanggal).'.pdf');
    }
}
