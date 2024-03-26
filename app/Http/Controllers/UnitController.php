<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Unit;
use DataTables;
use Str;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Units/Index');
    }

    public function loadDatatables()
    {
        $model = Unit::query();

        return DataTables::of($model)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::modal('Units/Create')
            ->baseRoute('units.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'short_code' => 'required',
        ]);

        Unit::create([
            "name" => $request->name,
            "short_code" => $request->short_code,
            "slug" => Str::slug($request->name)
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
        $data = Unit::find($id);
        return Inertia::modal('Units/Edit', ['data' => $data])
            ->baseRoute('units.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'short_code' => 'required',
        ]);

        $data = Unit::findOrFail($id);
        $input = $request->except(['created_at', 'updated_at']);
        $data->update($input);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Unit::findOrFail($id);
        $data->delete();
    }
}
