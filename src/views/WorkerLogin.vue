<template>
    <v-app>
        <v-app-bar color="primary" dark flat style="position: sticky;" class="py-2 pt-3 px-1">
            <v-row align="center" class="fill-height" no-gutters>
                <v-col class="d-flex align-center ms-2 mt-2" cols="auto" style="gap: 12px;">
                    <div style="width: 32px;height: 32px;border: 2px solid white;border-radius: 50%;overflow: hidden;
                        display: flex;justify-content: center;align-items: center;">
                        <img src="../assets/bharat.jpg" alt="India Flag"
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

        <v-main>
            <v-container class="py-12 fill-height d-flex align-center justify-content" fluid>
                <v-row>
                    <v-col cols="12">
                        <v-card class="pa-6 py-10" elevation="6">

                            <v-sheet color="primary" rounded="circle" width="64" height="64"
                                class="d-flex align-center justify-center mx-auto mb-4">
                                <v-icon color="white" size="36">mdi-account-hard-hat</v-icon>
                            </v-sheet>

                            <v-card-title class="text-h6 font-weight-bold text-center mb-5">
                                Worker Login
                            </v-card-title>

                            <!-- Step 1: Login Form (keep form as-is) -->
                            <v-form v-if="step === 1" @submit.prevent="submitLoginForm">

                                <!-- Mobile -->
                                <v-text-field v-model="mobile" label="Mobile Number" variant="filled"
                                    :error="v$.mobile.$error" :error-messages="mobileErrors" />

                                <!-- Password -->
                                <v-text-field v-model="password" label="Password" variant="filled" type="password"
                                    :error="v$.password.$error" :error-messages="passwordErrors" />

                                <v-btn type="submit" color="primary" block class="mt-4" :loading="loading">
                                    Login
                                </v-btn>

                                <router-link to="/worker-forgot-password">
                                    Forgot Password
                                </router-link>

                                <div class="text-center mt-3">
                                    <router-link to="/worker-register">
                                        Worker Register
                                    </router-link>
                                </div>

                            </v-form>

                            <!-- Step 2: OTP Verification -->
                            <v-form v-if="step === 2" @submit.prevent="verifyOtpAndLogin">
                                <p class="text-center mb-4">
                                    An OTP has been sent to <strong>{{ mobile }}</strong>
                                </p>
                                <v-text-field v-model="otp" label="Enter OTP" variant="filled"
                                    :error="otpError !== ''" :error-messages="otpError ? [otpError] : []" />
                                <v-btn type="submit" color="primary" block class="mt-4" :loading="loading">
                                    Verify &amp; Login
                                </v-btn>
                                <div class="text-center mt-3">
                                    <v-btn variant="text" size="small" @click="resendOtp" :loading="loading">
                                        Resend OTP
                                    </v-btn>
                                </div>
                            </v-form>

                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import useVuelidate from '@vuelidate/core'
import { required, minLength, helpers, numeric } from '@vuelidate/validators'
import api from "@/services/api.js"
import apiRoutes from "@/services/apiRoutes.js"
import { otpService } from "@/services/otpService.js"
import { registerPush } from "@/services/pushNotifications.js"

const router = useRouter()

// --------------------------
// Form fields
// --------------------------
const step = ref(1)
const mobile = ref('')
const password = ref('')
const otp = ref('')
const otpError = ref('')
const loading = ref(false)

// --------------------------
// Validation Rules
// --------------------------
const rules = {
    mobile: {
        required,
        numeric,
        minLength: minLength(10),
        mobileFormat: helpers.withMessage(
            "Mobile must be 10 digits",
            value => /^[6-9]\d{9}$/.test(value)
        )
    },
    password: {
        required,
        minLength: minLength(6)
    }
}

const v$ = useVuelidate(rules, { mobile, password })

// --------------------------
// Error Message Computed
// --------------------------
const mobileErrors = computed(() =>
    v$.value.mobile.$errors.map(e => e.$message)
)

const passwordErrors = computed(() =>
    v$.value.password.$errors.map(e => {
        if (e.$validator === "required") return "Password is required"
        if (e.$validator === "minLength") return "Minimum 6 characters required"
        return e.$message
    })
)

// --------------------------
// Step 1 – validate form then send OTP
// --------------------------
const submitLoginForm = async () => {
    v$.value.$touch()
    if (v$.value.$invalid) return

    loading.value = true
    try {
        await otpService.sendOtp(mobile.value)
        otp.value = ''
        otpError.value = ''
        step.value = 2
    } catch (err) {
        alert("Failed to send OTP. Please check the mobile number and try again.")
    } finally {
        loading.value = false
    }
}

// --------------------------
// Step 2 – verify OTP then call login API
// --------------------------
const verifyOtpAndLogin = async () => {
    otpError.value = ''
    if (!otp.value || otp.value.length < 6) {
        otpError.value = 'Please enter the 6-digit OTP.'
        return
    }

    loading.value = true
    try {
        await otpService.verifyOtp(mobile.value, otp.value)
    } catch (err) {
        otpError.value = 'Invalid or expired OTP. Please try again.'
        loading.value = false
        return
    }

    try {
        const res = await api.post(apiRoutes.workerLogin, {
            phone: mobile.value,
            password: password.value
        })
        localStorage.setItem('labour_currentUser', JSON.stringify(res.data))
        localStorage.setItem('labouchowk_userType', 'worker')
        await registerPush('worker', String(res.data.worker.id))
        alert("Login successful!")
        router.push('/worker-dashboard-home')
    } catch (err) {
        console.error("Error in login:", err)
        alert(
            "Login Failed!!\n" +
            Object.values(err.response?.data?.errors || {}).flat().join("\n")
        )
        step.value = 1
    } finally {
        loading.value = false
    }
}

// --------------------------
// Resend OTP
// --------------------------
const resendOtp = async () => {
    loading.value = true
    try {
        await otpService.sendOtp(mobile.value)
        alert("OTP resent successfully.")
    } catch (err) {
        alert("Failed to resend OTP. Please try again.")
    } finally {
        loading.value = false
    }
}

// --------------------------
function goBack() {
    if (step.value === 2) { step.value = 1; return }
    router.back()
}
</script>
