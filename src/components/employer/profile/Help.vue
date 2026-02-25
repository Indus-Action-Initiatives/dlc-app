<template>
    <BackButtonAppBar />
    <v-container class="fill-height d-flex align-center justify-center" fluid>
        <v-row>
            <v-col>
                <v-card class="pa-4 mx-auto" max-width="400" elevation="4">
                    <v-card-title class="text-h6 d-flex align-center justify-space-between">
                        <span>Need Help?</span>
                        <v-icon color="primary">mdi-help-circle-outline</v-icon>
                    </v-card-title>

                    <v-divider class="my-2"></v-divider>

                    <v-card-text>
                        <v-row align="center" class="mb-3">
                            <v-col cols="auto">
                                <v-icon color="primary">mdi-email</v-icon>
                            </v-col>
                            <v-col>
                                <div class="text-subtitle-2">Email</div>
                                <div class="text-body-2">{{ email }}</div>
                            </v-col>
                        </v-row>

                        <v-row align="center">
                            <v-col cols="auto">
                                <v-icon color="primary">mdi-phone</v-icon>
                            </v-col>
                            <v-col>
                                <div class="text-subtitle-2">Contact No</div>
                                <div class="text-body-2">{{ phone }}</div>
                            </v-col>
                        </v-row>
                    </v-card-text>

                    <v-divider class="my-2"></v-divider>

                    <v-card-actions>
                        <v-btn color="primary" block prepend-icon="mdi-email-send-outline" @click="contactSupport">
                            Contact Support
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>

</template>

<script>

import BackButtonAppBar from "@/components/header/BackButtonAppBar.vue";
import api from "@/services/api";
import apiRoutes from "@/services/apiRoutes";

export default {
    components: { BackButtonAppBar },
    name: "HelpCard",
    data() {
        return {
            phone: "",
            email: ""
        }
    },
    methods: {
        contactSupport() {
            // You can change this to open chat or another route
            window.location.href = "mailto:support@example.com";
        },
        async getConfig() {
            try {

                const res = await api.get(apiRoutes.getAllConfig);
                console.log('config', res.data.data);

                if (res.data && res.data.data) {
                    const configs = res.data.data;

                    this.phone = configs.find(item => item.key === 'help_phone')?.value || '';
                    this.email = configs.find(item => item.key === 'help_email')?.value || '';
                }
            } catch (error) {
                console.error("Error fetching help:", error);
                alert("Failed to load help.");
            }

        }
    },
    mounted() {
        this.getConfig()
    },
};
</script>

<style scoped>
.v-card {
    border-radius: 12px;
}
</style>
