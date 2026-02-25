<template>
    <BackButtonAppBar />
    <v-container class="fill-height d-flex align-center justify-center" fluid>
        <v-row>
            <v-col cols="12">
                <!-- Work Details -->
                <v-card outlined class="pa-6">
                    <h4 class="mb-4">कार्य विवरण - Work Details</h4>

                    <!-- Work Category -->
                    <v-select label="कार्य श्रेणी - Typical Work Category" v-model="form.workCategory" multiple chips
                        :items="workCategories" item-title="name" item-value="id" class="mb-4"></v-select>

                    <!-- Average Workers per Month -->
                    <v-select label="प्रति माह औसत श्रमिक - Average Workers Hired per Month" v-model="form.workerRange"
                        :items="workerRanges" item-title="title" item-value="value"></v-select>

                    <v-btn block color="primary" large @click="submitForm">Save </v-btn>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import BackButtonAppBar from "@/components/header/BackButtonAppBar.vue";
import api from "@/services/api.js";
import apiRoutes from "@/services/apiRoutes.js";
import { useRouter } from 'vue-router'


const form = ref({
    workCategory: [],
    workerRange: "",
});


const router = useRouter()
const workCategories = ref([
    // { title: "Construction", value: "construction" },
    // { title: "Manufacturing", value: "manufacturing" },
    // { title: "Service", value: "service" },
    // { title: "Agriculture", value: "agriculture" },
]);

const workerRanges = ref([
    { title: "1-10", value: "1-10" },
    { title: "11-50", value: "11-50" },
    { title: "51-100", value: "51-100" },
    { title: "100+", value: "100+" },
]);

async function submitForm() {
    //console.log(form.value);

    try {
        const employerData = JSON.parse(localStorage.getItem("labour_currentUser"));

        const res = await api.put(apiRoutes.employerUpdate + '/' + employerData.employer.id, {
            skills: form.value.workCategory,
            avg_worker: form.value.workerRange,

        });

        console.log("Success:", res.data)
        router.push('/employer-dashboard-home')

    } catch (err) {
        alert(
            "Error !!\n" +
            Object.values(err.response?.data?.errors || {}).flat().join("\n")
        )
        console.error("Error login:", err)
    }
}

async function getSkills() {
    try {
        const res = await api.get(apiRoutes.getAllSkill);
        console.log('skills', res.data)
        if (res.data && res.data.data) {
            workCategories.value = res.data.data; // expected: [{id, name}]
        }
    } catch (error) {
        console.error("Error fetching skills:", error);
        alert("Failed to load skills.");
    }
}


async function getData() {

    try {
        const employerData = JSON.parse(localStorage.getItem("labour_currentUser"));
        const res = await api.get(`${apiRoutes.employerGetById}/${employerData.employer.id}`);

        // console.log("Success: location -", res.data);

        const data = res.data.employer;
        // Populate form values safely
        form.value = {
            workCategory: data.skills.map(s => s.id),
            workerRange: data.profile?.avg_worker || ''
        };


    } catch (err) {
        alert(
            "Error !!\n" +
            Object.values(err.response?.data?.errors || {}).flat().join("\n")
        );
        console.error("Error fetching worker details:", err);
    }

}

onMounted(() => {
    getSkills(),
        getData()
})
</script>

<style scoped>
.v-card {
    border-radius: 12px;
}
</style>
