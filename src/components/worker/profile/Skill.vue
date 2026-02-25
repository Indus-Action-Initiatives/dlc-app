<template>
    <BackButtonAppBar />
    <v-container class="fill-height d-flex align-center justify-center" fluid>
        <v-row>
            <v-col cols="12">
                <!-- skill -->
                <v-form v-model="valid" ref="skillForm" class="mb-4">
                    <v-card outlined class="py-4">
                        <v-card-title class="font-weight-bold">
                            कौशल सूचना * Skill Information
                        </v-card-title>
                        <v-card-text>
                            <!-- Skill Selection -->
                            <div class="mb-2">Select Skills:</div>
                            <v-select v-model="form.skill_id" :items="skills" item-title="name" item-value="id" multiple
                                chips label="Select Skill" />

                            <!-- Experience Input -->
                            <v-text-field label="Experience (Years)" v-model="form.experience" type="number"
                                :rules="[v => !!v || 'Experience is required']" class="mt-3" />

                            <!-- Preferred Work Type -->
                            <v-select label="Preferred Work Type" v-model="form.workType"
                                :items="['Daily Wage', 'Contract']" :rules="[v => !!v || 'Please select work type']"
                                class="mt-3" />

                            <!-- Experience Input -->
                            <v-text-field label="Daily Rate (Rs)" v-model="form.rate" type="number"
                                :rules="[v => !!v || 'Rate ie required !!']" class="mt-3" />

                            <!-- Experience Input -->
                            <!-- Preferred Work Type -->
                            <v-select label="Select Availability for work " v-model="form.availability"
                                :items="['Yes', 'No']" :rules="[v => !!v || 'Please select work type']" class="mt-3" />



                        </v-card-text>



                        <v-btn class="mx-3" block color="primary" large @click="submitForm">Submit</v-btn>

                    </v-card>
                </v-form>


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
    data() {
        return {
            valid: false,
            skills: [],
            form: {
                skill_id: [],
                experience: "",
                rate: "",
                availability: [],
                workType: [],
            },
        };
    },
    methods: {
        async getSkills() {
            try {
                this.loading = true;
                const res = await api.get(apiRoutes.getAllSkill);
                console.log('skills', res.data)
                if (res.data && res.data.data) {
                    this.skills = res.data.data; // expected: [{id, name}]
                }
            } catch (error) {
                console.error("Error fetching skills:", error);
                alert("Failed to load skills.");
            }
        },
        async getWoker() {
            try {
                const workerData = JSON.parse(localStorage.getItem('labour_currentUser'))
                const res = await api.get(`${apiRoutes.workerGetWorkerByid}/${workerData.worker.id}`);
                console.log("Success:", res.data);

                const data = res.data.worker;
                // Populate form values safely
                this.form = {
                    experience: data.profile?.experience || '',         // use profile.name if exists
                    workType: data.profile?.work_type || '',                // email directly from data
                    skill_id: data.profile?.skill_id || '',               // phone directly from data
                    availability: data.profile?.availability || '',
                    rate: data.profile?.rate || '',
                };

            } catch (err) {
                alert(
                    "Error !!\n" +
                    Object.values(err.response?.data?.errors || {}).flat().join("\n")
                );
                console.error("Error fetching worker details:", err);
            }
        },
        async submitForm() {
            if (this.$refs.skillForm.validate()) {
                //console.log("Form Submitted:", this.form);
                try {
                    const workerData = JSON.parse(localStorage.getItem('labour_currentUser'))
                    const res = await api.put(apiRoutes.workerUpdate + '/' + workerData.worker.id, {
                        skill_id: this.form.skill_id,
                        experience: this.form.experience,
                        work_type: this.form.workType,
                        availability: this.form.availability,
                        rate: this.form.rate,
                    });

                    //console.log("Success:", res.data)
                    alert("Form submitted successfully!");
                    this.$router.push('/worker-dashboard-profile-workhistory')

                } catch (err) {
                    alert(
                        "Error !!\n" +
                        Object.values(err.response?.data?.errors || {}).flat().join("\n")
                    )
                    console.error("Error login:", err)
                }

            }
        },
    },
    mounted() {
        this.getSkills();
        this.getWoker();
    }
};
</script>


<style></style>
