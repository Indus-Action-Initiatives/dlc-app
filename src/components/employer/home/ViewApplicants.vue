<template>
    <BackButtonAppBar />
    <v-container class="pa-4">

        <!-- Header -->
        <v-row align="center" justify="space-between" class="mb-4 mt-4 ms-2">
            <h2 class="text-h6 font-weight-bold">
                <v-icon color="primary" left>mdi-account-group</v-icon>
                Applicants for: {{ job.title ?? 'N/A' }}
            </h2>
        </v-row>
        <div v-if="loading" class="d-flex justify-center align-center" style="height: 200px;">
            <v-progress-circular indeterminate color="primary" size="50"></v-progress-circular>
        </div>

        <!-- Applicants List -->
        <v-row dense v-else>
            <v-col v-for="(applicant, index) in applicants" :key="index" cols="12" sm="6" md="4">
                <v-card class="pa-3" elevation="3" rounded="lg">
                    <!-- Profile Section -->
                    <v-row no-gutters align="center">
                        <v-col cols="2">
                            <v-avatar size="60">
                                <v-img :src="applicant.worker.profile.profile_image_url"></v-img>
                            </v-avatar>
                        </v-col>

                        <v-col cols="7">
                            <h4 class="text-subtitle-1 font-weight-bold mb-1">
                                {{ applicant.worker.profile.name }}
                            </h4>
                            <p class="text-caption mb-0">
                                <v-icon small color="primary" left>mdi-hammer-wrench</v-icon>
                                {{ applicant.worker.profile.skills }}
                            </p>
                            <p class="text-caption mb-0">
                                <v-icon small color="green" left>mdi-briefcase-clock</v-icon>
                                {{ applicant.worker.profile.experience }} exp.
                            </p>
                        </v-col>
                        <v-col cols="3">
                            <v-btn variant="outlined" :class="{
                    'text-blue': applicant.status === 'pending',
                    'text-green': applicant.status === 'success',
                    'text-red': applicant.status === 'rejected'
                }">
                                {{ applicant.status }}
                            </v-btn>

                        </v-col>
                    </v-row>

                    <!-- Contact & Location -->
                    <v-divider class="my-2"></v-divider>

                    <p class="text-caption mb-1">
                        <v-icon small color="blue" left>mdi-map-marker</v-icon>
                        {{ applicant.worker.profile.location }}
                    </p>
                    <p class="text-caption mb-1">
                        <v-icon small color="orange" left>mdi-phone</v-icon>
                        {{ applicant.worker.phone }}
                    </p>

                    <!-- Skills -->
                    <div class="mt-2">
                        <v-chip-group column>
                            <v-chip v-for="(skill, i) in applicant.worker.profile.skills" :key="i" color="primary"
                                text-color="white" small class="ma-1">
                                <v-icon start small>mdi-hammer</v-icon>{{ skill }}
                            </v-chip>
                        </v-chip-group>
                    </div>

                    <!-- Action Buttons -->
                    <v-divider class="my-3"></v-divider>
                    <div class="d-flex justify-space-between">
                        <v-btn size="small" color="blue" variant="tonal" @click="viewDetails(applicant.worker_id)">
                            <v-icon left>mdi-eye</v-icon> View
                        </v-btn>

                        <v-btn size="small" color="green" variant="tonal" @click="acceptApplicant(applicant)">
                            <v-icon left>mdi-check-circle</v-icon> Accept
                        </v-btn>

                        <v-btn size="small" color="red" variant="tonal" @click="rejectApplicant(applicant)">
                            <v-icon left>mdi-close-circle</v-icon> Reject
                        </v-btn>
                    </div>
                </v-card>
            </v-col>
        </v-row>

        <v-alert v-if="!loading && applicants.length === 0" type="info" border="start" class="mt-4">No Data !!</v-alert>
    </v-container>
</template>

<script>
import BackButtonAppBar from "@/components/header/BackButtonAppBar.vue";
import api from "@/services/api.js";
import apiRoutes from "@/services/apiRoutes.js";

export default {
    components: { BackButtonAppBar },
    name: "ViewApplicantsPage",
    data() {
        return {
            job: "",
            loading: false,
            applicants: [
            ],
        };
    },
    methods: {
        viewDetails(id) {
            this.$router.push({ name: "employer-dashboard-home-applied-worker-detail", params: { id: id } });
        },
        async acceptApplicant(applicant) {
            try {
                const res = await api.post(apiRoutes.employerJobApplicantStatusByApplicatId + applicant.id, {
                    status: "accepted"
                });

                ////console.log("my job applicant::", this.job);
                alert("Success !!")
                this.getApplicant()

            } catch (err) {
                alert(
                    "Error !!\n" +
                    Object.values(err.response?.data?.errors || {}).flat().join("\n")
                );
                //console.error("Error fetching state", err);
            }
            finally {
                this.loading = false; // stop loading
            }
        },
        async rejectApplicant(applicant) {
            try {
                const res = await api.post(apiRoutes.employerJobApplicantStatusByApplicatId + applicant.id, {
                    status: "rejected"
                });
                ////console.log("my job applicant::", this.job);
                alert("Success !!")
                this.getApplicant()

            } catch (err) {
                alert(
                    "Error !!\n" +
                    Object.values(err.response?.data?.errors || {}).flat().join("\n")
                );
                //console.error("Error fetching state", err);
            }
            finally {
                this.loading = false; // stop loading
            }
        },
        async getApplicant() {
            this.loading = true;
            const employerData = JSON.parse(localStorage.getItem("labour_currentUser"));
            const jobId = this.$route.params.id;
            try {
                const res = await api.get(apiRoutes.employerJobApplicantGetByJobID + jobId);
                //console.log("my job applicant::", res.data);
                this.applicants = res.data.data;
                this.job = res.data?.job?.job || {};
                ////console.log("my job applicant::", this.job);


            } catch (err) {
                alert(
                    "Error !!\n" +
                    Object.values(err.response?.data?.errors || {}).flat().join("\n")
                );
                //console.error("Error fetching state", err);
            }
            finally {
                this.loading = false; // stop loading
            }
        }
    },
    mounted() {
        this.getApplicant()
    }
};
</script>

<style scoped>
.v-card {
    transition: 0.2s ease-in-out;
}

.v-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
}
</style>
