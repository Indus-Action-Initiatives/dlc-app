<template>
    <LogoutAppBar />
    <v-container>
        <div v-if="loading" class="d-flex justify-center align-center" style="height: 200px;">
            <v-progress-circular indeterminate color="primary" size="50"></v-progress-circular>
        </div>
        <v-row v-if="!loading">
            <v-col cols="12">
                <v-card class="mx-auto my-4 pa-4 " shaped elevation="5">
                    <v-row align="center">
                        <v-col cols="4" sm="4" class="text-center">
                            <v-avatar size="110" class="elevation-4">
                                <v-img :src="worker.photo" alt="Image"></v-img>
                            </v-avatar>
                        </v-col>
                        <v-col cols="8" sm="8" class="">

                            <h3 class="white--text font-weight-bold mb-1">
                                <v-icon left small color="primary">mdi-account</v-icon>
                                {{ worker.name }}
                            </h3>

                            <p class="subtitle-1 white--text mb-2">
                                <v-icon left small color="primary">mdi-phone</v-icon>
                                {{ worker.phone }}
                            </p>

                            <p class="subtitle-1 white--text mb-2">
                                <v-icon left small color="primary">mdi-mail</v-icon>
                                {{ worker.email }}
                            </p>

                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="4">
                            <v-card class="pa-3 text-center stats-card" height="120" outlined shaped elevation="2">
                                <v-icon large color="primary">mdi-clock-outline</v-icon>

                                <div class="mt-1" :class="worker.isAvailable === 'Yes' ? 'text-success' : 'text-error'"
                                    style="font-size: 12px; font-weight: 600;">
                                    {{ worker.isAvailable === 'Yes' ? 'Available' : 'Not Available' }}
                                </div>

                                <v-switch v-model="worker.isAvailable" :true-value="'Yes'" :false-value="'No'" inset
                                    color="green" hide-details density="compact" class="mt-0 d-flex justify-center"
                                    style="transform: scale(0.85);" @change="toggleAvailability" />
                            </v-card>
                        </v-col>
                        <!-- Work / Job Applied -->
                        <v-col cols="4">
                            <v-card class="pa-3 text-center d-flex flex-column align-center justify-center text-white"
                                color="secondary" height="120" style="cursor: pointer;" @click="goToJobApplied"
                                elevation="3">
                                <v-icon size="32" class="mb-1" color="primary">mdi-briefcase</v-icon>
                                <small>Work / Job Applied</small>
                            </v-card>
                        </v-col>
                        <!-- Chat -->
                        <v-col cols="4">
                            <v-card class="pa-3 text-center d-flex flex-column align-center justify-center" height="120"
                                @click="goToChat" color="secondary" elevation="2">
                                <v-icon size="32" class="mb-1" color="primary">mdi-message</v-icon>
                                <h4 class="text-h6 mb-1">Chat</h4>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-card>
            </v-col>
        </v-row>
        <!-- 1️⃣ Worker Header -->


        <!-- 2️⃣ Stats Cards -->
        <v-row class="mb-4" dense v-if="!loading">
            <v-col cols="6" sm="3">
                <v-card class="pa-4 text-center stats-card" height="118" outlined shaped elevation="2" color="">
                    <v-icon x-large color="primary">mdi-map-marker</v-icon>
                    <div class="grey--text text-caption mt-2">Location </div>
                    <div class="font-weight-bold">{{ worker.location }}</div>
                </v-card>
            </v-col>
            <v-col cols="6" sm="3">
                <v-card class="pa-4 text-center stats-card" height="118" outlined shaped elevation="2" color="">
                    <v-icon x-large color="primary">mdi-briefcase</v-icon>
                    <div class="grey--text text-caption mt-2">Experience</div>
                    <div class="font-weight-bold">{{ worker.experience }} Years</div>
                </v-card>
            </v-col>
            <!-- Age -->
            <v-col cols="6">
                <v-card class="pa-3 text-center d-flex flex-column align-center justify-center" height="120"
                    elevation="2">
                    <v-icon size="32" class="mb-1" color="primary">mdi-calendar</v-icon>
                    <small>Age</small>
                    <h4 class="text-h6 mb-1">{{ worker.age }}</h4>
                </v-card>
            </v-col>

            <v-col cols="6" sm="3">
                <v-card class="pa-4 text-center stats-card" height="118" outlined shaped elevation="2" color="secondary"
                    @click="goToscheme">
                    <v-icon x-large color="primary">mdi-currency-inr</v-icon>
                    <h4>Welfare and Schemes</h4>
                    <!-- <div class="font-weight-bold">{{ worker.rate }}</div> -->
                </v-card>
            </v-col>
        </v-row>

        <!-- 3️⃣ Work History -->
        <v-card class="mx-auto my-4 pa-4" outlined shaped elevation="3" color="" v-if="!loading">
            <h5 class="font-weight-medium mb-3 primary--text">Work History</h5>

            <v-card v-for="(exp, index) in experienceHistory" :key="index" class="mb-3 pa-3 work-card" outlined shaped
                elevation="2">


                <div class="d-flex justify-space-between font-weight-medium mb-1">
                    <span class="primary--text"> <strong>Project Name:</strong>{{ exp.project_name }}</span>
                    <span class="grey--text text--darken-1">
                        {{ formatDate(exp.start_date) }} - {{ formatDate(exp.end_date) }}
                    </span>
                </div>


                <div class="font-weight-medium">
                    <strong>Employer Name:</strong>{{ exp.employer_name || 'N/A' }}
                </div>


                <p class="mb-1"> <strong>Descriptions:</strong>{{ exp.task_description }}</p>

                <div v-if="exp.skills && exp.skills.length">
                    <strong>Skills:</strong> <v-chip-group column>
                        <v-chip v-for="(skill, i) in exp.skills" :key="i" color="primary" text-color="white"
                            class="ma-1" small>
                            {{ skill.name }}
                        </v-chip>
                    </v-chip-group>
                </div>



                <div class="d-flex justify-space-between mt-2">
                    <span><strong>Payment:</strong> {{ exp.payment_status }} (₹{{ exp.total_amount }})</span>
                    <span v-if="exp.rating"><strong>Rating:</strong> {{ exp.rating }}/5</span>
                </div>
            </v-card>

        </v-card>

        <!-- 4️⃣ Skills -->
        <v-card class="mx-auto my-4 pa-4" outlined shaped elevation="3" color="" v-if="!loading">
            <h5 class="font-weight-medium mb-3 --text">Skills & Specializations</h5>
            <v-chip-group class="d-flex flex-wrap">
                <v-chip v-for="skill in worker.skills" :key="skill" class="ma-1" color="" text-color="white" pill>
                    {{ skill.name }}
                </v-chip>
            </v-chip-group>
        </v-card>
    </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import LogoutAppBar from '@/components/header/LogoutAppBar.vue';
import api from "@/services/api.js";
import apiRoutes from "@/services/apiRoutes.js";
import dayjs from 'dayjs';
import { useRouter } from 'vue-router';



const router = useRouter()

const defaultPhoto = "https://cdn.vuetifyjs.com/images/profiles/default.png";

const worker = ref({
    name: "",
    photo: "",
    phone: "",
    skill: "",
    rating: 0,
    reviews: 0,
    location: "",
    experience: "",
    rate: "",
    isAvailable: "",
    skills: [],
    age: "",
    gender: "",
    email: ""
});

const loading = ref(false)
const experienceHistory = ref([]);


function goToJobApplied() {
    //alert('click')
    router.push('/worker-dashboard-home-job-applied') // update with your route
}

async function toggleAvailability() {

    // alert(worker.value.isAvailable);
    const workerData = JSON.parse(localStorage.getItem('labour_currentUser'))

    try {
        const res = await api.put(apiRoutes.workerUpdate + '/' + workerData.worker.id, {
            availability: worker.value.isAvailable,
        });
        alert("Success !!")
        getWorker()

        // router.push('/worker-dashboard-profile-identity')

    } catch (err) {
        alert(
            "Error !!\n" +
            Object.values(err.response?.data?.errors || {}).flat().join("\n")
        )
        console.error("Error login:", err)
    }

}

// get worker by worker id
async function getWorker() {
    loading.value = true;
    const workerData = JSON.parse(localStorage.getItem('labour_currentUser'))
    try {
        const res = await api.get(`${apiRoutes.workerGetWorkerByid}/${workerData.worker.id}`);
        console.log("Worker Success:", res.data);
        const data = res.data.worker;
        worker.value = {
            name: data.profile?.name || "", // You may need to add 'name' field in API if missing
            photo: data.profile?.profile_image_url,
            phone: data.phone || "",
            skill: data.skill?.name || "", // if skill relation exists
            rating: data.rating || 0, // default if rating exists
            reviews: data.reviews || 0,
            location: (data.profile?.district_detail?.district_name || "") +
                " , " +
                (data.profile?.state_detail?.state_name || ""),
            experience: data.profile?.experience || "",
            rate: data.profile?.rate || "",
            isAvailable: data.profile?.availability || "",
            skills: data.profile?.skills || [], // Array of skills
            // experienceHistory: data.profile?.experienceHistory || [],
            age: data.profile?.age,
            gender: data.profile?.gender,
            email: data.email || "",

        };

        loading.value = false;
    } catch (err) {
        alert(
            "Error !!\n" +
            Object.values(err.response?.data?.errors || {}).flat().join("\n")
        );
        loading.value = false;
        // console.error("Error fetching worker details:", err);
    }

}
async function getWorkerHistory() {
    const workerData = JSON.parse(localStorage.getItem('labour_currentUser'))
    try {
        const res = await api.get(apiRoutes.workerfetchAllWorkerJobHistory, { params: { worker_id: workerData.worker.id } });
        console.log("Success:", res.data);
        const data = res.data.data;
        experienceHistory.value = data;
    } catch (err) {
        alert(
            "Error !!\n" +
            Object.values(err.response?.data?.errors || {}).flat().join("\n")
        );
        // console.error("Error fetching worker details:", err);
    }
}

onMounted(async () => {
    getWorker();
    getWorkerHistory();
})

function formatDate(date) {
    return dayjs(date).format('DD MMM YYYY');
}

function goToscheme() {
    router.push('/worker-dashboard-home-scheme') // update with your route
}

function goToChat() {
    alert('Upcoming..')
    // router.push({
    //     name: 'worker-chat',
    // });
}
</script>

<style scoped></style>
