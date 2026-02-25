<template>
    <BackButtonAppBar />

    <v-container>
        <v-card class="pa-4">
            <v-card-title>Worker Experience/Work History</v-card-title>
            <v-card-text>
                <v-form ref="formRef" @submit.prevent="submitForm">

                    <div v-for="(job, index) in workHistory" :key="index" class="mb-4 border p-3 rounded mypadding">
                        <v-row dense>

                            <!-- Employer Dropdown / Add New -->
                            <!-- <v-col cols="12" sm="6">
                                <v-autocomplete v-model="job.employer_id" :items="employers" item-title="name"
                                    item-value="id" label="Employer" clearable prepend-inner-icon="mdi-office-building">
                                    <template v-slot:append-item>
                                        <v-list-item>
                                            <v-list-item-content>
                                                <v-text-field v-model="newEmployerName" label="Add New Employer"
                                                    dense />
                                            </v-list-item-content>
                                            <v-list-item-action>
                                                <v-btn icon @click="addEmployer(job)">
                                                    <v-icon>mdi-plus</v-icon>
                                                </v-btn>
                                            </v-list-item-action>
                                        </v-list-item>
                                    </template>
</v-autocomplete>
</v-col> -->
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="job.employer_name" label="Employer Name"
                                    prepend-inner-icon="mdi-office-building" />
                            </v-col>
                            <!-- Project Name -->
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="job.project_name" label="Project Name"
                                    prepend-inner-icon="mdi-file-document" />
                            </v-col>

                            <!-- Work Place -->
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="job.work_place" label="Work Place"
                                    prepend-inner-icon="mdi-map-marker" />
                            </v-col>

                            <!-- Work Type -->
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="job.work_type" label="Work Type"
                                    prepend-inner-icon="mdi-briefcase" />
                            </v-col>

                            <!-- Skill -->
                            <v-col cols="12" sm="6">
                                <v-select v-model="job.skill_id" :items="skills" item-title="name" item-value="id"
                                    multiple chips label="Skill" clearable prepend-inner-icon="mdi-hammer-wrench" />
                            </v-col>

                            <!-- Task Description -->
                            <v-col cols="12">
                                <v-textarea v-model="job.task_description" label="Task Description"
                                    prepend-inner-icon="mdi-text-box-outline" rows="3" />
                            </v-col>

                            <!-- Start / End Dates -->
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="job.start_date" type="date" label="Start Date"
                                    prepend-inner-icon="mdi-calendar-start" />
                            </v-col>

                            <v-col cols="12" sm="6">
                                <v-text-field v-model="job.end_date" type="date" label="End Date"
                                    prepend-inner-icon="mdi-calendar-end" />
                            </v-col>

                            <!-- Days Worked / Daily Wage / Total Amount -->
                            <v-col cols="12" sm="4">
                                <v-text-field v-model="job.days_worked" type="number" label="Days Worked"
                                    prepend-inner-icon="mdi-calendar-clock" @input="calculateTotal(job)" />
                            </v-col>

                            <v-col cols="12" sm="4">
                                <v-text-field v-model="job.daily_wage" type="number" label="Daily Wage"
                                    prepend-inner-icon="mdi-currency-inr" @input="calculateTotal(job)" />
                            </v-col>

                            <v-col cols="12" sm="4">
                                <v-text-field v-model="job.total_amount" type="number" label="Total Amount"
                                    prepend-inner-icon="mdi-cash-multiple" readonly />
                            </v-col>

                            <!-- Payment Status -->
                            <v-col cols="12" sm="6">
                                <v-select v-model="job.payment_status" :items="['pending', 'paid', 'partial']"
                                    label="Payment Status" prepend-inner-icon="mdi-cash-check"
                                    :rules="[v => !!v || 'Payment Status is required']" required />
                            </v-col>


                            <!-- Remarks -->
                            <v-col cols="12" sm="6">
                                <v-textarea v-model="job.remarks" label="Remarks"
                                    prepend-inner-icon="mdi-comment-text-outline" rows="2" />
                            </v-col>

                            <!-- Rating -->
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="job.rating" type="number" label="Rating (1-5)"
                                    prepend-inner-icon="mdi-star" min="1" max="5" />
                            </v-col>

                            <!-- Remove Button -->
                            <v-col cols="12" class="text-right">
                                <v-btn color="red" text @click="removeJob(index)" v-if="workHistory.length > 1">
                                    <v-icon left>mdi-delete</v-icon> Remove
                                </v-btn>
                            </v-col>
                        </v-row>
                    </div>

                    <v-row class="mt-4" align="center">
                        <!-- Add More Button -->
                        <v-col cols="auto">
                            <v-btn color="primary" text @click="addJob">
                                <v-icon left>mdi-plus</v-icon> Add More
                            </v-btn>
                        </v-col>

                        <!-- Spacer -->
                        <v-col></v-col> <!-- empty column pushes Submit button to the right -->

                        <!-- Submit Button -->
                        <v-col cols="auto">
                            <v-btn type="submit" color="success">
                                <v-icon left>mdi-check</v-icon> Submit Work History
                            </v-btn>
                        </v-col>
                    </v-row>

                </v-form>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api.js";
import apiRoutes from "@/services/apiRoutes.js";
import BackButtonAppBar from "@/components/header/BackButtonAppBar.vue";
import { useRouter } from 'vue-router'

const router = useRouter()

// Reactive array of jobs
const workHistory = ref([
    {
        employer_name: null,
        project_name: "",
        work_place: "",
        work_type: "",
        skill_id: null,
        task_description: "",
        start_date: "",
        end_date: "",
        days_worked: null,
        daily_wage: null,
        total_amount: null,
        payment_status: "",
        remarks: "",
        rating: null
    }
]);

const skills = ref([]);
onMounted(async () => {
    try {
        const res = await api.get(apiRoutes.getAllSkill);
        console.log('skills', res.data)
        if (res.data && res.data.data) {
            skills.value = res.data.data; // expected: [{id, name}]
        }
    } catch (error) {
        console.error("Error fetching skills:", error);
        alert("Failed to load skills.");
    }
});

// Add new job row
function addJob() {
    workHistory.value.push({
        employer_name: null,
        project_name: "",
        work_place: "",
        work_type: "",
        skill_id: [],
        task_description: "",
        start_date: "",
        end_date: "",
        days_worked: null,
        daily_wage: null,
        total_amount: null,
        payment_status: "",
        remarks: "",
        rating: null
    });
}

// Remove job row
function removeJob(index) {
    workHistory.value.splice(index, 1);
}

// Calculate total_amount dynamically
function calculateTotal(job) {
    const days = parseFloat(job.days_worked) || 0;
    const wage = parseFloat(job.daily_wage) || 0;
    job.total_amount = days * wage;
}

// Submit
async function submitForm() {
    //console.log("Submitted Work History:", workHistory.value);

    const workerData = JSON.parse(localStorage.getItem('labour_currentUser'))
    try {
        for (const work of workHistory.value) {
            //console.log(work);
            if (!work.payment_status) {
                return false;
            }
            const payload = { ...work, worker_id: workerData.worker.id };
            await api.post(apiRoutes.workerCreateJobHistory, payload);
        }
        alert("Work history submitted successfully!");
        router.push('/worker-dashboard-profile-education')

        //console.log('All work history entries submitted successfully');
    } catch (error) {
        console.error('Error submitting work history:', error);
    }
}
</script>





<style scoped>
.mypadding {
    padding: 30px;
}
</style>
