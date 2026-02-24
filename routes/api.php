<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

use App\Http\Controllers\SkillController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SchemesPageController;
use App\Http\Controllers\WorkerHistoryController;
use App\Http\Controllers\WorkerProfileController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\EmployerJobpostController;
use App\Http\Controllers\EmployerProfileController;
use App\Http\Controllers\OtpController;



Route::post('/worker/register', [WorkerController::class, 'register']);
Route::post('/worker/login', [WorkerController::class, 'login'])->name('login');
Route::post('/worker/forgot-password', [WorkerController::class, 'forgotPassword'])->name('forgot-password');

Route::post('/employer/register', [EmployerController::class, 'register']);
Route::post('/employer/login', [EmployerController::class, 'login']);
Route::post('/employer/forgot-password', [EmployerController::class, 'forgotPassword'])->name('employer-forgot-password');


Route::get('/states', [LocationController::class, 'getStates']);
Route::post('/districts', [LocationController::class, 'getDistricts']);

Route::get('/config', [ConfigController::class, 'getConfig']);
Route::post('/config/save', [ConfigController::class, 'saveConfig']);

Route::get('/get-all-skill', [SkillController::class, 'index']);

// OTP — public (used for registration and forgot-password flows, no auth required)
Route::prefix('otp')->group(function () {
    Route::post('/send',   [OtpController::class, 'send']);
    Route::post('/verify', [OtpController::class, 'verify']);
});

Route::prefix('schemes')->group(function () {
    Route::post('/create', [SchemesPageController::class, 'store']);
    Route::get('/list', [SchemesPageController::class, 'index']);
    Route::get('/view/{id}', [SchemesPageController::class, 'show']);
    Route::post('/update/{id}', [SchemesPageController::class, 'update']);
    Route::delete('/delete/{id}', [SchemesPageController::class, 'destroy']);
});

// .........................Facilities......................Code.........................................

Route::prefix('facilities')->group(function () {
    Route::get('/', [FacilityController::class, 'index']);
    Route::post('/store', [FacilityController::class, 'store']);
    Route::get('/show/{id}', [FacilityController::class, 'show']);
    Route::put('/update-facility/{id}', [FacilityController::class, 'update']);
    Route::delete('/facility/{id}', [FacilityController::class, 'destroy']);
});

//-----------------------------------------------------------------------------------------------
//worker apis
Route::middleware(['auth:worker'])->group(function () {

Route::put('/worker/update/{id}', [WorkerController::class, 'updateWorker']);
Route::get('/get-worker/{id}', [WorkerController::class, 'getWorker']);
Route::post('/worker/change-password', [WorkerController::class, 'changePassword']);

Route::post('/worker/add-education', [WorkerProfileController::class, 'addEducation']);
Route::get('/worker/educations/{worker_id}', [WorkerProfileController::class, 'getEducation']);
Route::delete('/worker/educations-delete/{worker_id}/{id}', [WorkerProfileController::class, 'deleteEducation']);


Route::prefix('worker-history')->group(function () {
        Route::get('/', [WorkerHistoryController::class, 'index']);          // List all / filter by worker_id
        Route::post('/store', [WorkerHistoryController::class, 'store']);    // Create
        Route::get('/{id}', [WorkerHistoryController::class, 'show']);       // Read single
        Route::put('/update/{id}', [WorkerHistoryController::class, 'update']); // Update
        Route::delete('/destroy/{id}', [WorkerHistoryController::class, 'destroy']); // Delete
    });

Route::get('/worker/searchjob', [EmployerJobpostController::class, 'searchJobs']);
Route::get('/worker/show/{id}', [EmployerJobpostController::class, 'show']);

 Route::post('job/apply', [JobApplicationController::class, 'apply']); // worker apply
  Route::get('worker/applications/{id}', [JobApplicationController::class, 'workerApplications']); // worker sees his apps

});

//----------------------------------------------------------------------------------------------------
// employer apis
//---------------------------------------------------------------------------------------------
Route::middleware(['auth:employer'])->group(function () {

    Route::put('/employer/update/{id}', [EmployerController::class, 'updateEmployer']);
    Route::post('/employer/change-password', [EmployerController::class, 'changePassword']);
    Route::get('/employer/get-employer/{id}', [EmployerProfileController::class, 'getEmployer']);




    Route::prefix('employer/job-post')->group(function () {
        Route::get('/by-employer/{employer_id}', [EmployerJobpostController::class, 'getJobsByEmployer']);
        Route::post('/store', [EmployerJobpostController::class, 'store']);
        Route::post('/update/{id}', [EmployerJobpostController::class, 'update']);
        Route::get('/view/{id}', [EmployerJobpostController::class, 'view']);
   });

   Route::prefix('employer/job-applicant')->group(function () {

     Route::get('employer/{id}/applications', [JobApplicationController::class, 'employerApplications']); // employer sees all appicant
     Route::post('application/status/{id}', [JobApplicationController::class, 'changeStatus']); // change status
     Route::get('worker/{id}/applications', [JobApplicationController::class, 'workerApplications']); // worker sees his apps
    Route::get('job/{id}', [JobApplicationController::class, 'getApplicantByJobId']);
  });

  //call worker api

  Route::prefix('employer_worker')->group(function () {
      Route::get('/workers/nearby', [EmployerProfileController::class, 'findNearbyWorkers']);
      Route::get('/get-worker/{id}', [WorkerController::class, 'getWorker']);

      Route::prefix('worker-history')->group(function () {
        Route::get('/', [WorkerHistoryController::class, 'index']);          // List all / filter by worker_id
        //Route::post('/store', [WorkerHistoryController::class, 'store']);    // Create
        //Route::get('/{id}', [WorkerHistoryController::class, 'show']);       // Read single
        //Route::put('/update/{id}', [WorkerHistoryController::class, 'update']); // Update
        //Route::delete('/destroy/{id}', [WorkerHistoryController::class, 'destroy']); // Delete
    });
  });


});




