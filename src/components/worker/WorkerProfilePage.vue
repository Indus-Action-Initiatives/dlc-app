<template>
    <LogoutAppBar />
    <v-container>
        <v-row>
            <v-col cols="12" class="text-center mb-4">
                <h2>My Profile</h2>
            </v-col>
        </v-row>

        <v-list shaped dense>
            <v-list-item v-for="item in menuItems" :key="item.title" link @click="$router.push(item.route)">
                <template v-slot:prepend>
                    <v-icon color="orange darken-2" :icon="item.icon"></v-icon>
                </template>

                <v-list-item-title v-text="item.title"></v-list-item-title>

                <template v-slot:append>
                    <v-icon>mdi-chevron-right</v-icon>
                </template>
            </v-list-item>

        </v-list>
    </v-container>
</template>

<script>
import LogoutAppBar from '@/components/header/LogoutAppBar.vue';
export default {
    components: { LogoutAppBar },
    data() {
        return {
            menuItems: [
                { title: "Basic Details", icon: "mdi-account", route: "/worker-dashboard-profile-basic" },
                { title: "Identity & Verification", icon: "mdi-id-card", route: "/worker-dashboard-profile-identity" },
                { title: "Location Information", icon: "mdi-map-marker", route: "/worker-dashboard-profile-location" },
                { title: "Skill Details", icon: "mdi-briefcase", route: "/worker-dashboard-profile-skill" },
                { title: "Add Experience/ Work Deatils", icon: "mdi-plus", route: "/worker-dashboard-profile-workhistory" },
                { title: "Add Education", icon: "mdi-plus", route: "/worker-dashboard-profile-education" },
                { title: "Change Password", icon: "mdi-lock-reset", route: "/worker-dashboard-profile-password" },
                { title: "Help", icon: "mdi-help-circle", route: "/worker-dashboard-profile-help" },
            ]
        };
    }
};
</script>

<style>
.v-list-item {
    transition: background-color 0.2s;
}

.v-list-item:hover {
    background-color: rgba(255, 165, 0, 0.1);
    cursor: pointer;
}
</style>
