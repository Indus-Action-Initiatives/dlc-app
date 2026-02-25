<template>
    <BackButtonAppBar />
    <v-container class="fill-height d-flex align-center justify-center" fluid>
        <v-row>
            <v-col cols="12" class="text-center mb-4">
                <v-card outlined class="pa-4 mb-4">
                    <h4 class="mb-3">Change Password</h4>
                    <v-text-field class="mt-2" label="Old Password" v-model="form.old_password" />
                    <v-text-field class="mt-2" label="New Password" v-model="form.new_password" />
                    <v-text-field class="mt-2" label="Confirm Password" v-model="form.cnf_password" />
                    <v-btn block color="primary" large @click="submitForm">Save </v-btn>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import BackButtonAppBar from "@/components/header/BackButtonAppBar.vue";
import api from "@/services/api.js";
import apiRoutes from "@/services/apiRoutes.js";
import { useRouter } from 'vue-router'

import { ref } from 'vue'

const router = useRouter()

const form = ref({
    old_password: '',
    new_password: '',
    cnf_password: ''
})

async function submitForm() {

    if (!form.value.old_password) {
        alert('Please enter your old password.');
        return false;
    }
    if (form.value.old_password.length < 6 || form.value.old_password.length > 20) {
        alert('Old password must be between 6 and 20 characters.');
        return false;
    }

    // New password
    if (!form.value.new_password) {
        alert('Please enter your new password.');
        return false;
    }
    if (form.value.new_password.length < 6 || form.value.new_password.length > 20) {
        alert('New password must be between 6 and 20 characters.');
        return false;
    }

    // Confirm password
    if (!form.value.cnf_password) {
        alert('Please confirm your new password.');
        return false;
    }
    if (form.value.cnf_password !== form.value.new_password) {
        alert('Confirm password does not match the new password.');
        return false;
    }

    const employerData = JSON.parse(localStorage.getItem('labour_currentUser'))
    try {
        const res = await api.post(apiRoutes.employerChangePassword, {
            employer_id: employerData.employer.id,
            old_password: form.value.old_password,
            new_password: form.value.new_password,
            new_password_confirmation: form.value.cnf_password,
        });

        //console.log("Success:", res.data)
        alert('Success !!')

        localStorage.removeItem('labour_currentUser')
        localStorage.removeItem('labouchowk_userType')
        router.push('/')

    } catch (err) {
        alert(
            "Error !!\n" + err.response?.data?.message
        )
        console.error("Error login:", err)
    }

}


</script>

<style></style>
