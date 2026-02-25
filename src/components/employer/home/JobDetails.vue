<template>
    <BackButtonAppBar />

    <v-container class="pa-4">

        <!-- Page Title -->
        <h2 class="text-center font-weight-bold my-4">
            🧱 Job Details
        </h2>
        <div v-if="loading" class="d-flex justify-center align-center" style="height: 200px;">
            <v-progress-circular indeterminate color="primary" size="50"></v-progress-circular>
        </div>

        <div v-else>
            <!-- Job Main Card -->
            <v-card elevation="3" class="pa-4 mb-4 rounded-lg">

                <v-row>
                    <v-col cols="12" sm="8">
                        <h3 class="font-weight-bold mb-2">{{ job.title }}</h3>

                        <p class="text-subtitle-2 mb-1">
                            <v-icon color="orange">mdi-calendar-range</v-icon>
                            Duration: <b>{{ job.work_duration }} Days</b>
                        </p>

                        <p class="text-subtitle-2 mb-1">
                            <v-icon color="green">mdi-currency-inr</v-icon>
                            Rate: <b>₹{{ job.rate }}/day</b>
                        </p>

                        <p class="text-subtitle-2 mb-1">
                            <v-icon color="purple">mdi-briefcase</v-icon>
                            Job Type: <b>{{ job.job_type }}</b>
                        </p>

                    </v-col>

                    <v-col cols="12" sm="4" class="text-left">
                        <v-chip :color="job.status ? 'green' : 'red'" small text-color="white">
                            {{ job.status ? 'Open' : 'Closed' }}
                        </v-chip>

                        <!-- <p class="text-caption mt-2">
                        Posted: {{ job.created_at.split("T")[0] }}
                    </p> -->
                    </v-col>
                </v-row>

            </v-card>

            <!-- Location Card -->
            <v-card elevation="2" class="pa-4 mb-4 rounded-lg">
                <h4 class="font-weight-bold mb-3">
                    📍 Job Location
                </h4>

                <p class="mb-1 text-subtitle-2">
                    <v-icon color="red">mdi-map-marker</v-icon>
                    {{ job.address }}
                </p>

                <p class="text-subtitle-2 mb-1">
                    <v-icon color="blue">mdi-map-marker-radius</v-icon>
                    Pincode: <b>{{ job.pincode }}</b>
                </p>

                <p class="text-subtitle-2 mb-1">
                    <v-icon color="teal">mdi-earth</v-icon>
                    Latitude: <b>{{ job.lat }}</b>, Longitude: <b>{{ job.long }}</b>
                </p>

                <p class="text-subtitle-2 mb-1">
                    <v-icon color="grey">mdi-map</v-icon>
                    State:
                    <b>{{ job.state?.state_name || job.state || "N/A" }}</b>
                </p>

                <p class="text-subtitle-2 mb-1">
                    <v-icon color="grey">mdi-map</v-icon>
                    District:
                    <b>{{ job.district?.district_name || job.district || "N/A" }}</b>
                </p>


            </v-card>

            <!-- Description -->
            <v-card elevation="2" class="pa-4 mb-4 rounded-lg">
                <h4 class="font-weight-bold mb-3">📝 Description</h4>
                <p class="text-body-2">{{ job.description }}</p>
            </v-card>

            <!-- Work Info -->
            <v-card elevation="2" class="pa-4 mb-4 rounded-lg">
                <h4 class="font-weight-bold mb-3">🛠️ Work Information</h4>

                <v-row dense>
                    <v-col cols="12" sm="6">
                        <p class="text-subtitle-2">
                            <v-icon color="orange">mdi-tools</v-icon>
                            Tools Required: <b>{{ job.tools_required }}</b>
                        </p>
                    </v-col>

                    <v-col cols="12" sm="6">
                        <p class="text-subtitle-2">
                            <v-icon color="purple">mdi-account-group</v-icon>
                            Required People: <b>{{ job.required_people }}</b>
                        </p>
                    </v-col>

                    <v-col cols="12" sm="6">
                        <p class="text-subtitle-2">
                            <v-icon color="blue">mdi-clock-outline</v-icon>
                            Work Time: <b>{{ job.work_time }}</b>
                        </p>
                    </v-col>

                    <v-col cols="12" sm="6">
                        <p class="text-subtitle-2">
                            <v-icon color="brown">mdi-calendar-start</v-icon>
                            Start Date: <b>{{ job.start_date }}</b>
                        </p>
                        <p class="text-subtitle-2">
                            <v-icon color="red">mdi-calendar-end</v-icon>
                            End Date: <b>{{ job.end_date }}</b>
                        </p>
                    </v-col>
                </v-row>
            </v-card>

            <!-- Facilities -->
            <v-card elevation="2" class="pa-4 rounded-lg">
                <h4 class="font-weight-bold mb-3">🏕️ Facilities Provided</h4>

                <v-row dense>
                    <v-col v-for="(facility, index) in job.facilities" :key="index" cols="6" sm="4"
                        class="d-flex align-center mb-2">
                        <v-icon color="green" class="mr-2">mdi-check-circle</v-icon>
                        <span>{{ facility.name }}</span>
                    </v-col>
                </v-row>

            </v-card>

        </div>
    </v-container>
</template>

<script>
import BackButtonAppBar from "@/components/header/BackButtonAppBar.vue";
import api from "@/services/api.js";
import apiRoutes from "@/services/apiRoutes.js";
export default {
    components: { BackButtonAppBar },
    name: "JobDetailsPage",
    data() {
        return {
            job: {
            },
            loading: false,
        };
    },
    methods: {
        async getMyjob() {
            this.loading = true;
            const jobId = this.$route.params.id;
            try {
                const res = await api.get(apiRoutes.employerJobGetByJobId + jobId);
                console.log("my single  job:", res.data);
                this.job = res.data.data;


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
        this.getMyjob()
    },
};
</script>

<style scoped>
.v-card {
    border-radius: 10px;
    transition: 0.3s;
}

.v-card:hover {
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
}
</style>
