<template>
    <BackButtonAppBar />
    <v-container class="pa-4">
        <!-- Header -->
        <v-card flat class="mb-4 mt-4">
            <v-card-title class="text-h6 font-weight-bold">
                🧱 My Jobs
            </v-card-title>
            <v-card-subtitle>Manage or edit your posted jobs</v-card-subtitle>

        </v-card>

        <div v-if="loading" class="d-flex justify-center align-center" style="height: 200px;">
            <v-progress-circular indeterminate color="primary" size="50"></v-progress-circular>
        </div>


        <!-- Job Cards -->
        <v-row dense v-else>
            <v-col v-for="(job, index) in jobs" :key="index" cols="12" sm="6" md="4">
                <v-card class="pa-3 mb-3 rounded-lg" elevation="3" :color="job.status === true
            ? 'green-lighten-5'
            : job.status === false
                ? 'red-lighten-5'
                : ' '">
                    <!-- Job Header -->
                    <div class="d-flex justify-space-between align-center mb-2">
                        <span class="text-subtitle-1 font-weight-bold">{{ job.title }}</span>
                        <v-chip :color="statusColor(job.status)" size="small" text-color="white" label>
                            {{ job.status ? 'Open' : 'Closed' }}
                        </v-chip>

                    </div>

                    <!-- Job Info -->
                    <v-card-text class="pa-0">
                        <p class="mb-1 text-caption">
                            <v-icon size="small" color="blue-darken-2">mdi-map-marker</v-icon>
                            {{ job.address }}
                        </p>
                        <p class="mb-1 text-caption">
                            <v-icon size="small" color="orange-darken-2">mdi-calendar-range</v-icon>
                            {{ job.work_duration }}
                        </p>
                        <p class="mb-1 text-caption">
                            <v-icon size="small" color="green-darken-2">mdi-currency-inr</v-icon>
                            ₹{{ job.rate }}/day
                        </p>
                        <p class="mb-1 text-caption">
                            <v-icon size="small" color="purple-darken-2">mdi-account-group</v-icon>
                            {{ job.applicant_count }} Applicants
                        </p>
                        <v-btn color="orange" size="small" variant="elevated" @click="viewApplicants(job)">
                            <v-icon left small>mdi-account-eye</v-icon> View Applicant
                        </v-btn>
                    </v-card-text>

                    <v-divider class="my-2"></v-divider>

                    <!-- Actions -->
                    <div class="d-flex justify-space-between">


                        <v-btn color="green" size="small" variant="elevated" @click="view(job)">
                            <v-icon left small>mdi-account-eye</v-icon> View
                        </v-btn>

                        <v-btn color="primary" size="small" variant="tonal" @click="editJob(job)">
                            <v-icon left small>mdi-pencil</v-icon> Edit
                        </v-btn>

                    </div>
                </v-card>
            </v-col>
        </v-row>

        <!-- No Jobs -->
        <v-alert v-if="!loading && jobs.length === 0" type="info" border="start" class="mt-4">You haven’t posted any
            jobs yet.</v-alert>
    </v-container>
</template>

<script>
import BackButtonAppBar from "@/components/header/BackButtonAppBar.vue";
import api from "@/services/api.js";
import apiRoutes from "@/services/apiRoutes.js";

export default {
    components: { BackButtonAppBar },
    name: "MyJobsPage",
    data() {
        return {
            editDialog: false,
            selectedJob: null,
            jobs: [],
            loading: false,
        };
    },

    methods: {
        statusColor(status) {
            return status === true
                ? "green"
                : status === false
                    ? "red" : '';
        },
        viewApplicants(job) {
            //alert(`Viewing applicants for "${job.title}"`);
            this.$router.push({ name: 'employer-dashboard-home-detail-job-view-applicant', params: { id: job.id } });
        },
        view(job) {
            //alert(`Viewing applicants for "${job.title}"`);
            this.$router.push({ name: 'employer-dashboard-home-detail-Job', params: { id: job.id } });
        },
        editJob(job) {
            this.$router.push({
                name: 'employer-dashboard-home-edit-Job',
                params: { id: job.id }
            });
        },

        async getMyjob() {
            this.loading = true;
            const employerData = JSON.parse(localStorage.getItem("labour_currentUser"));
            try {
                const res = await api.get(apiRoutes.employerJobGetByEployerId + employerData.employer.id);
                console.log("my job:", res.data);
                this.jobs = res.data.data;


            } catch (err) {
                alert(
                    "Error !!\n" +
                    Object.values(err.response?.data?.errors || {}).flat().join("\n")
                );
                console.error("Error fetching state", err);
            }
            finally {
                this.loading = false; // stop loading
            }

        }
    },
    mounted() {
        this.getMyjob(),
            setTimeout(() => this.showAlert = true, 1000);

    }
};
</script>

<style scoped>
.v-card {
    transition: 0.25s ease-in-out;
}

.v-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}
</style>
