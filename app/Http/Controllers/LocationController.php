<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;
use App\Models\District;

class LocationController extends Controller
{
    public function getStates()
    {
        try {
            $states = State::orderBy('id', 'asc')->get();
            return response()->json([
                'success' => true,
                'data' => $states
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDistricts(Request $request)
    {
        try {
            if (!$request->has('state_id') || empty($request->state_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'state_id is required'
                ], 400);
            }

            $districts = District::where('state_id', $request->state_id)
                ->orderBy('district_name', 'asc')
                ->get(['id', 'lgd_code','district_name', 'state_id']);

            if ($districts->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No districts found for this state'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Districts fetched successfully',
                'data' => $districts
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
