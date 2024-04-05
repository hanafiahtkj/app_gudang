<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Role;
use DataTables;
use Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Users/Index');
    }

    public function loadDatatables(Request $request)
    {
        $model = User::with(['roles']);

        return DataTables::of($model)->toJson();
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::modal('Users/Create', [
            'roles' => Role::all(),
        ])
        ->baseRoute('users.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required',
            'posisi' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'posisi' => $request->posisi,
        ]);
        $user->addRole($request->role);
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
        $data = User::with('roles')->find($id);
        return Inertia::modal('Users/Edit', [
            'data'  => $data,
            'roles' => Role::all(),
        ])
        ->baseRoute('users.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => 'required',
            'posisi' => 'required',
        ]);

        $input = $request->except(['created_at', 'updated_at']);
        if ($input['password'] !== null) {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }
        $data = User::findOrFail($id);
        $data->update($input);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = User::findOrFail($id);
        $data->delete();
    }
}
