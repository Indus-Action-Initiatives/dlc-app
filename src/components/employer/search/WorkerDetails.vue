<template>
    <BackButtonAppBar />

    <v-container class="py-6">
        <v-card v-if="worker" class="mx-auto pa-5 rounded-xl shadow-lg" max-width="750" color="orange-lighten-5">
            <!-- Header Section -->
            <v-row align="center">
                <v-col cols="auto">
                    <v-avatar size="100" class="shadow-md border">
                        <img src="" alt="Worker" /> ,
                    </v-avatar>
                </v-col>

                <v-col>
                    <h2 class="font-weight-bold mb-1 text-h5">{{ worker.name }}</h2>
                    <p class="mb-0 text-subtitle-1 text-grey-darken-2">
                        🛠️ {{ worker.skill }}
                    </p>
                    <p class="mb-0 text-subtitle-2 text-grey-darken-1">
                        <v-icon size="18" color="orange">mdi-map-marker</v-icon>
                        {{ worker.location }} ({{ worker.pincode }})
                    </p>
                </v-col>

                <v-col cols="auto">
                    <v-chip :color="worker.availability === 'Available' ? 'green' : 'red'"
                        class="text-white font-weight-bold">
                        {{ worker.availability }}
                    </v-chip>
                </v-col>
            </v-row>

            <v-divider class="my-4"></v-divider>

            <!-- About Worker -->
            <section>
                <h3 class="mb-2 text-h6 font-weight-medium">👷 About Worker</h3>
                <v-row>
                    <v-col cols="6" sm="4">
                        <p class="mb-1"><strong>Age:</strong> {{ worker.age }} yrs</p>
                    </v-col>
                    <v-col cols="6" sm="4">
                        <p class="mb-1">
                            <strong>Experience:</strong> {{ worker.experience }} years
                        </p>
                    </v-col>
                    <v-col cols="6" sm="4">
                        <p class="mb-1">
                            <strong>Projects Done:</strong> {{ worker.projectsDone }}
                        </p>
                    </v-col>
                    <v-col cols="6" sm="4">
                        <p class="mb-1"><strong>Expected Wage:</strong> {{ worker.wage }}</p>
                    </v-col>
                </v-row>
            </section>

            <v-divider class="my-4"></v-divider>

            <!-- Facilities -->
            <section v-if="worker.facilities.length">
                <h3 class="mb-2 text-h6 font-weight-medium">🏕️ Facilities Available</h3>
                <v-row>
                    <v-col v-for="(facility, index) in worker.facilities" :key="index" cols="auto">
                        <v-chip color="orange lighten-4" class="ma-1" label variant="outlined">
                            <v-icon left color="orange">mdi-check-circle</v-icon>
                            {{ facility }}
                        </v-chip>
                    </v-col>
                </v-row>
            </section>

            <v-divider class="my-4"></v-divider>

            <!-- Contact Section -->
            <section>
                <h3 class="mb-2 text-h6 font-weight-medium">📞 Contact Information</h3>
                <v-row>
                    <v-col cols="12" sm="6">
                        <p class="mb-1">
                            <v-icon left color="orange">mdi-phone</v-icon>
                            <a :href="'tel:' + worker.contact" class="text-decoration-none">
                                {{ worker.contact }}
                            </a>
                        </p>
                    </v-col>
                    <v-col cols="12" sm="6">
                        <p class="mb-1">
                            <v-icon left color="orange">mdi-map-marker</v-icon>
                            {{ worker.location }}
                        </p>
                    </v-col>
                </v-row>
            </section>

            <!-- Action Buttons -->
            <div class="mt-5 text-center">
                <v-btn color="green" size="large" class="mr-3" elevation="2" @click="callWorker">
                    <v-icon left>mdi-phone</v-icon>
                    Call Worker
                </v-btn>
                <v-btn color="orange" size="large" elevation="2" @click="hireWorker">
                    <v-icon left>mdi-briefcase-check</v-icon>
                    Hire Now
                </v-btn>
            </div>
        </v-card>

        <v-alert v-else type="error" color="red" class="mt-6">
            Worker not found.
        </v-alert>
    </v-container>
</template>

<script>
import BackButtonAppBar from "@/components/header/BackButtonAppBar.vue";

export default {
    name: "WorkerDetailPage",
    components: { BackButtonAppBar },
    data() {
        return {
            worker: null,
            demoWorkers: [],
        };
    },
    mounted() {
        this.demoWorkers = Array.from({ length: 50 }, (_, i) => ({
            id: i + 1,
            name: `Worker ${i + 1}`,
            contact: "+91 98765 43" + (100 + i),
            age: 20 + Math.floor(Math.random() * 20),
            experience: Math.floor(Math.random() * 10 + 1),
            projectsDone: Math.floor(Math.random() * 30 + 5),
            location: ["Delhi", "Mumbai", "Bangalore", "Chennai", "Pune"][i % 5],
            pincode: ["110001", "400001", "560001", "600001", "411001"][i % 5],
            skill: [
                "Masonry",
                "Carpentry",
                "Plumbing",
                "Electrician",
                "Painter",
                "Welder",
                "Helper",
                "Tile Setter",
            ][i % 8],
            wage: `₹ 25000/month`,
            availability: Math.random() > 0.5 ? "Available" : "Occupied",
            facilities: ["Accommodation", "Meal", "Transport"].filter(
                () => Math.random() > 0.5
            ),
            photo: `https://randomuser.me/api/portraits/men/${i + 20}.jpg`,
        }));

        const workerId = Number(this.$route.params.id);
        this.worker = this.demoWorkers.find((w) => w.id === workerId) || null;
    },
    methods: {
        callWorker() {
            if (this.worker?.contact) {
                window.location.href = `tel:${this.worker.contact}`;
            }
        },
        hireWorker() {
            alert(`🎉 You have hired ${this.worker.name} successfully!`);
        },
    },
};
</script>

<style scoped>
.v-card {
    background: linear-gradient(180deg, #fff8f0, #fff);
    border: 1px solid #f9d29d;
    box-shadow: 0 6px 14px rgba(255, 165, 0, 0.2);
}

h3 {
    color: #e65100;
}

a {
    color: #e65100;
}

.v-avatar img {
    border-radius: 50%;
}

.v-chip {
    font-weight: 500;
}

.v-btn {
    font-weight: bold;
    text-transform: none;
}
</style>
