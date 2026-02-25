<template>
    <BackButtonAppBar />

    <v-container class="pa-4">
        <div v-if="loading" class="d-flex justify-center align-center" style="height: 200px;">
            <v-progress-circular indeterminate color="primary" size="50"></v-progress-circular>
        </div>
        <!-- Job / Employer Info -->
        <v-card class="mb-4 pa-4" color="orange-lighten-5" elevation="2" v-if="job">
            <v-row align="center">
                <v-col cols="12" class="d-flex justify-center">
                    <v-avatar size="200" color="orange lighten-2">
                        <v-img :src="job.employer.profile.profile_image_url" alt="Worker Image" cover></v-img>
                    </v-avatar>
                </v-col>
                <v-col cols="12">
                    <h3 class="font-weight-bold mb-1">{{ job.title }}</h3>
                    <p class="mb-1"><strong>Employer:</strong> {{ job.employer.profile.name }}</p>
                    <p class="mb-1"><strong>Mobile:</strong> {{ job.employer.phone }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ job.employer.email }}</p>
                    <p class="mb-0">
                        <strong>Start:</strong> {{ job.start_date }} &nbsp; | &nbsp;
                        <strong>Close:</strong> {{ job.end_date }}
                    </p>
                    <p class="mb-0"><strong>Work Duration:</strong> {{ job.work_duration }}</p>
                </v-col>

                <v-col cols="12" class="d-flex justify-space-between">
                    <v-btn color="green" class="mb-2" @click="callEmployer">
                        <v-icon left>mdi-phone</v-icon> Call
                    </v-btn>
                    <v-btn color="orange" class="mb-2" @click="applyJob">
                        <v-icon left>mdi-briefcase-check</v-icon> Apply
                    </v-btn>
                    <v-btn color="orange" @click="goToChat">
                        <v-icon left>mdi-chat</v-icon> Chat
                    </v-btn>
                </v-col>
            </v-row>
        </v-card>

        <!-- Job Address -->
        <v-card class="mb-4 pa-4" elevation="2" v-if="job">
            <h4 class="mb-2">Job Address</h4>
            <p>{{ job.address }}</p>
            <p>{{ job.district.district_name }}</p>
            <p>{{ job.state.state_name }}</p>
            <p>{{ job.pincode }}</p>
        </v-card>

        <!-- Work Description -->
        <v-card class="mb-4 pa-4" elevation="2" v-if="job">
            <h4 class="mb-2">Work Description</h4>
            <v-row dense>
                <v-col cols="12" sm="6">
                    <p><strong>Description:</strong> {{ job.description }}</p>
                </v-col>
                <v-col cols="12" sm="6">
                    <!-- <p><strong>Tools:</strong> {{ job.tools.join(", ") }}</p> -->
                    <p><strong>Required People:</strong> {{ job.required_people }}</p>
                    <p><strong>Work Time:</strong> {{ job.work_time }}</p>
                </v-col>
            </v-row>
        </v-card>
        <!-- Skills  -->
        <v-card class="mb-4 pa-4" elevation="2" v-if="job">
            <h4 class="mb-2">Skill Required</h4>
            <v-row>
                <v-col cols="auto" v-for="(skill, index) in job.skills_list" :key="index">
                    <v-chip color="primary" class="ma-1" outlined>
                        {{ skill.name }}
                    </v-chip>
                </v-col>
            </v-row>
        </v-card>
        <!-- Facilities -->
        <v-card class="mb-4 pa-4" elevation="2" v-if="job">
            <h4 class="mb-2">Facilities Provided</h4>
            <v-row>
                <v-col cols="auto" v-for="(facility, index) in job.facilities" :key="index">
                    <v-chip color="primary" class="ma-1" outlined>
                        {{ facility.name }}
                    </v-chip>
                </v-col>
            </v-row>
        </v-card>

        <v-alert v-if="!loading && !job" type="error" color="primary">
            Job not found.
        </v-alert>

    </v-container>
</template>

<script>
import BackButtonAppBar from "@/components/header/BackButtonAppBar.vue";
import api from "@/services/api.js";
import apiRoutes from "@/services/apiRoutes.js";

export default {
    name: "JobDetailsPage",
    components: { BackButtonAppBar },
    data() {
        return {
            loading: false,
            job: null,
        };
    },
    methods: {
        callEmployer() {
            if (this.job?.employer?.phone) {
                window.location.href = `tel:${this.job.employer.phone}`;
            } else {
                alert("Employer phone number not available.");
            }
        },
        async applyJob() {
            //alert(`Applied for ${this.job.title} successfully!`);
            const workerData = JSON.parse(localStorage.getItem('labour_currentUser'))
            try {
                const res = await api.post(apiRoutes.workerJobApply, {
                    worker_id: workerData.worker.id,
                    job_post_id: this.job.id,
                });
                alert('Success !!');
                //console.log("Success:", res.data)
                this.$router.push('/worker-dashboard-home-job-applied');
            } catch (err) {
                alert(
                    "Error !!\n" +
                    Object.values(err.response?.data?.errors || {}).flat().join("\n")
                )
                //console.error("Error post:", err)
            }
        },

        goToChat() {
            alert("Upcoming ..")
            // this.$router.push({
            //     name: 'worker-chat',
            //     params: { id: this.job.employerId }
            // });
        },
        async getJob() {
            const id = this.$route.params.id;
            this.loading = true;
            try {
                const res = await api.get(apiRoutes.workerShowjob + id);

                this.job = res.data.data;
                //console.log("Success job:", this.job);
                this.loading = false;
            } catch (err) {
                alert(
                    "Error !!\n" +
                    Object.values(err.response?.data?.errors || {}).flat().join("\n")
                );
                // console.error("Error fetching worker details:", err);
                this.loading = false;
            }

        },

    },
    mounted() {
        this.getJob()
    }
};



</script>

<style scoped>
.v-card {
    transition: all 0.2s ease-in-out;
}

.v-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}
</style>
