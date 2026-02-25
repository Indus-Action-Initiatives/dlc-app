<template>
    <v-app-bar color="primary" dark flat class="pb-2 pt-4 px-2" style="position: sticky; top: 0; z-index: 10;">
        <v-row align="center" class="fill-height" no-gutters>
            <v-col class="d-flex align-center ms-2 mt-2" cols="auto" style="gap: 12px;">
                <div
                    style="width: 32px; height: 32px; border: 2px solid white; border-radius: 50%; overflow: hidden; display: flex; justify-content: center; align-items: center;">
                    <img src="@/assets/bharat.jpg" alt="India Flag"
                        style="width: 28px; height: 28px; object-fit: cover;" />
                </div>
                <div style="line-height: 1;">
                    <div style="font-weight: 700; font-size: 14px;">भारत सरकार</div>
                    <div style="font-size: 12px;">Govt. of India</div>
                </div>
            </v-col>
        </v-row>

        <v-spacer></v-spacer>

        <!-- Logout -->
        <v-btn @click="logout" variant="text" color="white">
            Logout
            <v-icon end>mdi-logout</v-icon>
        </v-btn>

    </v-app-bar>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

function logout() {
    localStorage.removeItem('labour_currentUser')
    localStorage.removeItem('labouchowk_userType')
    router.push('/')
}
</script>

<style scoped></style>
