<template>
    <BackButtonAppBar />

    <v-container class="fill-height d-flex align-center justify-center" fluid>
        <v-row>
            <v-col cols="12" class="text-center mb-4">
                <v-card outlined class="pa-4 mb-4">
                    <h4 class="mb-3">बुनियादी जानकारी - Basic Information</h4>

                    <!-- NAME -->
                    <v-text-field label="नियोक्ता / संगठन का नाम - Name of Employer / Organisation" v-model="form.name"
                        dense outlined :error="v$.name.$error"
                        :error-messages="v$.name.$errors.map(e => 'Name is required')"></v-text-field>

                    <!-- MOBILE -->
                    <v-text-field label="मोबाइल नंबर - Mobile Number" v-model="form.mobile" type="tel" dense outlined
                        :error="v$.mobile.$error" :error-messages="v$.mobile.$errors.map(err =>
                        err.$validator === 'required'
                            ? 'Mobile is required'
                            : 'Mobile must be 10 digits'
                    )
                        "></v-text-field>

                    <!-- EMAIL -->
                    <v-text-field label="ईमेल आईडी (वैकल्पिक) - Email ID (Optional)" v-model="form.email" dense outlined
                        :error="v$.email.$error"
                        :error-messages="v$.email.$errors.map(e => 'Invalid email format')"></v-text-field>

                    <!-- GENDER -->
                    <v-radio-group v-model="form.gender" label="Gender" :error="v$.gender.$error"
                        :error-messages="v$.gender.$errors.map(e => 'Gender is required')">
                        <v-radio label="Male" value="male" />
                        <v-radio label="Female" value="female" />
                        <v-radio label="Other" value="other" />
                    </v-radio-group>

                    <h5>नियोक्ता का प्रकार - Type of Employer</h5>

                    <!-- EMPLOYER TYPE -->
                    <v-select v-model="form.employerType" :items="[
                        { title: 'Individual', value: 'individual' },
                        { title: 'Contractor', value: 'contractor' },
                        { title: 'Company', value: 'company' },
                        { title: 'Agency', value: 'agency' },
                    ]" label="Select Employer Type" variant="outlined" class="my-2" :error="v$.employerType.$error"
                        :error-messages="v$.employerType.$errors.map(e => 'Employer type is required')"></v-select>

                    <!-- IMAGE UPLOAD -->
                    <v-file-input v-model="form.image" accept="image/*" label="Upload Profile Image"
                        prepend-icon="mdi-camera" show-size outlined dense :error="imageError"
                        :error-messages="imageErrorMessage" @change="handleFileChange"></v-file-input>


                    <!-- IMAGE PREVIEW -->
                    <v-avatar size="120" class="my-3">
                        <v-img v-if="form.imageUrl" :src="form.imageUrl" />
                        <v-icon v-else size="64" color="grey">mdi-account</v-icon>
                    </v-avatar>

                    <!-- SAVE BUTTON -->
                    <v-btn block color="primary" large @click="submitForm">
                        Save
                    </v-btn>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>


<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import BackButtonAppBar from "@/components/header/BackButtonAppBar.vue";
import api from "@/services/api.js";
import apiRoutes from "@/services/apiRoutes.js";

/* VUELIDATE */
import useVuelidate from "@vuelidate/core";
import { required, email, minLength, maxLength } from "@vuelidate/validators";

const router = useRouter();

/* FORM MODEL */
const form = ref({
    name: "",
    mobile: "",
    email: "",
    gender: "",
    employerType: "",
    image: "",
    imageUrl: "",
});

// Image validation error states
const imageError = ref(false);
const imageErrorMessage = ref("");



// Handle file change event (validate image)
const handleFileChange = (event) => {
    const file = event.target.files[0];

    if (file) {
        const isValid = validateImage(file);
        if (isValid) {
            // Valid file, update image in form
            form.value.image = file;
            form.value.imageUrl = URL.createObjectURL(file);  // For preview
        } else {
            // Invalid file, clear the image
            form.value.image = null;
            form.value.imageUrl = null;
        }
    }
};

// Validate image file (size and type)
const validateImage = (file) => {
    const allowedTypes = ["image/jpeg", "image/png", "image/gif"];
    const maxSize = 2 * 1024 * 1024; // 2MB max size

    // Check if the file is an allowed image type
    if (!allowedTypes.includes(file.type)) {
        imageError.value = true;
        imageErrorMessage.value = "Only JPEG, PNG, and GIF images are allowed.";
        return false;
    }

    // Check if the file size is within the limit
    if (file.size > maxSize) {
        imageError.value = true;
        imageErrorMessage.value = "Image size must be less than 5MB.";
        return false;
    }

    imageError.value = false;  // Reset error if file is valid
    imageErrorMessage.value = "";
    return true;
};
/* VUELIDATE RULES */
const rules = {
    name: { required },
    mobile: { required, minLength: minLength(10), maxLength: maxLength(10) },
    email: { email },
    gender: { required },
    employerType: { required },
};

const v$ = useVuelidate(rules, form);

/* GET EMPLOYER DETAILS */
const employerData = JSON.parse(localStorage.getItem("labour_currentUser"));

onMounted(async () => {
    try {
        const res = await api.get(
            `${apiRoutes.employerGetById}/${employerData.employer.id}`
        );

        const data = res.data.employer;

        form.value = {
            name: data.profile?.name || "",
            email: data.email || "",
            mobile: data.phone || "",
            employerType: data.profile?.emp_type || "",
            gender: data.profile?.gender || "",
            imageUrl: data.profile?.profile_image_base64 || null,
        };
    } catch (err) {
        alert("Error loading data", err);
    }
});

/* SUBMIT FORM */
async function submitForm() {
    const isValid = await v$.value.$validate();


    if (!isValid || imageError.value) {
        alert("Please fix the highlighted errors.");
        return;
    }

    let base64Image = null;

    if (form.value.image) {
        base64Image = await toBase64(form.value.image);
    }

    try {
        await api.put(
            apiRoutes.employerUpdate + "/" + employerData.employer.id,
            {
                email: form.value.email,
                phone: form.value.mobile,
                name: form.value.name,
                emp_type: form.value.employerType,
                gender: form.value.gender,
                profile_image: base64Image,
            }
        );

        router.push("/employer-dashboard-profile-identity");
    } catch (err) {
        alert("Update failed!", err);
    }
}

/* Convert File to Base64 */
const toBase64 = (file) => {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = (err) => reject(err);
    });
};
</script>

<style></style>
