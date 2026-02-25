<template>
    <v-app>
        <v-app-bar color="primary" dark flat style="position: sticky;" class="py-2 pt-3 px-1">
            <v-row align="center" class="fill-height" no-gutters>
                <v-col class="d-flex align-center ms-2 mt-2" cols="auto" style="gap: 12px;">
                    <div
                        style="width: 32px;height: 32px;border: 2px solid white;border-radius: 50%;overflow: hidden; display: flex;justify-content: center;align-items: center;">
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
            <v-container class="py-12 fill-height d-flex align-center" fluid>
                <v-row>
                    <v-col cols="12">
                        <v-card class="pa-6 py-10" elevation="6">

                            <v-sheet color="primary" rounded="circle" width="72" height="72"
                                class="d-flex align-center justify-center mx-auto mb-4" elevation="4">
                                <v-icon color="white" size="40">mdi-account-plus</v-icon>
                            </v-sheet>

                            <v-card-title class="text-h5 font-weight-bold text-center mb-5">
                                Worker Register
                            </v-card-title>

                            <!-- Step 1 -->
                            <v-form v-if="step === 1" @submit.prevent="sendOtp">

                                <v-text-field v-model="form.name" label="Full Name" variant="filled"
                                    :error="v$.form.name.$error"
                                    :error-messages="v$.form.name.$errors.map(e => e.$message)" />

                                <v-text-field v-model="form.email" label="Email (Optional)" variant="filled"
                                    type="email" :error="v$.form.email.$error"
                                    :error-messages="v$.form.email.$errors.map(e => e.$message)" />

                                <v-text-field v-model="form.phone" label="Phone Number" variant="filled"
                                    :error="v$.form.phone.$error"
                                    :error-messages="v$.form.phone.$errors.map(e => e.$message)" />

                                <v-text-field v-model="form.password" label="Password" type="password" variant="filled"
                                    :error="v$.form.password.$error"
                                    :error-messages="v$.form.password.$errors.map(e => e.$message)" />

                                <!-- Agreement Checkbox -->
                                <v-checkbox v-model="isAgreed" hide-details color="primary">
                                    <template #label>
                                        I agree to the
                                        <span class="text-primary" style="cursor: pointer; text-decoration: underline;"
                                            @click.stop="showTerms = true">
                                            Terms & Conditions
                                        </span>
                                    </template>
                                </v-checkbox>
                                <!-- Terms Modal -->
                                <v-dialog v-model="showTerms" width="600">
                                    <v-card>
                                        <v-card-title class="text-h6">Terms & Conditions</v-card-title>
                                        <v-card-text>
                                            {{ terms }}
                                        </v-card-text>

                                        <v-card-actions>
                                            <v-spacer></v-spacer>
                                            <v-btn color="primary" @click="showTerms = false">Close</v-btn>
                                        </v-card-actions>
                                    </v-card>
                                </v-dialog>
                                <!--  -->

                                <v-btn type="submit" color="primary" block class="mt-4" :loading="loading">Send OTP</v-btn>

                                <div class="text-center mt-3">
                                    <router-link to="/worker-login">Already have an account? Login</router-link>
                                </div>
                            </v-form>

                            <!-- Step 2 -->
                            <v-form v-if="step === 2" @submit.prevent="verifyOtp">
                                <v-text-field v-model="otp" label="Enter OTP" variant="filled" required />
                                <v-btn type="submit" color="primary" block class="mt-4" :loading="loading">Verify OTP</v-btn>

                                <div class="text-center mt-3">
                                    <v-btn text small @click="resendOtp">Resend OTP</v-btn>
                                </div>
                            </v-form>

                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
import useVuelidate from "@vuelidate/core";
import { required, email, numeric, minLength, helpers } from "@vuelidate/validators";

import api from "@/services/api.js";
import apiRoutes from "@/services/apiRoutes.js";
import { otpService } from "@/services/otpService.js";

export default {
    name: "WorkerRegister",

    data() {
        return {
            terms: "",
            isAgreed: false,
            showTerms: false,
            step: 1,
            otp: "",
            loading: false,

            form: {
                name: "",
                email: "",
                phone: "",
                password: "",
            },
        };
    },

    validations() {
        return {
            form: {
                name: { required },

                email: {
                    email: helpers.withMessage("Invalid email", email),
                },

                phone: {
                    required,
                    numeric: helpers.withMessage("Phone must be digits", numeric),
                    minLength: helpers.withMessage("Phone must be 10 digits", minLength(10)),
                },

                password: {
                    required,
                    minLength: helpers.withMessage("Minimum 6 characters", minLength(6)),
                },
            },
        };
    },

    setup() {
        return { v$: useVuelidate() };
    },

    methods: {
        async sendOtp() {
            if (!this.isAgreed) {
                alert("You must agree to the Terms & Conditions before submitting.");
                return;
            }
            const valid = await this.v$.$validate();
            if (!valid) return;

            this.loading = true;
            try {
                await otpService.sendOtp(this.form.phone);
                this.step = 2;
            } catch (err) {
                alert("Failed to send OTP. Please check the mobile number and try again.");
            } finally {
                this.loading = false;
            }
        },

        async verifyOtp() {
            if (!this.otp) { alert("Please enter the OTP."); return; }

            this.loading = true;
            try {
                await otpService.verifyOtp(this.form.phone, this.otp);
            } catch (err) {
                alert("Invalid or expired OTP. Please try again.");
                this.loading = false;
                return;
            }

            try {
                const res = await api.post(apiRoutes.workerRegister, this.form);
                alert("Registration successful!");
                localStorage.setItem("labour_currentUser", JSON.stringify(res.data));
                localStorage.setItem("labouchowk_userType", "worker");
                this.$router.push("/worker-dashboard-profile");
            } catch (err) {
                alert(
                    Object.values(err.response?.data?.errors || {})
                        .flat()
                        .join("\n")
                );
                this.step = 1;
            } finally {
                this.loading = false;
            }
        },

        async resendOtp() {
            this.loading = true;
            try {
                await otpService.sendOtp(this.form.phone);
                alert("OTP resent successfully.");
            } catch (err) {
                alert("Failed to resend OTP. Please try again.");
            } finally {
                this.loading = false;
            }
        },

        goBack() {
            this.$router.back();
        },
        async getConfig() {
            try {

                const res = await api.get(apiRoutes.getAllConfig);
                console.log('config', res.data.data);

                if (res.data && res.data.data) {
                    const configs = res.data.data;

                    this.terms = configs.find(item => item.key === 'worker_agreement')?.value || '';

                }
            } catch (error) {
                //console.error("Failed:", error);
                alert("Failed!!.");
            }

        }
    },
    mounted() {
        this.getConfig()
    }
};
</script>

<style scoped></style>
