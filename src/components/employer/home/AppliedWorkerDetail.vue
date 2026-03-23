<template>
    <BackButtonAppBar />
    <v-container class="py-6 px-4">
        <!-- Header -->
        <v-card class="pa-4 mb-4" color="primary" dark rounded="lg">
            <v-card-title class="text-h6 font-weight-bold d-flex align-center justify-space-between">
            </v-card-title>
            <div v-if="loading" class="d-flex justify-center align-center" style="height: 200px;">
                <v-progress-circular indeterminate color="primary" size="50"></v-progress-circular>
            </div>
            <v-row align="center">
                <v-col cols="12" class="d-flex justify-center">
                    <v-avatar size="200" color="orange lighten-2">
                        <v-img :src="worker.photo" alt="Worker Image" cover></v-img>
                    </v-avatar>
                </v-col>


                <v-col>
                    <p class="font-weight-bold mb-1 text-subtitle-1 ">
                        <v-icon left color="white">mdi-account</v-icon>
                        {{ worker.name }}
                    </p>
                    <p class="mb-1">
                        <v-icon left color="white">mdi-phone</v-icon>
                        {{ worker.phone }}
                    </p>
                    <p class="mb-0">
                        <v-icon left color="white">mdi-mail</v-icon>
                        {{ worker.email }}
                    </p>
                </v-col>
            </v-row>

            <v-card-actions class="mt-2">
                <v-btn color="white" variant="flat" class="mr-2" @click="callWorker(worker.phone)">
                    <v-icon left color="primary">mdi-phone</v-icon>
                    Call
                </v-btn>
                <v-spacer></v-spacer>
                <!-- <v-btn color="secondary" variant="flat" @click="hireWorker">
                    <v-icon left>mdi-account-check</v-icon>
                    Accept
                </v-btn> -->
            </v-card-actions>
        </v-card>

        <!-- Worker Info -->
        <v-row dense class="mb-4">
            <v-col cols="6" sm="3">
                <v-card class="pa-3 text-center" color="orange-lighten-5" rounded="lg">
                    <v-icon color="orange" size="28">mdi-briefcase</v-icon>
                    <div class="font-weight-bold mt-1">{{ worker.experience }}</div>
                    <small>Experience</small>
                </v-card>
            </v-col>



            <v-col cols="6" sm="3">
                <v-card class="pa-3 text-center" color="orange-lighten-5" rounded="lg">
                    <v-icon color="orange" size="28">mdi-currency-inr</v-icon>
                    <div class="font-weight-bold mt-1">{{ worker.rate }}</div>
                    <small>Daily Rate</small>
                </v-card>
            </v-col>

            <v-col cols="6" sm="3">
                <v-card class="pa-3 text-center" color="orange-lighten-5" rounded="lg">
                    <v-icon color="orange" size="28">mdi-clock-outline</v-icon>
                    <div class="font-weight-bold mt-1">{{ worker.availability }}</div>
                    <small>Availability</small>
                </v-card>
            </v-col>
            <v-col cols="6" sm="3">
                <v-card class="pa-3 text-center" color="orange-lighten-5" rounded="lg" @click="chat()">
                    <v-icon color="orange" size="28">mdi-mail</v-icon>
                    <div class="font-weight-bold mt-1">Chat</div>
                    <small>chat</small>
                </v-card>
            </v-col>
            <v-col cols="12">
                <v-card class="pa-3 text-center" color="orange-lighten-5" rounded="lg">
                    <v-icon color="orange" size="28">mdi-map-marker</v-icon>
                    <div class="font-weight-bold mt-1">{{ worker.location }}</div>
                    <small>Address</small>
                </v-card>
            </v-col>
        </v-row>

        <!-- Skills -->
        <v-card class="pa-4 mb-4" outlined rounded="lg">
            <h3 class="font-weight-bold mb-3">
                <v-icon left color="primary">mdi-tools</v-icon> Skills & Specializations
            </h3>
            <v-chip-group column>
                <v-chip v-for="( skill, i ) in  worker.skills " :key="i" color="primary" text-color="white"
                    class="ma-1">
                    <v-icon start small>mdi-hammer</v-icon>{{ skill.name }}
                </v-chip>
            </v-chip-group>
        </v-card>

        <!-- Work History -->
        <v-card class="pa-4 mb-4" outlined rounded="lg">
            <h3 class="font-weight-bold mb-3">
                <v-icon left color="primary">mdi-history</v-icon> Work History
            </h3>

            <v-card v-for="( exp, index ) in  experienceHistory " :key="index" class="mb-3 pa-3 work-card" outlined
                shaped elevation="2">
                <!-- Header -->
                <div class="d-flex justify-space-between font-weight-medium mb-1">
                    <span class="primary--text"><strong>Project Name:</strong> {{ exp.project_name }}</span>
                    <span class="grey--text text--darken-1">{{ exp.start_date }} - {{ exp.end_date }}</span>
                </div>

                <!-- Employer / Company -->
                <div class="font-weight-medium">
                    <strong>Employer Name:</strong> {{ exp.employer_name }}
                </div>

                <!-- Work Type / Description -->
                <p class="mb-1"><strong>Descriptions:</strong> {{ exp.task_description }}</p>

                <!-- Skills -->
                <div>
                    <strong>Skills:</strong>
                    <v-chip-group column>
                        <v-chip v-for="skill in exp.skills" :key="skill" class="ma-1" color="" text-color="white" pill>
                            {{ skill.name }}
                        </v-chip>
                    </v-chip-group>
                </div>

                <!-- Payment & Rating -->
                <div class="d-flex justify-space-between mt-2">
                    <span><strong>Payment:</strong> {{ exp.total_amount }}</span>
                    <span><strong>Rating:</strong> {{ exp.rating }}/5</span>
                </div>
            </v-card>
        </v-card>

        <!-- Reviews -->
        <!-- <v-card class="pa-4" outlined rounded="lg">
            <h3 class="font-weight-bold mb-3">
                <v-icon left color="primary">mdi-star</v-icon> Reviews
            </h3>
            <v-card v-for="(review, index) in worker.reviewsList" :key="index" class="mb-3 pa-3" outlined rounded="lg">
                <div class="d-flex justify-space-between mb-1">
                    <span class="font-weight-medium">{{ review.reviewer }}</span>
                    <div>
                        <v-icon v-for="n in review.stars" :key="n" color="amber" size="18">mdi-star</v-icon>
                    </div>
                </div>
                <p class="mb-1">{{ review.text }}</p>
                <small class="grey--text">{{ review.date }}</small>
            </v-card>
        </v-card> -->
    </v-container>
</template>

<script>
import BackButtonAppBar from "@/components/header/BackButtonAppBar.vue";
import api from "@/services/api.js";
import apiRoutes from "@/services/apiRoutes.js";

export default {
    components: { BackButtonAppBar },
    name: "AppliedWorkerDetail",
    data() {
        return {
            loading: false,
            experienceHistory: [
            ],
            worker: {
                name: "",
                email: "",
                photo: "",
                phone: "",
                skill: "",
                experience: "",
                rate: "",
                availability: "",
                location: "",
                skills: [],

                reviewsList: [
                    // {
                    //     reviewer: "Amit Sharma",
                    //     stars: 5,
                    //     text: "Excellent work quality and punctual. Highly recommended!",
                    //     date: "2 days ago",
                    // },
                    // {
                    //     reviewer: "Sunil Verma",
                    //     stars: 4,
                    //     text: "Good work, but was slightly delayed on one project.",
                    //     date: "1 week ago",
                    // },
                ],
            },
        };
    },
    methods: {
        callWorker(phone) {
            window.location.href = `tel:${phone}`;
        },
        chat() {
            const workerId = this.worker.id;                
            const workerName = this.worker.name;

            if (!workerId || !workerName) {
                alert("Worker details not available for chat.");
                return;
            }

            this.$router.push({
                name: "worker-chat",
                params: { id: workerId },
                query: workerName ? { name: workerName } : {},
            });
        },
        getInitials(name) {
            if (!name) return '';
            const parts = name.split(' ');
            if (parts.length === 1) return parts[0].charAt(0).toUpperCase();
            return (parts[0].charAt(0) + parts[1].charAt(0)).toUpperCase();
        },
        async getWorker() {
            this.loading = true;
            const id = this.$route.params.id;
            try {
                const res = await api.get(apiRoutes.employerWorkerGetWorkerByID + id);
                const data = res.data.worker;
                this.worker = {
                    id: id,
                    name: data.profile?.name || "", // You may need to add 'name' field in API if missing
                    email: data.email || "",
                    photo: data.profile?.profile_image_url,
                    phone: data.phone || "",
                    skill: data.skill?.name || "", // if skill relation exists
                    rating: data.rating || 0, // default if rating exists
                    reviews: data.reviews || 0,
                    location: (data.profile?.district_detail?.district_name || "") +
                        " , " +
                        (data.profile?.state_detail?.state_name || ""),
                    experience: data.profile?.experience || "",
                    rate: data.profile?.rate || "",
                    availability: data.profile?.availability || "",
                    skills: data.profile?.skills || [], // Array of skills
                    // experienceHistory: data.profile?.experienceHistory || [],
                    age: data.profile?.age,
                    gender: data.profile?.gender,
                    email: data.email || "",
                };
                this.loading = false; // stop loading
            } catch (err) {
                alert(
                    "Error !!\n" +
                    Object.values(err.response?.data?.errors || {}).flat().join("\n")
                );
                console.error("Error fetching worker details:", err);
                this.loading = false; // stop loading
            }

        },
        async getWorkerHistory() {
            this.loading = true;
            const id = this.$route.params.id;
            try {
                const res = await api.get(apiRoutes.employerWorkerfetchAllWorkerJobHistory, { params: { worker_id: id } });
                console.log("Success worker history:", res.data);
                const data = res.data.data;
                this.experienceHistory = data;
                this.loading = false; // stop loading
            } catch (err) {
                alert(
                    "Error !!\n" +
                    Object.values(err.response?.data?.errors || {}).flat().join("\n")
                );
                console.error("Error fetching worker details:", err);
                this.loading = false; // stop loading
            }
        }
    },
    mounted() {
        this.getWorker(),
            this.getWorkerHistory()
    }
};
</script>

<style scoped>
.v-card {
    transition: 0.2s ease-in-out;
}

.v-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
}
</style>
