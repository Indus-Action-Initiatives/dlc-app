<template>
    <BackButtonAppBar />
    <v-container class="pa-4">
        <!-- Title -->
        <v-card flat class="mb-4">
            <v-card-title class="text-h6 font-weight-bold">👷 Applied Workers</v-card-title>
            <v-card-subtitle>List of workers who applied for your posted job</v-card-subtitle>
        </v-card>

        <!-- Search / Filter Bar -->
        <v-row class="mb-4" dense>
            <v-col cols="12" sm="6">
                <v-text-field v-model="search" label="Search by Name or Skill" prepend-inner-icon="mdi-magnify"
                    variant="outlined" density="compact" clearable />
            </v-col>
        </v-row>

        <!-- Worker Cards grouped by Job -->
        <v-row dense v-for="(job, jIndex) in filteredJobs" :key="job.id" class="mb-6">
            <v-col cols="12">
                <!-- Job Header -->
                <v-card class="pa-3 mb-3 rounded-lg" color="grey lighten-4" elevation="2">
                    <h3 class="font-weight-bold mb-1">{{ job.title }}</h3>
                    <p class="mb-0 text-caption">
                        <v-icon size="small" color="blue">mdi-map-marker</v-icon> {{ job.location }}
                    </p>
                    <p class="mb-0 text-caption">
                        <v-icon size="small" color="green">mdi-cash</v-icon> {{ job.salary }}
                    </p>
                </v-card>

                <!-- Workers Applied -->
                <v-row dense>
                    <v-col v-for="worker in job.workers" :key="worker.id" cols="12" sm="6" md="4">
                        <v-card class="pa-3 mb-3 rounded-lg" elevation="3">
                            <v-row align="center" no-gutters>
                                <!-- Avatar -->
                                <v-col cols="auto">
                                    <!-- <v-avatar size="64" color="orange lighten-2" class="white--text">
                                        {{ getInitials(worker.name) }}
                                    </v-avatar> -->
                                    <!-- <img src="@/assets/worker/worker2.jpg" alt="Worker" /> , -->
                                </v-col>

                                <!-- Info -->
                                <v-col class="pl-3">
                                    <h4 class="text-subtitle-1 font-weight-bold mb-1">{{ worker.name }}</h4>
                                    <p class="mb-0 text-caption">
                                        <v-icon size="small" color="orange">mdi-hammer-wrench</v-icon> {{ worker.skill
                                        }}
                                    </p>
                                    <p class="mb-0 text-caption">
                                        <v-icon size="small" color="blue">mdi-briefcase</v-icon> {{ worker.experience }}
                                        yrs experience
                                    </p>
                                    <p class="mb-0 text-caption">
                                        <v-icon size="small" color="green">mdi-phone</v-icon> {{ worker.phone }}
                                    </p>
                                </v-col>
                            </v-row>

                            <v-divider class="my-3"></v-divider>

                            <!-- Actions -->
                            <div class="d-flex justify-space-between align-center">
                                <!-- <v-chip
                                    :color="worker.status === 'Accepted' ? 'green' : worker.status === 'Pending' ? 'orange' : 'red'"
                                    text-color="white" size="small" label>
                                    {{ worker.status }}
                                </v-chip> -->

                                <div>
                                    <v-btn size="small" color="green" class="mr-2" @click="callWorker(worker)">
                                        <v-icon left small>mdi-phone</v-icon> Call
                                    </v-btn>
                                    <v-btn size="small" color="primary" @click="viewDetails(worker)">
                                        <v-icon left small>mdi-eye</v-icon> View
                                    </v-btn>
                                </div>
                            </div>
                        </v-card>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>

        <!-- Empty State -->
        <v-alert v-if="filteredWorkersCount === 0" type="info" border="start" class="mt-4">
            No workers have applied yet.
        </v-alert>
    </v-container>
</template>

<script>
import BackButtonAppBar from "@/components/header/BackButtonAppBar.vue";
export default {
    components: { BackButtonAppBar },
    name: "AppliedWorkersPage",
    data() {
        return {
            search: "",
            jobsWithApplicants: [
                {
                    id: 1,
                    title: "Masonry Work",
                    location: "New Delhi",
                    salary: "₹1500/day",
                    workers: [
                        { id: 1, name: "Ramesh Kumar", skill: "Masonry", experience: 5, phone: "+91 9876543210", status: "Pending" },
                        { id: 2, name: "Suresh Singh", skill: "Masonry", experience: 3, phone: "+91 9876543211", status: "Accepted" },
                    ],
                },
                {
                    id: 2,
                    title: "Plumbing Work",
                    location: "Mumbai",
                    salary: "₹1200/day",
                    workers: [
                        { id: 3, name: "Anil Sharma", skill: "Plumbing", experience: 4, phone: "+91 9876543212", status: "Rejected" },
                    ],
                },
            ],
        };
    },
    computed: {
        // Filter jobs and their workers by search term
        filteredJobs() {
            if (!this.search) return this.jobsWithApplicants;

            const query = this.search.toLowerCase();
            return this.jobsWithApplicants
                .map(job => {
                    const filteredWorkers = job.workers.filter(
                        w => w.name.toLowerCase().includes(query) || w.skill.toLowerCase().includes(query)
                    );
                    return { ...job, workers: filteredWorkers };
                })
                .filter(job => job.workers.length > 0);
        },
        filteredWorkersCount() {
            return this.filteredJobs.reduce((acc, job) => acc + job.workers.length, 0);
        },
    },
    methods: {
        getInitials(name) {
            if (!name) return '';
            const parts = name.split(' ');
            if (parts.length === 1) return parts[0].charAt(0).toUpperCase();
            return (parts[0].charAt(0) + parts[1].charAt(0)).toUpperCase();
        },
        callWorker(worker) {
            console.log("Calling", worker.name);
        },
        viewDetails(worker) {
            this.$router.push({ name: "employer-dashboard-home-applied-worker-detail", params: { id: worker.id } });
        },
    },
};
</script>

<style scoped>
.v-card {
    transition: 0.2s ease-in-out;
}

.v-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}
</style>
