<template>
    <v-app>
        <!-- 🔹 App Bar -->
        <v-app-bar color="primary" dark flat app style="position: sticky;" class="pb-2 pt-4 px-2">
            <v-row align="center" class="fill-height" no-gutters>
                <v-col class="d-flex align-center ms-2 mt-2" cols="auto" style="gap: 12px;">
                    <div style="
              width: 32px;
              height: 32px;
              border: 2px solid white;
              border-radius: 50%;
              overflow: hidden;
              display: flex;
              justify-content: center;
              align-items: center;
            ">
                        <img src="../assets/bharat.jpg" alt="India Flag"
                            style="width: 28px; height: 28px; object-fit: cover;" />
                    </div>
                    <div style="line-height: 1;">
                        <div style="font-weight: 700; font-size: 14px;">भारत सरकार</div>
                        <div style="font-size: 12px;">Govt. of India</div>
                    </div>
                </v-col>

                <v-spacer></v-spacer>

                <!-- 🔹 Bhashini Plugin Container (Fixed Position Right) -->
                <v-col cols="auto" class="d-flex justify-end align-center me-4">
                    <!-- <p>Translate</p> -->
                    <div class="bhashini-plugin-container"></div>
                </v-col>
            </v-row>
        </v-app-bar>

        <!-- 🔹 Subheader Section -->
        <v-sheet class="pa-3" color="primary" elevation="0" style="text-align: center;">
            <div style="color: white; font-weight: 700; font-size: 22px;">Digital Labour Chowk</div>
            <div style="color: white; font-weight: 600; font-size: 18px;">डिजिटल लेबर चौक</div>
            <p style="margin: 4px 0 0; font-weight: 400; font-size: 14px;">Ministry of Labour & Employment</p>
        </v-sheet>

        <!-- 🔹 Main Content -->
        <v-main class="py-5">
            <v-container class="text-center py-6" style="max-width: 420px;">
                <v-sheet class="mx-auto mb-3" elevation="0" rounded
                    style="background: #fff0dc; width: 72px; height: 72px; display: flex; justify-content: center; align-items: center;">
                    <v-icon size="36" color="primary" style="stroke-width: 2;">mdi-hard-hat</v-icon>
                </v-sheet>

                <h3 style="font-weight: 700; font-size: 20px; margin-bottom: 4px;">Welcome / स्वागत है</h3>
                <p style="font-weight: 400; font-size: 14px; line-height: 1.4; color: #526170; margin: 0;">
                    Connecting workers and employers digitally<br />
                    श्रमिकों और नियोकताओं को जोड़ें
                </p>
            </v-container>

            <v-container style="max-width: 420px;" class="py-0">
                <v-row dense>
                    <!-- Worker Card -->
                    <v-col cols="12" class="mb-4">
                        <v-card class="d-flex align-center" outlined style="border-color: #f8c5a7; cursor: pointer;"
                            @click="onWorkerClick">
                            <v-sheet color="secondary" rounded="50%" width="48" height="48"
                                class="d-flex align-center justify-center" style="margin-left: 16px;">
                                <v-icon color="surface" size="28">mdi-account-hard-hat</v-icon>
                            </v-sheet>

                            <v-card-text class="pl-4">
                                <h4 style="margin: 0 0 4px; font-weight: 700; font-size: 18px;">I am a Worker</h4>
                                <p style="margin: 0; font-weight: 400; color: #526170; font-size: 14px;">मैं एक श्रमिक
                                    हूँ</p>
                            </v-card-text>

                            <v-spacer></v-spacer>

                            <v-icon color="#e17b3a" size="28"
                                style="margin-right: 16px; font-weight: 700;">mdi-chevron-right</v-icon>
                        </v-card>
                    </v-col>

                    <!-- Employer Card -->
                    <v-col cols="12">
                        <v-card class="d-flex align-center" outlined
                            style="border-color: #b3c7f6; cursor: pointer; box-shadow: 0 0 8px rgba(79, 111, 219, 0.3);"
                            @click="onEmployerClick">
                            <v-sheet color="#e6f0ff" rounded="50%" width="48" height="48"
                                class="d-flex align-center justify-center" style="margin-left: 16px;">
                                <v-icon color="#6495ed" size="28">mdi-domain</v-icon>
                            </v-sheet>

                            <v-card-text class="pl-4">
                                <h4 style="margin: 0 0 4px; font-weight: 700; font-size: 18px;">I am an Employer</h4>
                                <p style="margin: 0; font-weight: 400; color: #526170; font-size: 14px;">मैं नियोकता हूँ
                                </p>
                            </v-card-text>

                            <v-spacer></v-spacer>

                            <v-icon color="#5d7cf6" size="28"
                                style="margin-right: 16px; font-weight: 700;">mdi-chevron-right</v-icon>
                        </v-card>
                    </v-col>

                    <!-- Quick Access -->
                    <v-col cols="12" class="mt-2">
                        <v-card max-width="420" class="pa-4" outlined elevation="1">
                            <v-card-title class="font-weight-bold" style="font-size: 16px; padding-bottom: 12px;">
                                Quick Access / त्वरित पहुँच
                            </v-card-title>

                            <v-row dense>
                                <v-col cols="12" class="pr-2">
                                    <v-btn block color="#d9f0db" text
                                        style="color: #3a7a29; font-weight: 600; font-size: 12px; text-transform: none;"
                                        @click="onEShramClick">
                                        e-Shram Registration
                                    </v-btn>
                                </v-col>

                                <!-- <v-col cols="6" class="pl-2">
                                    <v-btn block color="#f2e6fc" text
                                        style="color: #7233b7; font-weight: 600; font-size: 12px; text-transform: none;"
                                        @click="onBocwClick">
                                        BOCW Card
                                    </v-btn>
                                </v-col> -->
                            </v-row>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
            <ChatBot />
        </v-main>
    </v-app>
</template>

<script setup>
import { onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import ChatBot from "@/components/chatboat/ChatBot.vue";

const router = useRouter()

function onWorkerClick() {
    router.push('/worker')
}

function onEmployerClick() {
    router.push('/employer')
}

function onEShramClick() {
    window.open('https://eshram.gov.in/', '_blank');
}

function onBocwClick() {
    alert('BOCW Card clicked')
}

onMounted(async () => {
    const currentUser = localStorage.getItem('labouchowk_currentUser')
    const userType = localStorage.getItem('labouchowk_userType')

    if (currentUser && userType) {
        if (userType === 'worker') {
            router.replace('/worker-dashboard')
        } else if (userType === 'employer') {
            router.replace('/employer-dashboard')
        }
    }

    await nextTick()

    // 🧩 Load or reload the Bhashini translation script
    const existingScript = document.querySelector('script[src*="bhashini"]')
    if (existingScript) {
        existingScript.remove()
    }

    const script = document.createElement('script')
    script.src = 'https://translation-plugin.bhashini.co.in/v3/website_translation_utility.js'
    script.async = true
    script.setAttribute('language-icon-color', '#ffffff')
    document.body.appendChild(script)

    script.onload = () => {
        console.log('✅ Bhashini Translation Plugin Loaded')
    }
})
</script>

<style scoped>
.v-app-bar,
.v-row,
.v-col {
    overflow: visible !important;
}
</style>
