<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Brand;
use DataTables;
use Str;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Brands/Index');
    }

    public function loadDatatables()
    {
        $model = Brand::query();

        return DataTables::of($model)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::modal('Brands/Create')
            ->baseRoute('brands.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        Brand::create([
            "name" => $request->name,
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
        $data = Brand::find($id);
        return Inertia::modal('Brands/Edit', ['data' => $data])
            ->baseRoute('brands.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $data = Brand::findOrFail($id);
        $input = $request->except(['created_at', 'updated_at']);
        $data->update($input);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Brand::findOrFail($id);
        $data->delete();
    }
}
