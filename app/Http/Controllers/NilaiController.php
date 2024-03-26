<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Nilai;
use DataTables;
use Str;

class NilaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nilai = Nilai::first();
        return Inertia::render('Nilai/Index', [
            'nilai' => $nilai
        ]);
    }

    public function loadDatatables()
    {
        $model = Nilai::query();

        return DataTables::of($model)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::modal('Nilai/Create')
            ->baseRoute('nilai.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'persen_pemilik' => 'required',
            'persen_karyawan' => 'required',
        ]);

        $nilai = Nilai::first();
        if ($nilai){
            $nilai->update([
                "persen_pemilik"  => $request->persen_pemilik,
                "persen_karyawan" => $request->persen_karyawan
            ]);
        }
        else {
            Nilai::create([
                "persen_pemilik"  => $request->persen_pemilik,
                "persen_karyawan" => $request->persen_karyawan
            ]);
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
        $data = Nilai::find($id);
        return Inertia::modal('Nilai/Edit', ['data' => $data])
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

        $data = Nilai::findOrFail($id);
        $input = $request->except(['created_at', 'updated_at']);
        $data->update($input);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Nilai::findOrFail($id);
        $data->delete();
    }
}
