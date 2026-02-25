<template>
    <v-app>
        <v-app-bar color="primary" dark flat style="position: sticky;" class="pb-2 pt-4 px-2">
            <v-row align="center" class="fill-height" no-gutters>
                <v-col class="d-flex align-center ms-2 mt-2" cols="auto" style="gap: 12px;">
                    <div style="width: 32px;height: 32px;border: 2px solid white;border-radius: 50%;overflow: hidden;
                        display: flex;justify-content: center;align-items: center;">
                        <img src="../assets/bharat.jpg" alt="India Flag"
                            style="width: 28px; height: 28px; object-fit: cover;" />
                    </div>
                    <div style="line-height: 1;">
                        <div style="font-weight: 700; font-size: 14px;">भारत सरकार</div>
                        <div style="font-size: 12px;">Govt. of India</div>
                    </div>
                </v-col>
            </v-row>
            <v-btn icon @click="goBack">
                <v-icon>mdi-arrow-left-bold</v-icon>
            </v-btn>
        </v-app-bar>
        <v-main>
            <v-container class="py-12 fill-height d-flex align-center justify-center" fluid>
                <v-row>
                    <v-col cols="12">
                        <!-- Card Wrapper -->
                        <v-card outlined class="py-5">
                            <v-card-title class="text-center">
                                <v-icon large color="orange darken-2">mdi-hard-hat</v-icon>
                            </v-card-title>

                            <v-card-text class="text-center">
                                <h2>Are you an existing employer?</h2>

                                <v-row class="mt-8" justify="center" align="stretch" dense>
                                    <!-- Existing User -->
                                    <v-col cols="12" sm="6" class="mt-2">
                                        <v-card outlined style="border: 2px solid #ff9800;" class="pa-4"
                                            @click="showScreen('employer-login')">
                                            <v-row align="center" no-gutters>
                                                <v-col cols="auto">
                                                    <v-icon color="orange darken-2" large>mdi-login</v-icon>
                                                </v-col>
                                                <v-col>
                                                    <div>
                                                        <h3 class="mb-1">Existing User</h3>
                                                        <span class="caption">Login with phone number</span>
                                                    </div>
                                                </v-col>
                                            </v-row>
                                        </v-card>
                                    </v-col>

                                    <!-- New User -->
                                    <v-col cols="12" sm="6" class="mt-2">
                                        <v-card tonal color="primary" class="pa-4" @click="registerEmployer()">
                                            <v-row align="center" no-gutters style="color:white">
                                                <v-col cols="auto">
                                                    <v-icon large>mdi-account-plus</v-icon>
                                                </v-col>
                                                <v-col>
                                                    <div>
                                                        <h3 class="mb-1">New User</h3>
                                                        <span class="caption">Register for the first time</span>
                                                    </div>
                                                </v-col>
                                            </v-row>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
export default {
    name: "AuthSelection",
    methods: {
        showScreen(screen) {
            this.$router.push({
                name: screen
            });
        },
        registerEmployer() {
            this.$router.push({
                name: 'employer-register'
            });
        },
        goBack() {
            this.$router.back()
        },
    },
};
</script>