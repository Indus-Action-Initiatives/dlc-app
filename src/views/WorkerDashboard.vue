<template>
    <v-app>
        <!-- App Bar -->
        <v-main>
            <router-view />
            <!-- Bottom Navigation -->
            <v-bottom-navigation v-model="activeTab" color="primary" grow app>
                <v-btn :to="isEmployer ? '/employer-dashboard-home' : '/worker-dashboard-home'">
                    <v-icon>mdi-home</v-icon>
                    <span>Home</span>
                </v-btn>

                <v-btn :to="isEmployer ? '/employer-dashboard-search' : '/worker-dashboard-search'">
                    <v-icon>mdi-magnify</v-icon>
                    <span>Search</span>
                </v-btn>

                <v-btn :to="isEmployer ? '/employer-dashboard-profile' : '/worker-dashboard-profile'">
                    <v-icon>mdi-account-circle</v-icon>
                    <span>Profile</span>
                </v-btn>
            </v-bottom-navigation>
        </v-main>
    </v-app>
</template>
<script>

export default {

    data() {
        return {
            activeTab: 0,
            isEmployer: false,
        };
    },
    mounted() {
        this.isEmployer = this.detectIsEmployer();
    },
    watch: {
        // If login state changes while staying on this layout.
        '$route.fullPath'() {
            this.isEmployer = this.detectIsEmployer();
        }
    },
    methods: {
        detectIsEmployer() {
            try {
                const raw = localStorage.getItem('labour_currentUser');
                const userData = raw ? JSON.parse(raw) : null;
                return !!userData?.employer?.id;
            } catch {
                return false;
            }
        },

    },
};
</script>
<style></style>
