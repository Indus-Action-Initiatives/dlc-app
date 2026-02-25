// export const mapSecretKey = import.meta.env.VITE_MAP_SECRET_KEY;

const apiRoutes = {
    mapSecretKey: '323636d60cb7037d21b710ee3d1716d8',
    // worker
    workerLogin: 'worker/login',
    workerForgotPassword: '/worker/forgot-password',
    workerRegister: 'worker/register',
    getAllScheme: 'schemes/list',
    // auth
    workerUpdate: 'worker/update',
    workerGetWorkerByid: 'get-worker', // get get-worker/1 , woker_id
    workerChangePassword: '/worker/change-password', //post send worker id
    workerCreateJobHistory: 'worker-history/store',
    workerfetchAllWorkerJobHistory: 'worker-history',
    workerfetchSingleRecordjobHistory: 'worker-history', //worker-history/1
    workerUpdatejobHistory: 'worker-history/update', //worker-history/1
    workerDeletejobHistory: 'worker-history/destroy', //worker-history/1
    workerAddEducation: 'worker/add-education',
    workerGetEducation: '/worker/educations', //{worker_id}'
    workerDeleteEducation: '/worker/educations-delete', //{worker_id}/{id}'
    workerSearchjobJobs: 'worker/searchjob',
    workerShowjob: 'worker/show/', //id
    workerJobApply: 'job/apply',
    workerGetAppliedjob: 'worker/applications/', //job id 1

    // get state an ddistrict 
    getState: 'states',
    getDistrict: 'districts',

    //extra 
    getAllSkill: 'get-all-skill',
    getAllConfig: 'config',
    getFacilities: 'facilities',
    //---------------------------------------------------------------
    // employer api call---------------------------------------------
    employerLogin: 'employer/login',
    employerForgotPassword: 'employer/forgot-password',
    employerRegister: 'employer/register',
    //auth
    employerUpdate: 'employer/update', //put 
    employerGetById: 'employer/get-employer', //get 1
    employerChangePassword: '/employer/change-password',
    employerJobPost: 'employer/job-post/store',
    employerJobGetByEployerId: 'employer/job-post/by-employer/', //id employer/job-post/1 {employer_id}
    employerJobGetByJobId: 'employer/job-post/view/', //id employer/job-post/1 {employer_id}
    employerJobUpdate: 'employer/job-post/update/',//job id
    //applicant
    employerJobApplicantGetByJobID: 'employer/job-applicant/job/', //job id
    employerJobApplicantStatusByApplicatId: 'employer/job-applicant/application/status/', // worker_id
    //woker
    employerWorkerGetWorkerByID: 'employer_worker/get-worker/', //id
    employerWorkerfetchAllWorkerJobHistory: 'employer_worker/worker-history',
    employerWorkerfetchAllWorker: 'employer_worker/workers/nearby' //?lat=28.640&lng=77.220&skill_id=3&range=10



}

export const OTP_ROUTES = {
  send:   '/otp/send',
  verify: '/otp/verify',
};

export default apiRoutes;