<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Models\JobApplication;
use App\Models\JobPost;
use Illuminate\Support\Facades\Validator;

class JobApplicationController extends Controller
{
    //
    // Worker applies to a job
    public function apply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'worker_id'   => 'required|exists:workers,id',
            'job_post_id' => 'required|exists:job_posts,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message'=>'Validation failed','errors'=>$validator->errors()], 422);
        }

        $job = JobPost::find($request->job_post_id);

        // Prevent duplicate apply
        $exists = JobApplication::where('worker_id', $request->worker_id)
                    ->where('job_post_id', $request->job_post_id)
                    ->exists();

        if ($exists) {
            return response()->json(['success'=>false,'message'=>'Already applied for this job'], 400);
        }

        $application = JobApplication::create([
            'worker_id'   => $request->worker_id,
            'job_post_id' => $request->job_post_id,
            'employer_id' => $job->employer_id,
            'status'      => 'pending',
        ]);

        return response()->json(['success'=>true,'message'=>'Applied successfully','data'=>$application], 201);
    }

    // Employer: list applications for his jobs
    public function employerApplications($employerId)
    {
        $applications = JobApplication::with(['worker','job'])
            ->where('employer_id', $employerId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success'=>true,'data'=>$applications], 200);
    }

    // Employer: change status (accept/reject)
    public function changeStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,accepted,rejected',
            'note'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message'=>'Validation failed','errors'=>$validator->errors()], 422);
        }

        $application = JobApplication::find($id);
        if (!$application) {
            return response()->json(['success'=>false,'message'=>'Application not found'], 404);
        }

        $application->update([
            'status' => $request->status,
            'note'   => $request->note,
        ]);

        // Optional: notify worker / employer here

        return response()->json(['success'=>true,'message'=>'Status updated','data'=>$application], 200);
    }

    // Worker: view his applications
    public function workerApplications($workerId)
    {
        $applications = JobApplication::with(['job','employer','employer.profile'])
            ->where('worker_id', $workerId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success'=>true,'data'=>$applications], 200);
    }

    // employer view applicant base on job id
    public function getApplicantByJobId($id){

           $applications = JobApplication::with(['job','worker','worker.profile'])
            ->where('job_post_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();


            $job = JobApplication::with(['job'])
            ->where('job_post_id', $id)
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json(['success'=>true,'data'=>$applications,'job'=>$job], 200);
    }

}
