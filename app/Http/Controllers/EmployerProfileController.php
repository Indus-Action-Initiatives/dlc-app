<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Employer;
use Illuminate\Http\Request;
use App\Models\WorkerProfile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EmployerProfileController extends Controller
{

public function getEmployer($id)
{
    try {
        $profile = Employer::with('profile','skills')->find($id);

        if (!$profile || !$profile->profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found'
            ], 404);
        }

        $uploadPath = env('UPLOAD_PATH_EMPLOYER');

        /* ------------ PROFILE IMAGE ------------- */

        $imagePath = $uploadPath . '/profile/' . $profile->profile->profile_image;

        if (!empty($profile->profile->profile_image) && file_exists($imagePath)) {

            $mimeType = mime_content_type($imagePath);
            $base64 = base64_encode(file_get_contents($imagePath));

            $profile->profile->profile_image_base64 = "data:$mimeType;base64,$base64";
        } else {
            $profile->profile->profile_image_base64 = null;
        }


        /* ------------- PROFILE PDF -------------- */

        $docPath = $uploadPath . '/docs/' . $profile->profile->pdf;

        if (!empty($profile->profile->pdf) && file_exists($docPath)) {

            $mimeType = mime_content_type($docPath);
            $base64 = base64_encode(file_get_contents($docPath));

            $profile->profile->profile_doc_base64 = "data:$mimeType;base64,$base64";
        } else {
            $profile->profile->profile_doc_base64 = null;
        }

        return response()->json([
            'success' => true,
            'employer' => $profile
        ], 200);

    } catch (\Exception $e) {

        \Log::error('Error fetching employer profile: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function findNearbyWorkers(Request $request)
{
    $lat     = $request->lat;
    $long    = $request->long;
    $range   = $request->range;
    $skill   = $request->skill_id;
    $pincode = $request->pincode;
    $village = $request->location;

    $perPage = $request->per_page ?? 10;   // frontend decides
    $page    = $request->page ?? 1;        // load more page number

    $workers = WorkerProfile::query()
        ->join('workers', 'workers.id', '=', 'worker_profiles.worker_id')
        ->select(
            'worker_profiles.*',
            'workers.email',
            'workers.phone'
        )
        ->when($village, fn($q) =>
            $q->where('worker_profiles.street_area_village', 'LIKE', "%$village%")
        )
        ->when($pincode, fn($q) =>
            $q->where('worker_profiles.pin_code', 'LIKE', "%$pincode%")
        )
        ->when($skill, fn($q) =>
            $q->whereJsonContains('worker_profiles.skill_id', (int)$skill)
        )
        ->when($lat && $long, function ($q) use ($lat, $long, $range) {
            $q->selectRaw("
                (
                    6371 * acos(
                        cos(radians(?)) *
                        cos(radians(worker_profiles.lat::float)) *
                        cos(radians(worker_profiles.long::float) - radians(?)) +
                        sin(radians(?)) *
                        sin(radians(worker_profiles.lat::float))
                    )
                ) AS distance
            ", [$lat, $long, $lat]);

            if ($range) {
                $q->whereRaw("
                    (
                        6371 * acos(
                            cos(radians(?)) *
                            cos(radians(worker_profiles.lat::float)) *
                            cos(radians(worker_profiles.long::float) - radians(?)) +
                            sin(radians(?)) *
                            sin(radians(worker_profiles.lat::float))
                        )
                    ) <= ?
                ", [$lat, $long, $lat, $range]);
            }

            $q->orderBy('distance', 'asc');
        })
        ->paginate($perPage, ['*'], 'page', $page);

    return response()->json([
        'success' => true,
        'workers' => $workers->items(),
        'current_page' => $workers->currentPage(),
        'next_page'    => $workers->hasMorePages() ? $workers->currentPage() + 1 : null,
        'has_more' => $workers->hasMorePages(),
        'total'        => $workers->total()
    ]);
}



// public function findNearbyWorkers(Request $request)
// {
//     $lat     = $request->lat;
//     $long    = $request->long;
//     $range   = $request->range;
//     $skill   = $request->skill_id;
//     $pincode = $request->pincode;
//     $village = $request->location;

//     $workers = WorkerProfile::query()
//         ->join('workers', 'workers.id', '=', 'worker_profiles.worker_id')

//         ->select(
//             'worker_profiles.*',
//             'workers.email',
//             'workers.phone'
//         )

//         /** FILTER : village */
//         ->when($village, fn($q) =>
//             $q->where('worker_profiles.street_area_village', 'LIKE', "%$village%")
//         )

//         /** FILTER : pincode */
//         ->when($pincode, fn($q) =>
//             $q->where('worker_profiles.pin_code', 'LIKE', "%$pincode%")
//         )

//         /** FILTER : skill IDs (jsonb contains) */
//         ->when($skill, fn($q) =>
//             $q->whereJsonContains('worker_profiles.skill_id', (int)$skill)
//         )

//         /** DISTANCE FILTER */
//         ->when($lat && $long, function ($q) use ($lat, $long, $range) {

//             // SELECT distance
//             $q->selectRaw("
//                 (
//                     6371 * acos(
//                         cos(radians(?)) *
//                         cos(radians(worker_profiles.lat::float)) *
//                         cos(radians(worker_profiles.long::float) - radians(?)) +
//                         sin(radians(?)) *
//                         sin(radians(worker_profiles.lat::float))
//                     )
//                 ) AS distance
//             ", [$lat, $long, $lat]);

//             // Apply distance filter using WHERE
//             if ($range) {
//                 $q->whereRaw("
//                     (
//                         6371 * acos(
//                             cos(radians(?)) *
//                             cos(radians(worker_profiles.lat::float)) *
//                             cos(radians(worker_profiles.long::float) - radians(?)) +
//                             sin(radians(?)) *
//                             sin(radians(worker_profiles.lat::float))
//                         )
//                     ) <= ?
//                 ", [$lat, $long, $lat, $range]);
//             }

//             $q->orderBy('distance', 'asc');

//         })
//         ->get();

//     return response()->json([
//         'success' => true,
//         'workers' => $workers
//     ]);
// }


//         $request->validate([
//            // 'lat' => 'required',
//             //'lng' => 'required',
//         ]);

// $lat = $request->lat;
// $lng = $request->lng;
// $range = $request->range ?? 20;

// $workers = \DB::table('worker_profiles')
//     ->selectRaw("
//         worker_profiles.*,
//         (
//             6371 * acos(
//                 cos(radians(?))
//                 * cos(radians(lat::float))
//                 * cos(radians(?) - radians(\"long\"::float))
//                 + sin(radians(?))
//                 * sin(radians(lat::float))
//             )
//         ) AS distance
//     ", [$lat, $lng, $lat])
//     ->when($request->skill_id, function ($q) use ($request) {
//         $q->whereJsonContains('skill_id', (int)$request->skill_id);
//     })
//     ->whereRaw("
//         (
//             6371 * acos(
//                 cos(radians(?))
//                 * cos(radians(lat::float))
//                 * cos(radians(?) - radians(\"long\"::float))
//                 + sin(radians(?))
//                 * sin(radians(lat::float))
//             )
//         ) <= ?
//     ", [$lat, $lng, $lat, $range])
//     ->orderBy('distance', 'asc')
//     ->get();


        // $workers = WorkerProfile::select(
        //     'worker_profiles.*',
        //     DB::raw("(
        //         6371 * acos(
        //             cos(radians($lat)) *
        //             cos(radians(lat)) *
        //             cos(radians(`long`) - radians($lng)) +
        //             sin(radians($lat)) *
        //             sin(radians(lat))
        //         )
        //     ) AS distance")
        // )

        // $workers = WorkerProfile::select(
        //     'worker_profiles.*',
        //     \DB::raw("(
        //         6371 * acos(
        //             cos(radians($lat)) *
        //             cos(radians(lat)) *
        //             cos(radians(\"long\")) * cos(radians($lng)) +
        //             sin(radians($lat)) *
        //             sin(radians(lat))
        //         )
        //     ) AS distance")
        // )


        // ->when($request->pincode, function ($q) use ($request) {
        //     $q->where('pin_code', 'LIKE', '%' . $request->pincode . '%');
        // })

        // ->when($request->skill_id, function ($q) use ($request) {
        //     $q->whereJsonContains('skill_id', (int) $request->skill_id);
        // })

        // ->having('distance', '<=', $range)
        // ->orderBy('distance', 'asc')
        // ->get();

    //     return response()->json([
    //         'success' => true,
    //         'workers'  => $workers
    //     ]);
    // }




}
