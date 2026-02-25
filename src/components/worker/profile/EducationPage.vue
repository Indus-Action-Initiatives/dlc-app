<template>
    <BackButtonAppBar />

    <v-container class="fill-height d-flex align-center justify-center" fluid>
        <v-row>
            <v-col cols="12" class="text-center mb-4">
                <h2 class="text-center mb-6">My Education Details</h2>

                <!-- ADD EDUCATION -->
                <v-card class="pa-4 mb-6" elevation="2">
                    <v-row dense>
                        <v-col cols="12" sm="6">
                            <v-select v-model="newEducation.level" :items="educationLevels" label="Education Level"
                                variant="outlined" density="comfortable" hide-details />
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field v-model="newEducation.institution" label="Institution / School / College"
                                variant="outlined" density="comfortable" hide-details />
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field v-model="newEducation.year" label="Passing Year" type="number"
                                variant="outlined" density="comfortable" hide-details />
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-file-input v-model="newEducation.file" label="Upload Certificate"
                                prepend-icon="mdi-file-upload" variant="outlined" density="comfortable" hide-details
                                accept=".pdf,.jpg,.jpeg,.png" />
                        </v-col>

                        <v-col cols="12" class="text-end">
                            <v-btn color="orange" class="text-white" @click="addEducation">
                                <v-icon left>mdi-plus</v-icon> Add Education
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-card>

                <!-- EDUCATION LIST -->
                <v-card class="pa-4" elevation="2">
                    <h3 class="mb-4">Uploaded Education Details</h3>

                    <v-row dense>
                        <v-col cols="12" v-for="(edu, index) in educationList" :key="index" class="mb-3">
                            <v-card class="pa-3" outlined>
                                <v-row align="center" justify="space-between">
                                    <v-col cols="auto">
                                        <h4 class="mb-1">{{ edu.education_level }}</h4>
                                        <p class="mb-0 text-caption">
                                            {{ edu.institution }} ({{ edu.passing_year }})
                                        </p>
                                    </v-col>

                                    <v-col cols="auto" class="d-flex align-center">
                                        <v-btn v-if="edu.certificate" color="green" size="small" variant="tonal"
                                            @click="viewCertificate(edu)">
                                            <v-icon left>mdi-eye</v-icon> View
                                        </v-btn>

                                        <v-btn color="red" size="small" variant="tonal" class="ml-2"
                                            @click="deleteEducation(edu)">
                                            <v-icon left>mdi-delete</v-icon> Delete
                                        </v-btn>
                                    </v-col>
                                </v-row>
                            </v-card>
                        </v-col>
                    </v-row>
                    <!-- v-model -->
                    <div v-if="educationList.length === 0" class="text-center text-grey mt-4">
                        No education records added yet.
                    </div>
                </v-card>
                <v-dialog v-model="showModal" max-width="900px">
                    <v-card>
                        <v-card-title class="d-flex justify-space-between">
                            Certificate
                            <v-btn icon @click="showModal = false">
                                <v-icon>mdi-close</v-icon>
                            </v-btn>
                        </v-card-title>

                        <v-card-text>
                            <iframe v-if="selectedCertificate" :src="selectedCertificate" width="100%" height="500px"
                                style="border:none;">
                            </iframe>
                        </v-card-text>
                    </v-card>
                </v-dialog>

            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import BackButtonAppBar from "@/components/header/BackButtonAppBar.vue";
import api from "@/services/api";
import apiRoutes from "@/services/apiRoutes";


export default {
    name: "EducationPage",
    components: { BackButtonAppBar },

    data() {
        return {
            educationLevels: [
                "8th",
                "10th",
                "12th",
                "Graduation",
                "Post Graduation",
                "Diploma",
                "Technical Certificate",
                "Other",
            ],

            newEducation: {
                level: "",
                institution: "",
                year: "",
                file: null,
            },

            educationList: [],
            showModal: false,
            selectedCertificate: null,
        };
    },

    methods: {
        async addEducation() {
            if (
                !this.newEducation.level ||
                !this.newEducation.institution ||
                !this.newEducation.year
            ) {
                alert("Please fill all required fields.");
                return;
            }

            let base64File = null;

            if (this.newEducation.file) {
                base64File = await this.toBase64(this.newEducation.file);
            }

            try {
                const workerData = JSON.parse(localStorage.getItem("labour_currentUser"));

                const res = await api.post(apiRoutes.workerAddEducation, {
                    worker_id: workerData.worker.id,
                    education_level: this.newEducation.level,
                    institution: this.newEducation.institution,
                    passing_year: this.newEducation.year,
                    certificate: base64File,
                });

                console.log("Saved:", res.data);

                alert("Education added successfully.");

                this.$router.push("/worker-dashboard-profile");

                this.newEducation = { level: "", institution: "", year: "", file: null };
            } catch (err) {
                console.log(err)
                alert(
                    "Error !!\n" +
                    Object.values(err.response?.data?.errors || {}).flat().join("\n")
                );
            }
        },

        viewCertificate(edu) {
            if (!edu.certificate) {
                alert("No certificate uploaded.");
                return;
            }

            this.selectedCertificate = edu.certificate;
            this.showModal = true;
        },
        async getEducation() {
            try {
                const workerData = JSON.parse(localStorage.getItem('labour_currentUser'))
                const res = await api.get(`${apiRoutes.workerGetEducation}/${workerData.worker.id}`);
                console.log('educations', res.data)
                if (res.data && res.data.data) {
                    this.educationList = res.data.data; // expected: [{id, name}]
                }
            } catch (error) {
                console.error("Error fetching educations:", error);
                alert("Failed to load educations.");
            }
        },

        deleteEducation(edu) {
            console.log(edu);
            if (!confirm("Are you sure you want to delete this education?")) {
                return;
            }
            const workerData = JSON.parse(localStorage.getItem('labour_currentUser'))
            api.delete(apiRoutes.workerDeleteEducation + '/' + workerData.worker.id + "/" + edu.id)
                .then((response) => {
                    if (response.data.success) {
                        alert("Education deleted successfully");
                        this.getEducation();
                    } else {
                        alert("Failed to delete");
                    }
                })
                .catch((error) => {
                    console.error(error);
                    alert("Something went wrong");
                });
        },
        // Convert file → Base64
        toBase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result);
                reader.onerror = reject;
            });
        },
    },
    mounted() {
        this.getEducation();
    }

};
</script>

<style scoped>
.v-card {
    transition: 0.2s ease-in-out;
}

.v-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}
</style>
