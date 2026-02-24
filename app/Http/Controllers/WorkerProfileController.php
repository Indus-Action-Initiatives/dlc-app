<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Models\WorkerEducation;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\{User,Worker,WorkerProfile};

class WorkerProfileController extends Controller
{
    // public function getWorkerById($id)
    // {
    //     try {
    //         $worker = Worker::with('profile')->find($id);

    //         if (!$worker) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Worker not found'
    //             ], 404);
    //         }

    //         if ($worker) {
    //             if (!empty($worker->profile->profile_image)) {

    //                 // If stored in public folder OR your custom folder
    //                 $worker->profile->profile_image_url = url(
    //                     env('UPLOAD_PATH_WORKER').'/profile/'. $worker->profile->profile_image
    //                 );

    //             } else {

    //                 // Default image
    //                 $worker->profile->profile_image_url = url('default/no-photos.png');
    //             }
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'data' => $worker
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Something went wrong',
    //             'error' => $e->getMessage() // optional: production me hata sakte ho
    //         ], 500);
    //     }
    // }

    public function addEducation(Request $request)
    {
        $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'education_level' => 'required|string',
            'institution' => 'nullable|string',
            'passing_year' => 'nullable|string',
            'certificate' => 'nullable|string',
        ]);


        $uploadPath = env('UPLOAD_PATH_WORKER');
        $educationPath = $uploadPath . '/education';

        if (!file_exists($uploadPath)) mkdir($uploadPath, 0777, true);
        if (!file_exists($educationPath)) mkdir($educationPath, 0777, true);

        $certificateFilename = null;

        if ($request->has('certificate')) {

            $base64 = $request->certificate;

            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {

                $data = base64_decode(substr($base64, strpos($base64, ',') + 1));
                $type = strtolower($type[1]); // jpg / png

                $certificateFilename = time() . "." . $type;

                if ($data !== false) {
                    file_put_contents($educationPath . '/' . $certificateFilename, $data);
                }
            }

            elseif (preg_match('/^data:application\/pdf;base64,/', $base64)) {

                $data = base64_decode(substr($base64, strpos($base64, ',') + 1));
                $certificateFilename = time() . ".pdf";

                if ($data !== false) {
                    file_put_contents($educationPath . '/' . $certificateFilename, $data);
                }
            }
        }

        $education = WorkerEducation::create([
            'worker_id' => $request->worker_id,
            'education_level' => $request->education_level,
            'institution' => $request->institution,
            'passing_year' => $request->passing_year,
            'certificate_path' => $certificateFilename,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Education added successfully.',
            'data' => $education
        ]);
    }

    // public function getEducation($worker_id)
    // {
    //     $education = WorkerEducation::where('worker_id', $worker_id)->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $education
    //     ]);
    // }

public function getEducation($worker_id)
{
    $uploadPath = env('UPLOAD_PATH_WORKER');
    $educationPath = $uploadPath . '/education';

    $education = WorkerEducation::where('worker_id', $worker_id)->get();

    // Convert file to Base64
    foreach ($education as $edu) {
        if ($edu->certificate_path) {


        $fullPath = $educationPath . '/' . $edu->certificate_path;

        $fullPath = str_replace('C: /', 'C:/', $fullPath);
        $fullPath = str_replace('C:  /', 'C:/', $fullPath); // extra safety
        $fullPath = str_replace('C:   /', 'C:/', $fullPath);
        $fullPath = preg_replace('/C:\s+\/?/', 'C:/', $fullPath);
        $fullPath = str_replace('\\', '/', $fullPath);

      //  echo $fullPath;


      //  echo $fullPath;

            if (file_exists($fullPath)) {

                // Detect file type
                $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

                $fileData = file_get_contents($fullPath);
                $base64 = base64_encode($fileData);

                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    // Image Base64
                    $edu->certificate = "data:image/$extension;base64," . $base64;
                } elseif ($extension === 'pdf') {
                    // PDF Base64
                    $edu->certificate = "data:application/pdf;base64," . $base64;
                } else {
                    $edu->certificate = null;
                }

            } else {
                $edu->certificate = null;
            }
        } else {
            $edu->certificate = null;
        }
    }

    return response()->json([
        'success' => true,
        'data' => $education
    ]);
}


public function deleteEducation($worker_id, $id)
{
    $uploadPath = rtrim(env('UPLOAD_PATH_WORKER', public_path('storage/worker')), '/');
    $educationPath = $uploadPath . '/education';

    // Find record
    $education = WorkerEducation::where('worker_id', $worker_id)
                                ->where('id', $id)
                                ->first();

    if (!$education) {
        return response()->json([
            'success' => false,
            'message' => 'Education record not found.'
        ], 404);
    }

    // Delete certificate file if exists
    if ($education->certificate_path) {
        $fullPath = $educationPath . '/' . $education->certificate_path;

        // sanitize path
        $fullPath = str_replace('C: /', 'C:/', $fullPath);
        $fullPath = str_replace('C:  /', 'C:/', $fullPath);
        $fullPath = preg_replace('/C:\s+\/?/', 'C:/', $fullPath);
        $fullPath = str_replace('\\', '/', $fullPath);

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    // Delete DB record
    $education->delete();

    return response()->json([
        'success' => true,
        'message' => 'Education record deleted successfully.'
    ]);
}



}
