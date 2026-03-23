<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\{User,Employer,EmployerProfile};

class EmployerController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|unique:employers,email',
            'phone' => 'required|digits:10|unique:employers,phone',
            'password' => 'required|string|min:6|max:10',
           // 'skills' => 'required|array',
           // 'skills.*' => 'exists:skills,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Employer::create([
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $profile = EmployerProfile::create([
            'name'=> $request->name,
            'employer_id' => $user->id,
            'avg_worker' => $request->avg_worker,
            'work_type' => $request->work_type,
            'location' => $request->location,
            'availability' => $request->availability,
            'eshram' => $request->eshram,
            'aadhar' => $request->aadhar,
            'bocw' => $request->bocw,
            'language' => $request->language,
        ]);

        if ($request->has('skills')) {
            $user->skills()->attach($request->skills);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User registered successfully',
            'employer' => $user,
            'profile' => $profile,
            'skills' => $user->skills,
            'token' => $token
        ]);
    }



    public function login(Request $request)
    {
        // Validation
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find worker by phone
        $worker = Employer::where('phone', $request->phone)->first();

        if (!$worker || !Hash::check($request->password, $worker->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Generate JWT token
        $token = JWTAuth::fromUser($worker);

        $profile = $worker->profile()->first();

        return response()->json([
            'message' => 'Employer Login successful',
            'employer' => $worker,
            'token' => $token,
            'profile' => $profile
        ]);
    }

    public function updateEmployer(Request $request, $id)
        {
            $employer = Employer::find($id);

            if (!$employer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Employer not found'
                ], 404);
            }

             $validator = Validator::make($request->all(), [
                'email' => 'nullable|email|unique:employers,email,' . $id,
                'phone' => 'sometimes|digits:10|unique:employers,phone,' . $id,
                'password' => 'sometimes|string|min:6|max:10',
                'name' => 'sometimes|string|max:255',
                'emp_type' => 'sometimes|string|max:255',
                'gender' => 'string|max:20', // ✅ added
                // ✅ Document fields
                'docType' => 'nullable|string|max:100',
                'docNumber' => 'nullable|string|max:50',
                 'pdf' => 'nullable|string',
                //'skill_id' => 'sometimes|numeric',
                'avg_worker' => 'sometimes|string|max:100',
                'work_type' => 'sometimes|string|max:100',
                'location' => 'sometimes|string|max:255',
                'availability' => 'sometimes|string|max:50',
                'eshram' => 'nullable|string|max:50',
                'aadhar' => 'nullable|string|max:12',
                'bocw' => 'nullable|string|max:50',
                'language' => 'nullable|string|max:100',
                'plot_no' => 'nullable|string|max:255',
                'street_area_village' => 'nullable|string|max:255',
                'post_office' => 'nullable|string|max:255',
                'district' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
                'pin_code' => 'nullable|string|max:10',
                'profile_image' => 'nullable|string',
            ]);



            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // ✅ Update employer basic info
            if ($request->has('password')) {
                $employer->password = Hash::make($request->password);
            }
            if ($request->has('email')) {
                $employer->email = $request->email;
            }
            if ($request->has('phone')) {
                $employer->phone = $request->phone;
            }

            if ($request->has('skills')) {
                $skills = is_array($request->skills) ? $request->skills : [$request->skills];
                $employer->skills()->sync($skills);
            }
            $employer->save();
            $profile = EmployerProfile::where('employer_id', $id)->first();


            if ($profile) {
                $profile->update($request->only([
                    'name',
                    'avg_worker',
                    'work_type',
                    'location',
                    'gender',
                    'availability',
                    'eshram',
                    'aadhar',
                    'bocw',
                    'language',
                    'plot_no',
                    'street_area_village',
                    'post_office',
                    'district',
                    'state',
                    'pin_code',
                    'docType',
                    'docNumber',
                    'emp_type'
                ]));

            // ✅ Upload directories from .env
            //$uploadPath = env('UPLOAD_PATH_EMPLOYER');
            $uploadPath = config('filesystems.paths.employer_upload');
            $imagePath= public_path($uploadPath. '/profile');
            $docPath = public_path($uploadPath . '/docs');
           // Log::info('Upload Path: ' . $uploadPath);
           // Log::info('Doc Path: ' . $docPath);

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
                'message' => 'Employer & profile updated successfully',
                'employer' => $employer,
                'profile' => $profile
            ]);
        }


    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'employer_id' => 'required|integer|exists:employers,id',
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

            $employer = Employer::find($request->employer_id);

            if (!$employer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employer not found',
                ], 404);
            }

            if (!Hash::check($request->old_password, $employer->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Old password is incorrect',
                ], 400);
            }

            $employer->password = Hash::make($request->new_password);
            $employer->save();

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully',
            ]);

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
            // ✅ Validate input
            $validator = Validator::make($request->all(), [
                'phone' => 'required|digits:10|exists:employers,phone',
                'password' => 'required|string|min:6|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // ✅ Find employer by phone
            $employer = Employer::where('phone', $request->phone)->first();

            if (!$employer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employer not found with this phone number.',
                ], 404);
            }

            // ✅ Update password securely
            $employer->password = Hash::make($request->password);
            $employer->save();

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
