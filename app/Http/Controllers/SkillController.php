<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    //
    public function index()
    {
        try {
            $skills = Skill::select('id', 'name')->orderBy('name', 'asc')->get();

            return response()->json([
                'success' => true,
                'message' => 'Skills fetched successfully',
                'data' => $skills,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while fetching skills',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
