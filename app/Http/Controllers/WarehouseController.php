<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Warehouse;
use DataTables;
use Str;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Warehouses/Index');
    }

    public function loadDatatables()
    {
        $model = Warehouse::query();

        return DataTables::of($model)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::modal('Warehouses/Create')
            ->baseRoute('warehouses.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
        ]);

        Warehouse::create([
            "name" => $request->name,
            "address" => $request->address,
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
        $data = Warehouse::find($id);
        return Inertia::modal('Warehouses/Edit', ['data' => $data])
            ->baseRoute('warehouses.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
        ]);

        $data = Warehouse::findOrFail($id);
        $input = $request->except(['created_at', 'updated_at']);
        $data->update($input);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Warehouse::findOrFail($id);
        $data->delete();
    }
}
