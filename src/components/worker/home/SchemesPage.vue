<template>
    <BackButtonAppBar />
    <v-container class="py-6">
        <h2 class="text-center mb-6">Government Schemes for Workers</h2>

        <div v-if="loading" class="d-flex justify-center align-center" style="height: 200px;">
            <v-progress-circular indeterminate color="primary" size="50"></v-progress-circular>
        </div>

        <v-row dense>
            <v-col v-for="(scheme, index) in schemes" :key="index" cols="12" sm="6" md="4">
                <v-card class="pa-4 text-center hover-elevate" elevation="2" rounded="xl" outlined>
                    <v-icon size="48" color="primary" class="mb-2">mdi-domain</v-icon>
                    <h3 class="text-subtitle-1 font-weight-medium mb-2">
                        {{ scheme.name }}
                    </h3>
                    <p class="text-caption grey--text mb-4" style="min-height: 50px;">
                        {{ scheme.description }}
                    </p>

                    <v-btn color="orange-darken-2" variant="tonal" size="small" append-icon="mdi-open-in-new"
                        @click="openLink(scheme.url)">
                        Know More
                    </v-btn>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import BackButtonAppBar from "@/components/header/BackButtonAppBar.vue";
import api from "@/services/api.js";
import apiRoutes from "@/services/apiRoutes.js";

export default {
    components: { BackButtonAppBar },
    name: "SchemesPage",
    data() {
        return {
            loading: false,
            schemes: [

            ],
        };
    },
    methods: {
        openLink(url) {
            window.open(url, "_blank");
        },
        async getScheme() {
            this.loading = true;
            try {
                const res = await api.get(apiRoutes.getAllScheme);
                console.log("Success:", res.data);
                this.schemes = res.data.data;
                this.loading = false;

            } catch (err) {
                alert(
                    "Error !!\n" +
                    Object.values(err.response?.data?.errors || {}).flat().join("\n")
                );
                this.loading = false;
                // console.error("Error fetching worker details:", err);
            }
        },

    },
    mounted() {
        this.getScheme()
    }
};
</script>

<style scoped>
.hover-elevate {
    transition: all 0.25s ease-in-out;
}

.hover-elevate:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}
</style>
