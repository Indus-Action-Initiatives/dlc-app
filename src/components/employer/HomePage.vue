<template>
    <LogoutAppBar />
    <v-container class="pa-4">
        <div v-if="loading" class="d-flex justify-center align-center" style="height: 200px;">
            <v-progress-circular indeterminate color="primary" size="50"></v-progress-circular>
        </div>
        <!-- Employer Info Card -->
        <v-card elevation="4" class="pa-4 mb-4" v-if="!loading">
            <v-row align="center">
                <v-col cols="auto">
                    <v-avatar size="72">
                        <img :src="employer.profile.profile_image_base64" alt="Employer" />
                    </v-avatar>
                </v-col>
                <v-col>
                    <h3 class="mb-1">{{ employer.profile.name }}</h3>
                    <p class="text-subtitle-1 mb-0">{{ employer.phone }}</p>
                </v-col>
            </v-row>

            <!-- Stats Section -->
            <v-row class="mt-4" dense>
                <v-chip-group class="d-flex flex-wrap">
                    <v-chip v-for="skill in employer.skills" :key="skill" class="ma-1" color="" text-color="white" pill>
                        {{ skill.name }}
                    </v-chip>
                </v-chip-group>

                <!-- <v-col cols="6">
                    <v-card class="pa-3 text-center" color="">
                        <h4 class="text-h6 mb-1">{{ stats.hired }}</h4>
                        <small>Workers Hired</small>
                    </v-card>
                </v-col>
                <v-col cols="6">
                    <v-card class="pa-3 text-center" color="">
                        <h4 class="text-h6 mb-1">{{ stats.activeJobs }}</h4>
                        <small>Active Jobs</small>
                    </v-card>
                </v-col> -->
            </v-row>
        </v-card>

        <!-- Action Cards -->
        <v-row dense v-if="!loading">
            <v-col cols="6">
                <v-card class="pa-4 text-center" color="secondary" @click="goTo('employer-dashboard-home-postjob')">
                    <v-icon size="36" color="primary">mdi-briefcase-plus</v-icon>
                    <div class="mt-2">Post Job</div>
                </v-card>
            </v-col>
            <v-col cols="6">
                <v-card class="pa-4 text-center" color="secondary" @click="goTo('employer-dashboard-home-myjob')">
                    <v-icon size="36" color="primary">mdi-message-text</v-icon>
                    <div class="mt-2">My Jobs</div>
                </v-card>
            </v-col>

            <v-col cols="12">
                <v-card class="pa-4 text-center" color="secondary" @click="goTo('employer-dashboard-search')">
                    <v-icon size="36" color="primary">mdi-account-search</v-icon>
                    <div class="mt-2">Find Worker</div>
                </v-card>
            </v-col>

            <!-- <v-col cols="6">
                <v-card class="pa-4 text-center" color="secondary" @click="goTo('employer-dashboard-home-applyworker')">
                    <v-icon size="36" color="primary">mdi-account-cog</v-icon>
                    <div class="mt-2">Applied Worker</div>
                </v-card>
            </v-col> -->

        </v-row>
    </v-container>
</template>

<script>
import LogoutAppBar from '@/components/header/LogoutAppBar.vue';
import api from "@/services/api.js";
import apiRoutes from "@/services/apiRoutes.js";

export default {
    components: { LogoutAppBar },
    data() {
        return {
            loading: false,
            employer: {

                profile: {
                    profile_image_base64: '',  // Employer avatar
                    name: '',                  // Employer name
                },
                phone: '',                     // Employer phone number
                skills: [],

            },
            stats: {
                hired: 12,
                activeJobs: 5,
                messages: 9,
            },
        };
    },
    methods: {
        goTo(page) {
            console.log("Navigating to:", page);
            // Example navigation
            this.$router.push(`/${page}`);
        },
        async getEmployer() {
            this.loading = true;
            const employerData = JSON.parse(localStorage.getItem("labour_currentUser"));

            try {
                const res = await api.get(
                    `${apiRoutes.employerGetById}/${employerData.employer.id}`
                );
                const data = res.data.employer;
                //this.employer = data;
                this.employer.profile.profile_image_base64 = data.profile?.profile_image_base64 || this.defaultAvatar;
                this.employer.profile.name = data.profile?.name || '';
                this.employer.phone = data.phone || '';
                this.employer.skills = data.skills || [];
                //console.log(data);
                this.loading = false;
            } catch (err) {
                alert("Error loading data", err);
                this.loading = false;
            }

        }
    },
    mounted() {
        this.getEmployer()
    }
};
</script>
