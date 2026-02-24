<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index()
    {
        $facilities = Facility::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $facilities
        ]);
    }

    public function store(Request $request)
    {

       // dd($request);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:facilities,name',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $facility = Facility::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Facility created',
            'data' => $facility
        ], 201);
    }

    public function show($id)
    {
        $facility = Facility::find($id);
        if (!$facility) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $facility]);
    }

    public function update(Request $request, $id)
    {
        $facility = Facility::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:facilities,name,'.$facility->id,
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $facility->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Facility updated',
            'data' => $facility
        ]);
    }

    public function destroy($id)
    {
        $facility = Facility::find($id);
        if (!$facility) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $facility->delete();

        return response()->json(['success' => true, 'message' => 'Facility deleted']);
    }
}
