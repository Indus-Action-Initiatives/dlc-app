<template>
    <v-app>
        <!-- App Bar -->
        <v-main>
            <router-view />
            <!-- Bottom Navigation -->
            <v-bottom-navigation v-model="activeTab" color="primary" grow app>
                <v-btn to="/worker-dashboard-home">
                    <v-icon>mdi-home</v-icon>
                    <span>Home</span>
                </v-btn>

                <v-btn to="/worker-dashboard-search">
                    <v-icon>mdi-magnify</v-icon>
                    <span>Search</span>
                </v-btn>

                <v-btn to="/worker-dashboard-profile">
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
        };
    },
    methods: {

    },
};
</script>
<style></style>
