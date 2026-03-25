<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        return response()->json(Staff::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staffs,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'nullable|string|max:100',
        ]);

        $staff = Staff::create($validated);

        return response()->json($staff, 201);
    }

    public function show(Staff $staff)
    {
        return response()->json($staff, 200);
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:staffs,email,' . $staff->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'nullable|string|max:100',
        ]);

        $staff->update($validated);

        return response()->json($staff, 200);
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();

        return response()->json(['message' => 'Staff removido com sucesso.'], 200);
    }
}