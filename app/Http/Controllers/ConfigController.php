<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConfigController extends Controller
{
    //
    public function getConfig()
    {
        $data = \App\Models\Config::all();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }


    public function saveConfig(Request $request)
    {
        $request->validate([
            'key' => 'required',
            'value' => 'required',
        ]);

        $config = \App\Models\Config::updateOrCreate(
            ['key' => $request->key],
            ['value' => $request->value]
        );

        return response()->json([
            'success' => true,
            'message' => 'Config saved successfully',
            'data' => $config
        ]);
    }

}
