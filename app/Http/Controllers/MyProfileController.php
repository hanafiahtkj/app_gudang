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

class MyProfileController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function store(Request $request)
    {
        $id = auth()->user()->id;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
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
}
