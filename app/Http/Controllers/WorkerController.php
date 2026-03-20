<?php

namespace App\Http\Controllers;

use App\Models\{User, Worker, WorkerProfile};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class WorkerController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|unique:workers,email',
            'phone' => 'required|digits:10|unique:workers,phone',
            'password' => 'required|string|min:6|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Worker::create([
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $profile = WorkerProfile::create([
            'name' => $request->name,
            'worker_id' => $user->id,
            'skill_id' => array_values(array_filter($request->skill_id ?? [])),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User registered successfully',
            'worker' => $user,
            'profile' => $profile,
            'token' => $token
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $worker = Worker::where('phone', $request->phone)->first();

        if (!$worker || !Hash::check($request->password, $worker->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $token = JWTAuth::fromUser($worker);

        $profile = $worker->profile()->first();


        return response()->json([
            'message' => 'Worker login successful',
            'worker' => $worker,
            'token' => $token,
            'profile' => $profile
        ]);
    }

    public function updateWorker(Request $request, $id)
    {
        $worker = Worker::find($id);

        if (!$worker) {
            return response()->json([
                'status' => false,
                'message' => 'Worker not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|unique:workers,email,' . $id,
            'phone' => 'sometimes|digits:10|unique:workers,phone,' . $id,
            'password' => 'sometimes|string|min:6|max:10',
            'name' => 'sometimes|string|max:255',
            'age' => 'sometimes|numeric',
            'gender' => 'sometimes|string|max:20',
            'skill_id' => 'sometimes|array',
            'experience' => 'sometimes|string|max:100',
            'work_type' => 'sometimes|string|max:100',
            'location' => 'sometimes|string|max:255',
            'availability' => 'sometimes|string|max:50',
            'eshram' => 'nullable|string|max:50',
            'bocw' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:100',
            'plot_no' => 'nullable|string|max:255',
            'street_area_village' => 'nullable|string|max:255',
            'post_office' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'pin_code' => 'nullable|string|max:10',
            'profile_image' => 'nullable|string',
            'docType' => 'nullable|string|max:50',
            'docNumber' => 'nullable|string|max:50',
            'pdf' => 'nullable|string',
            'rate' => 'nullable|string|max:50',
            'lat' => 'nullable|string',
            'long' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // ✅ Update Worker basic info
        if ($request->has('password')) $worker->password = Hash::make($request->password);
        if ($request->has('email')) $worker->email = $request->email;
        if ($request->has('phone')) $worker->phone = $request->phone;
        $worker->save();

        $profile = WorkerProfile::where('worker_id', $id)->first();

        if ($profile) {
            $profile->update($request->only([
                'name', 'age', 'gender', 'experience', 'work_type',
                'location', 'availability', 'eshram', 'bocw', 'language',
                'plot_no', 'street_area_village', 'post_office', 'district',
                'state', 'pin_code', 'docType', 'docNumber', 'rate','lat',
        'long'
            ]));

            if ($request->has('skill_id')) {
                $profile->skill_id = array_values(array_filter($request->skill_id ?? []));
            }

            // ✅ Upload directories from .env
            //$uploadPa th = env('UPLOAD_PATH_WORKER');
            $uploadPath = config('filesystems.paths.worker_upload');
            $imagePath= public_path($uploadPath. '/profile');
            $docPath = public_path($uploadPath . '/docs');

            if (!is_dir($imagePath)) {
                    mkdir($imagePath, 0777, true);
                }

                if (!is_dir($docPath)) {
                    mkdir($docPath, 0777, true);
                }

            // ✅ Handle Profile Image (Base64)
            if ($request->has('profile_image')) {
                $base64Image = $request->profile_image;

                if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                    $data = base64_decode(substr($base64Image, strpos($base64Image, ',') + 1));
                    $type = strtolower($type[1]);
                    $filename = time() . '.' . $type;

                    if ($data !== false) {
                        if ($profile->profile_image && file_exists($imagePath . '/' . $profile->profile_image)) {
                            unlink($imagePath . '/' . $profile->profile_image);
                        }
                        file_put_contents($imagePath . '/' . $filename, $data);
                        $profile->profile_image = $filename;
                    }
                }
            }

            // ✅ Handle PDF (Base64)
            if ($request->has('pdf')) {
                $base64Pdf = $request->pdf;

                if (preg_match('/^data:application\/pdf;base64,/', $base64Pdf)) {
                    $data = base64_decode(substr($base64Pdf, strpos($base64Pdf, ',') + 1));
                    $filename = time() . '.pdf';

                    if ($data !== false) {
                        if ($profile->pdf && file_exists($docPath . '/' . $profile->pdf)) {
                            unlink($docPath . '/' . $profile->pdf);
                        }
                        file_put_contents($docPath . '/' . $filename, $data);
                        $profile->pdf = $filename;
                    }
                }
            }

            $profile->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Worker & profile updated successfully',
            'worker' => $worker,
            'profile' => $profile
        ]);
    }

    public function getWorker($id)
    {
        $worker = Worker::with(['profile', 'profile.stateDetail', 'profile.districtDetail'])->find($id);

        if (!$worker) {
            return response()->json([
                'status' => false,
                'message' => 'Worker not found'
            ], 404);
        }

        $profile = $worker->profile;
        $skills = $profile->skills ?? [];
        $worker->profile['skills'] = $skills;



        return response()->json([
            'status' => true,
            'message' => 'Worker profile fetched successfully',
            'worker' => $worker
        ]);
    }

    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'worker_id' => 'required|integer|exists:workers,id',
                'old_password' => 'required|string',
                'new_password' => 'required|string|min:6|max:12|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $worker = Worker::find($request->worker_id);

            if (!Hash::check($request->old_password, $worker->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Old password is incorrect',
                ], 400);
            }

            $worker->password = Hash::make($request->new_password);
            $worker->save();

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while changing password',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|digits:10|exists:workers,phone',
                'password' => 'required|string|min:6|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $worker = Worker::where('phone', $request->phone)->first();
            $worker->password = Hash::make($request->password);
            $worker->save();

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while updating password.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
