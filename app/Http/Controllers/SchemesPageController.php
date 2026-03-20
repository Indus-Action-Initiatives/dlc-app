<?php

namespace App\Http\Controllers;

use App\Models\SchemesPage;
use Illuminate\Http\Request;

class SchemesPageController extends Controller
{
    //

    // Create
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'url'         => 'nullable|url',
        ]);

        $data = SchemesPage::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Scheme created successfully',
            'data' => $data
        ]);
    }

    // List All
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => SchemesPage::all()
        ]);
    }

    // Single View
    public function show($id)
    {
        $data = SchemesPage::find($id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Scheme not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // Update
    public function update(Request $request, $id)
    {
        $data = SchemesPage::find($id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Scheme not found'
            ], 404);
        }

        $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'url'         => 'nullable|url',
        ]);

        $data->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Scheme updated successfully',
            'data' => $data
        ]);
    }

    // Delete
    public function destroy($id)
    {
        $data = SchemesPage::find($id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Scheme not found'
            ], 404);
        }

        $data->delete();

        return response()->json([
            'success' => true,
            'message' => 'Scheme deleted successfully'
        ]);
    }
}
