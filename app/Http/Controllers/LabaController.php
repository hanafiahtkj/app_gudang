<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Nilai;
use App\Models\Laba;
use DataTables;
use Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LabaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nilai = Nilai::first();
        return Inertia::render('Laba/Index', [
            'nilai' => $nilai
        ]);
    }

    public function loadDatatables(Request $request)
    {
        $bulantahun = explode("/", $request->bulantahun);
        $model = Laba::whereYear('tanggal', '=', $bulantahun[1])
            ->whereMonth('tanggal', '=', $bulantahun[0]);

        return DataTables::of($model)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::modal('Laba/Create')
            ->baseRoute('nilai.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "tanggal"           => 'required',
            "penjualan"         => 'required|not_in:0',
            "pengeluaran"       => 'required',
            "laba"              => 'required|not_in:0',
            "persen_pemilik"    => 'required',
            "persen_karyawan"   => 'required',
            "laba_pemilik"      => 'required|not_in:0',
            "laba_karyawan"     => 'required|not_in:0',
        ]);

        $input = $request->except(['created_at', 'updated_at']);
        $input['tanggal'] = Carbon::createFromFormat('d/m/Y', $request->tanggal)->format('Y-m-d');
        $laba = Laba::where('tanggal', $input['tanggal'])->first();
        if ($laba){
            $laba->update($input);
        }
        else {
            Laba::create($input);
        }
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
        $data = Laba::find($id);
        return Inertia::modal('Laba/Edit', ['data' => $data])
            ->baseRoute('nilai.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $data = Laba::findOrFail($id);
        $input = $request->except(['created_at', 'updated_at']);
        $data->update($input);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Laba::findOrFail($id);
        $data->delete();
    }

    public function loadProsesData(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $input['tanggal'] = Carbon::createFromFormat('d/m/Y', $request->tanggal)->format('Y-m-d');
        $laba = Laba::where('tanggal', $input['tanggal'])->first();
        if (!$laba) {
            $sales = DB::table('sales')->select( DB::raw('SUM(total) as total_sales'))->where('sale_date', $input['tanggal'])->first();

            $proses = [
                'penjualan' => 0,
                'pengeluaran' => @$sales->total_sales ?? 0,
                'laba' => 0,
                'laba_pemilik' => 0,
                'laba_karyawan' => 0,
            ];
        }
        else {
            $proses = $laba;
        }

        return response()->json([
            'proses' => $proses,
        ]);
    }
}
