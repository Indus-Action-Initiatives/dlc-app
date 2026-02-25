<template>
    <v-app-bar color="primary" dark flat style="position: sticky;" class="pb-2 pt-4 px-2">
        <v-row align="center" class="fill-height" no-gutters>
            <v-col class="d-flex align-center ms-2 mt-2" cols="auto" style="gap: 12px;">
                <div style="width: 32px;height: 32px;border: 2px solid white;border-radius: 50%;overflow: hidden;
                        display: flex;justify-content: center;align-items: center;">
                    <img src="@/assets/bharat.jpg" alt="India Flag"
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
    <v-container class="fill-height d-flex align-center justify-center" fluid>
        <v-row>
            <v-col cols="12" class="text-center mb-4">
                <v-card outlined class="pa-4 mb-4">
                    <h4 class="mb-3">बुनियादी जानकारी - Basic Information</h4>

                    <v-text-field label="Full Name" v-model="form.name" />
                    <v-text-field label="Age in Years" type="number" v-model="form.age" />
                    <v-radio-group v-model="form.gender" label="Gender">
                        <v-radio label="Male" value="male" />
                        <v-radio label="Female" value="female" />
                        <v-radio label="Other" value="other" />
                    </v-radio-group>
                    <v-text-field label="Mobile Number" v-model="form.mobile" />
                    <v-text-field label="Email (optional)" v-model="form.email" />


                    <!-- User Image Upload -->
                    <!-- Image Upload Direct -->
                    <v-file-input v-model="form.image" accept="image/*" label="Upload Profile Image"
                        prepend-icon="mdi-camera" show-size @change="handleFileChange" outlined dense></v-file-input>

                    <!-- Preview -->
                    <v-avatar size="120" class="my-3">
                        <v-img v-if="form.imageUrl" :src="form.imageUrl" />
                        <v-icon v-else size="64" color="grey">mdi-account</v-icon>
                    </v-avatar>

                    <v-btn block color="primary" large @click="submitForm">Save </v-btn>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import BackButtonAppBar from "@/components/header/BackButtonAppBar.vue";
import api from "@/services/api.js";
import apiRoutes from "@/services/apiRoutes.js";

const router = useRouter()

const form = ref({
    name: '',
    age: '',
    gender: '',
    mobile: '',
    email: '',
    image: '',
    imageUrl: '',
})

const workerData = JSON.parse(localStorage.getItem('labour_currentUser'))

onMounted(async () => {
    try {
        const res = await api.get(`${apiRoutes.workerGetWorkerByid}/${workerData.worker.id}`);
        console.log("Success:", res.data);

        const data = res.data.worker;
        // Populate form values safely
        form.value = {
            name: data.profile?.name || '',         // use profile.name if exists
            email: data.email || '',                // email directly from data
            mobile: data.phone || '',               // phone directly from data
            age: data.profile?.age || '',           // profile.age if exists
            gender: data.profile?.gender || '',     // profile.gender if exists
            imageUrl: data.profile?.profile_image_url || null,     // profile.image if exists
        };

    } catch (err) {
        console.error("Error fetching worker details:", err);
        alert(
            "Error !!\n" +
            Object.values(err.response?.data?.errors || {}).flat().join("\n")
        );
        console.error("Error fetching worker details:", err);
    }
});



async function submitForm() {
    if (!validateForm()) {
        // Validation failed, stop execution
        return;
    }
    let base64Image = null;

    if (form.value.image) {
        base64Image = await toBase64(form.value.image);
    }

    try {
        const res = await api.put(apiRoutes.workerUpdate + '/' + workerData.worker.id, {
            email: form.value.email,
            phone: form.value.mobile,
            name: form.value.name,
            age: form.value.age,
            gender: form.value.gender,
            profile_image: base64Image,
        });

        console.log("Success:", res.data)

        router.push('/worker-dashboard-profile-identity')

    } catch (err) {
        alert(
            "Error !!\n" +
            Object.values(err.response?.data?.errors || {}).flat().join("\n")
        )
        console.error("Error login:", err)
    }
}

// Convert File to Base64
const toBase64 = (file) => {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file); // converts to base64 string
        reader.onload = () => resolve(reader.result);
        reader.onerror = (error) => reject(error);
    });
};

function goBack() {
    router.push('/worker-dashboard-profile')
}

function validateForm() {
    const { name, age, gender, mobile } = form.value;

    if (!name) {
        alert('Please fill in Name.');
        return false;
    }
    if (!mobile) {
        alert('Please fill in Mobile.');
        return false;
    }
    if (!age) {
        alert('Please fill in Age.');
        return false;
    }
    if (!gender) {
        alert('Please fill in Gender.');
        return false;
    }

    // All fields valid
    return true;
}

</script>

<style></style>
