<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\{JobPost,JobPostFacility,Employer};

class EmployerJobpostController extends Controller
{

    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'employer_id'       => 'required|exists:employers,id',
                'title'             => 'required|string|max:255',
                'description'       => 'nullable|string',
                'skills_id'         => 'required|array',
                'address'           => 'nullable|string',
                'qualification'     => 'nullable|string',
                'rate'              => 'nullable|numeric',
                'job_type'          => 'required',
                'status'            => 'nullable',

                'tools_required'    => 'nullable|string',
                'required_people'   => 'nullable|integer',
                'work_time'         => 'nullable|string',

                'work_location'     => 'nullable|string',
                'work_duration'  => 'nullable|string',
                'lat'               => 'nullable',
                'long'               => 'nullable',

                'start_date'        => 'nullable|date',
                'end_date'          => 'nullable|date|after_or_equal:start_date',

                'state'             => 'nullable|string',
                'district'          => 'nullable|string',
                'pincode'          => 'nullable|string|max:10',

                'facilities'        => 'nullable|array', // facility IDs
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // ⭐ First Create Job Post
            $jobPost = JobPost::create([
                'employer_id'       => $request->employer_id,
                'title'             => $request->title,
                'description'       => $request->description,
                'skills_id'         => json_encode($request->skills_id),
                'address'           => $request->experience,
                'qualification'     => $request->qualification,
                'rate'              => $request->rate,
                'job_type'          => $request->job_type,
                'status'            => $request->status ?? 1,

                'tools_required'    => $request->tools_required,
                'required_people'   => $request->required_people,
                'work_time'         => $request->work_time,

                'work_duration'     => $request->work_duration,
                'lat'               => $request->lat,
                'long'               => $request->long,

                'start_date'        => $request->start_date,
                'end_date'          => $request->end_date,

                'state'             => $request->state,
                'district'          => $request->district,
                'pincode'          => $request->pincode,
                'address'          => $request->address,
            ]);

            // // ⭐ Store Facilities Mapping (Pivot Table)
            if (!empty($request->facilities)) {
                $jobPost->facilities()->sync($request->facilities);
            }

            return response()->json([
                'success' => true,
                'message' => 'Job Post Created Successfully!',
               // 'data'    => $jobPost->load('facilities')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function getJobsByEmployer($employer_id)
    {
        try {
            $employer = Employer::where('id', $employer_id)->exists();
            if (!$employer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employer not found'
                ], 404);
            }

            $jobs = JobPost::with([
                        'facilities',
                        'state',
                        'district'
                    ])
                    ->where('employer_id', $employer_id)
                    ->orderBy('id', 'DESC')
                    ->get();
             $jobs->each(function ($job) {
                            $job->applicant_count = $job->applicants->count();
                    });

            return response()->json([
                'success' => true,
                'message' => 'Jobs fetched successfully',
                'data' => $jobs
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

// Get single job post
   public function show($id)
    {
        try {
            // Load Job + Employer + Employer Profile (with accessor)
            $job = JobPost::with([
                'employer.profile', 'facilities','state','district'  // loads profile and profile_image_url accessor
            ])->findOrFail($id);

            return response()->json([
                'status'  => true,
                'message' => 'Job post details fetched successfully!',
                'data'    => $job
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Job post not found!'
            ], 404);

        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }

    }
    public function view($id)
    {
        try {
            $job = JobPost::with(['facilities','state',
                        'district'])
                            ->find($id);

            if (!$job) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job not found'
                ], 404);
            }

            $skillIds = json_decode($job->skills_id, true);
            $skillNames = [];

            if (!empty($skillIds)) {
                $skillNames = Skill::whereIn('id', $skillIds)->pluck('name');
            }

            $job->skills_list = $skillNames;

            return response()->json([
                'success' => true,
                'message' => 'Job fetched successfully',
                'data'    => $job
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

public function searchJobs(Request $request)
{
    try {
        $lat     = $request->lat;
        $long    = $request->long;
        $range   = $request->range;

        $perPage = $request->per_page ?? 10;
        $page    = $request->page ?? 1;

        $jobs = JobPost::query()

            ->join('employers', 'employers.id', '=', 'job_posts.employer_id')
            ->join('employer_profiles', 'employer_profiles.employer_id', '=', 'employers.id')

            ->select(
                'job_posts.*',
                'employers.phone as employer_phone',
                'employers.email as employer_email',
                'employer_profiles.name as employer_name',
                'employer_profiles.profile_image as employer_image'
            )

            ->selectRaw("
                (
                    6371 * acos(
                        cos(radians(?)) *
                        cos(radians(lat::float)) *
                        cos(radians(long::float) - radians(?)) +
                        sin(radians(?)) *
                        sin(radians(lat::float))
                    )
                ) AS distance
            ", [$lat, $long, $lat])

            ->when($request->skills_id, function ($q) use ($request) {
                $skills = is_array($request->skills_id) ? $request->skills_id : [$request->skills_id];
                foreach ($skills as $skill) {
                    $q->whereJsonContains('skills_id', (int)$skill);
                }
            })

            ->when($request->pincode, fn($q) => $q->where('pincode', $request->pincode))

            ->when($request->location, function ($q) use ($request) {
                $loc = explode(',', $request->location);
                $state = trim($loc[0] ?? null);
                $district = trim($loc[1] ?? null);

                if ($state) $q->where('state', $state);
                if ($district) $q->where('district', $district);
            })

            ->when($range, function ($q) use ($lat, $long, $range) {
                $q->whereRaw("
                    (
                        6371 * acos(
                            cos(radians(?)) *
                            cos(radians(lat::float)) *
                            cos(radians(long::float) - radians(?)) +
                            sin(radians(?)) *
                            sin(radians(lat::float))
                        )
                    ) <= ?
                ", [$lat, $long, $lat, $range]);
            })

            ->orderBy('distance', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);

        // ⭐ ADD ACCESSOR VALUE HERE ⭐
        $jobsCollection = $jobs->getCollection();

        $jobsCollection->transform(function ($job) {
            $profile = \App\Models\EmployerProfile::where('employer_id', $job->employer_id)->first();
            $job->employer_image_url = $profile?->profile_image_url ?? null;
            return $job;
        });

        $jobs->setCollection($jobsCollection);

        return response()->json([
            'success'      => true,
            'jobs'         => $jobs->items(),
            'current_page' => $jobs->currentPage(),
            'next_page'    => $jobs->hasMorePages() ? $jobs->currentPage() + 1 : null,
            'has_more'     => $jobs->hasMorePages(),
            'total'        => $jobs->total(),
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'error'   => $e->getMessage()
        ], 500);
    }
}


     public function update(Request $request, $id)
    {
        try {

            $jobPost = JobPost::find($id);
            if (!$jobPost) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job Post not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'title'             => 'nullable|string|max:255',
                'description'       => 'nullable|string',
                'skills_id'         => 'nullable|array',

                'address'           => 'nullable|string',
                'qualification'     => 'nullable|string',
                'rate'              => 'nullable|numeric',
                'job_type'          => 'nullable|string',

                'status'            => 'nullable|boolean',

                'tools_required'    => 'nullable|string',
                'required_people'   => 'nullable|integer',
                'work_time'         => 'nullable|string',

                'work_location'     => 'nullable|string',
                'lat'               => 'nullable',
                'long'              => 'nullable',

                'start_date'        => 'nullable|date',
                'end_date'          => 'nullable|date|after_or_equal:start_date',

                'state'             => 'nullable|string',
                'district'          => 'nullable|string',
                'pincode'          => 'nullable|string|max:10',

                'facilities'        => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $jobPost->update([
                'title'             => $request->title ?? $jobPost->title,
                'description'       => $request->description ?? $jobPost->description,

                'skills_id'         => $request->skills_id ? json_encode($request->skills_id) : $jobPost->skills_id,

                'address'           => $request->address ?? $jobPost->address,
                'qualification'     => $request->qualification ?? $jobPost->qualification,
                'rate'              => $request->rate ?? $jobPost->rate,
                'job_type'          => $request->job_type ?? $jobPost->job_type,

                'status'            => $request->status ?? $jobPost->status,

                'tools_required'    => $request->tools_required ?? $jobPost->tools_required,
                'required_people'   => $request->required_people ?? $jobPost->required_people,
                'work_time'         => $request->work_time ?? $jobPost->work_time,

                'work_location'     => $request->work_location ?? $jobPost->work_location,
                'lat'               => $request->lat ?? $jobPost->lat,
                'long'              => $request->long ?? $jobPost->long,

                'start_date'        => $request->start_date ?? $jobPost->start_date,
                'end_date'          => $request->end_date ?? $jobPost->end_date,

                'state'             => $request->state ?? $jobPost->state,
                'district'          => $request->district ?? $jobPost->district,
                'pincode'          => $request->pincode ?? $jobPost->pincode,
            ]);

            if (!empty($request->facilities)) {
                $jobPost->facilities()->sync($request->facilities);
            }

            return response()->json([
                'success' => true,
                'message' => 'Job Post Updated Successfully!',
                'data'    => $jobPost->load('facilities')
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }





}
