<template>
    <BackButtonAppBar />
    <v-container class="py-6">
        <h2 class="text-h5 font-weight-bold mb-4">Jobs You Applied For</h2>
        <div v-if="loading" class="d-flex justify-center align-center" style="height: 200px;">
            <v-progress-circular indeterminate color="primary" size="50"></v-progress-circular>
        </div>
        <!-- No jobs -->
        <v-alert v-if="!loading && jobs.length === 0" type="info" variant="tonal" border="start" class="mb-4">
            You haven't applied for any jobs yet.
        </v-alert>

        <!-- Job list -->
        <v-row v-else dense>
            <v-col v-for="(job, index) in jobs" :key="index" cols="12" md="6">
                <v-card class="pa-4" elevation="2">
                    <div class="d-flex align-center justify-space-between mb-2">
                        <h3 class="text-h6 font-weight-bold">{{ job.title }}</h3>

                        <v-chip :color="{
            accepted: 'green',
            pending: 'orange',
            rejected: 'red'
        }
        [job.status] || 'grey'" variant="flat" size="small" class="text-uppercase">
                            {{ job.status }}
                        </v-chip>
                    </div>

                    <div class="text-body-2 mb-3">
                        <div><strong>Title:</strong> {{ job.job.title }}</div>
                        <div><strong>Company:</strong> {{ job.employer.profile.name }}</div>
                        <div><strong>Location:</strong> {{ job.job.address }}</div>
                        <div><strong>Applied On:</strong> {{ job.created_at.split('T')[0] }}</div>
                    </div>

                    <v-divider class="my-3"></v-divider>

                    <div class="d-flex justify-end">
                        <v-btn color="primary" variant="flat" @click="viewJobDetails(job.id)">
                            <v-icon start>mdi-eye</v-icon>
                            View Details
                        </v-btn>
                    </div>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { ref, onMounted } from 'vue'
import BackButtonAppBar from "@/components/header/BackButtonAppBar.vue";
import api from "@/services/api.js";
import apiRoutes from "@/services/apiRoutes.js";

const router = useRouter()

// Sample job data (you can later load this from API or localStorage)
const jobs = ref([
])

const loading = ref(false)

async function getJob() {
    loading.value = true;
    const workerData = JSON.parse(localStorage.getItem('labour_currentUser'))
    try {
        const res = await api.get(apiRoutes.workerGetAppliedjob + workerData.worker.id);
        console.log("Success:", res.data);
        jobs.value = res.data.data;

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



function viewJobDetails(jobId) {
    router.push(`/worker-dashboard-employer-detail/${jobId}`)
}
onMounted(() => {
    getJob()
})
</script>

<style scoped>
.text-body-2 {
    line-height: 1.4;
}
</style>
