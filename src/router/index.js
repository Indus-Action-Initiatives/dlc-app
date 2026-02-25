import { createRouter, createWebHistory } from 'vue-router'

// Worker views
import Home from '../views/Home.vue'
import Worker from '../views/Worker.vue'
import WorkerLogin from '../views/WorkerLogin.vue'
import WorkerFrogotPassword from '@/views/WorkerFrogotPassword.vue'
import WorkerRegister from '../views/WorkerRegister.vue'
import WorkerDashboard from '../views/WorkerDashboard.vue'

import WorkerDashboardHomePage from '@/components/worker/WorkerHomePage.vue'
import WorkerDashboardHomePageJobApplied from '@/components/worker/home/JobApplied.vue'

import SchemesPage from '@/components/worker/home/SchemesPage.vue'

import WorkerDashboardSearchPage from '@/components/worker/WorkerSearchPage.vue'

import WorkerDashboardProfilePage from '@/components/worker/WorkerProfilePage.vue'
import WorkerDashboardBasicDetail from '@/components/worker/profile/BasicDetail.vue'
import WorkerDashboardChangePassword from '@/components/worker/profile/ChangePassword.vue'
import WorkerDashboardHelp from '@/components/worker/profile/Help.vue'
import WorkerDashboardIdentityAndVerification from '@/components/worker/profile/IdentityAndVerification.vue'
import WorkerDashboardLocation from '@/components/worker/profile/Location.vue'
import WorkerDashboardSkill from '@/components/worker/profile/Skill.vue'
import WorkerDashboardWorkHistory from '@/components/worker/profile/WorkHistory.vue'
import EducationPage from '@/components/worker/profile/EducationPage.vue'

import WorkerDashboardWorkEmployerDetail from '@/components/worker/search/WorkDetail.vue'


// Employer views
import Employer from '../views/Employer.vue'
import EmployerLogin from '../views/EmployerLogin.vue'
import EmployerForgotPassword from '@/views/EmployerForgotPassword.vue'
import EmployerRegister from '../views/EmployerRegister.vue'
import EmployerDashboard from '../views/EmployerDashboard.vue'
import EmployerDashboardHomePage from '../components/employer/HomePage.vue'
import EmployerDashboardHomePostJob from '@/components/employer/home/PostJob.vue'
import EmployerDashboardHomeAppliedWorker from '@/components/employer/home/AppliedWorker.vue'
import EmployerDashboardHomeMyJob from '@/components/employer/home/MyJob.vue'
import EmployerDashboardHomeEditJob from '@/components/employer/home/EditJob.vue'
import EmployerDashboardHomeJobDetails from '@/components/employer/home/JobDetails.vue'
import EmployerDashboardHomeViewApplicants from '@/components/employer/home/ViewApplicants.vue'
import EmployerDashboardHomeAppliedWorkerDetail from '@/components/employer/home/AppliedWorkerDetail.vue'

import EmployerDashboardSearchPage from '../components/employer/SearchPage.vue'

import EmployerDashboardProfilePage from '../components/employer/ProfilePage.vue'
import BasicDetail from '@/components/employer/profile/BasicDetail.vue'
import ChangePassword from '@/components/employer/profile/ChangePassword.vue'
import Help from '@/components/employer/profile/Help.vue'
import IdentityAndVerification from '@/components/employer/profile/IdentityAndVerification.vue'
import Location from '@/components/employer/profile/Location.vue'
import WorkDetail from '@/components/employer/profile/WorkDetail.vue'
import WorkerDetails from '@/components/employer/search/WorkerDetails.vue'

import ChatPage from '@/components/chat/ChatPage.vue'



function isAuthenticated() {
  const userData = JSON.parse(localStorage.getItem('labour_currentUser'))
  return !!userData?.token
}

const routes = [
  // Home
  { path: '/', name: 'home', component: Home },



  // Worker routes
  { path: '/worker', name: 'worker', component: Worker },
  { path: '/worker-login', name: 'worker-login', component: WorkerLogin },
  { path: '/worker-forgot-password', name: 'worker-forgot-password', component: WorkerFrogotPassword },
  { path: '/worker-register', name: 'worker-register', component: WorkerRegister },
  {
    path: '/worker-dashboard', name: 'worker-dashboard', component: WorkerDashboard, meta: { requiresAuth: true },
    children: [
      //chat
      { path: '/chat/:id?', name: 'worker-chat', component: ChatPage, props: true },
      // dsaboard pages
      { path: '/worker-dashboard-home', name: 'worker-dashboard-home', component: WorkerDashboardHomePage },
      { path: '/worker-dashboard-home-job-applied', name: 'worker-dashboard-home-job-applied', component: WorkerDashboardHomePageJobApplied },
      { path: '/worker-dashboard-home-scheme', name: 'worker-dashboard-home-scheme', component: SchemesPage },

      { path: '/worker-dashboard-search', name: 'worker-dashboard-search', component: WorkerDashboardSearchPage },
      { path: '/worker-dashboard-profile', name: 'worker-dashboard-profile', component: WorkerDashboardProfilePage },
      { path: '/worker-dashboard-profile-basic', name: 'worker-dashboard-profile-basic', component: WorkerDashboardBasicDetail },
      { path: '/worker-dashboard-profile-password', name: 'worker-dashboard-profile-password', component: WorkerDashboardChangePassword },
      { path: '/worker-dashboard-profile-help', name: 'worker-dashboard-profile-help', component: WorkerDashboardHelp },
      { path: '/worker-dashboard-profile-identity', name: 'worker-dashboard-profile-identity', component: WorkerDashboardIdentityAndVerification },
      { path: '/worker-dashboard-profile-location', name: 'worker-dashboard-profile-location', component: WorkerDashboardLocation },
      { path: '/worker-dashboard-profile-skill', name: 'worker-dashboard-profile-skill', component: WorkerDashboardSkill },
      { path: '/worker-dashboard-profile-workhistory', name: 'worker-dashboard-profile-workhistory', component: WorkerDashboardWorkHistory },
      { path: '/worker-dashboard-profile-education', name: 'worker-dashboard-profile-education', component: EducationPage },


      { path: '/worker-dashboard-employer-detail/:id', name: 'worker-dashboard-employer-detail', component: WorkerDashboardWorkEmployerDetail, props: true },
    ],

  },

  // Employer routes
  { path: '/employer', name: 'employer', component: Employer },
  { path: '/employer-login', name: 'employer-login', component: EmployerLogin },
  { path: '/employer-forgot-password', name: 'employer-forgot-password', component: EmployerForgotPassword },
  { path: '/employer-register', name: 'employer-register', component: EmployerRegister },
  {
    path: '/employer-dashboard', name: 'employer-dashboard', component: EmployerDashboard, meta: { requiresAuth: true },
    children: [
      // dsaboard pages
      { path: '/employer-dashboard-home', name: 'employer-dashboard-home', component: EmployerDashboardHomePage },
      { path: '/employer-dashboard-home-postjob', name: 'employer-dashboard-home-postjob', component: EmployerDashboardHomePostJob },
      { path: '/employer-dashboard-home-applyworker', name: 'employer-dashboard-home-applyworker', component: EmployerDashboardHomeAppliedWorker },
      { path: '/employer-dashboard-home-myjob', name: 'employer-dashboard-home-myjob', component: EmployerDashboardHomeMyJob },
      { path: '/employer-dashboard-home-edit-job/:id', name: 'employer-dashboard-home-edit-Job', component: EmployerDashboardHomeEditJob },
      { path: '/employer-dashboard-home-detail-job/:id', name: 'employer-dashboard-home-detail-Job', component: EmployerDashboardHomeJobDetails },
      { path: '/employer-dashboard-home-detail-job-view-applicant/:id', name: 'employer-dashboard-home-detail-job-view-applicant', component: EmployerDashboardHomeViewApplicants },
      { path: '/employer-dashboard-home-applied-worker-detail/:id', name: 'employer-dashboard-home-applied-worker-detail', component: EmployerDashboardHomeAppliedWorkerDetail },

      { path: '/employer-dashboard-search', name: 'employer-dashboard-search', component: EmployerDashboardSearchPage },
      { path: '/employer-dashboard-profile', name: 'employer-dashboard-profile', component: EmployerDashboardProfilePage },

      { path: '/employer-dashboard-profile-basic', name: 'employer-dashboard-profile-basic', component: BasicDetail },
      { path: '/employer-dashboard-profile-password', name: 'employer-dashboard-profile-password', component: ChangePassword },
      { path: '/employer-dashboard-profile-help', name: 'employer-dashboard-profile-help', component: Help },
      { path: '/employer-dashboard-profile-identity', name: 'employer-dashboard-profile-identity', component: IdentityAndVerification },
      { path: '/employer-dashboard-profile-location', name: 'employer-dashboard-profile-location', component: Location },
      { path: '/employer-dashboard-profile-work', name: 'employer-dashboard-profile-work', component: WorkDetail },
      // { path: '/employer-dashboard-worker-detail/:id', name: 'employer-dashboard-worker-detail', component: WorkerDetails, props: true },
    ],

  },

]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

// 🔒 Global route guard
router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !isAuthenticated()) {
    next('/') // redirect if not authenticated
  } else {
    next()
  }
})


export default router
