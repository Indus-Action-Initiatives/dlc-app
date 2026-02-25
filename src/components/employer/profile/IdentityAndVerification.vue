<template>
    <BackButtonAppBar />
    <v-container class="fill-height d-flex align-center justify-center" fluid>
        <v-row>
            <v-col cols="12" class="text-center mb-4">
                <!-- Identity & Verification -->
                <v-card outlined class="pa-4 mb-4">
                    <h4 class="mb-3">पहचान और सत्यापन - Identity & Verification</h4>
                    <v-alert type="info" variant="tonal" color="orange">
                        सत्यापन लंबित - Verification Pending
                    </v-alert>

                    <v-select label="दस्तावेज़ प्रकार - Document Type" v-model="form.docType" :items="docTypes"
                        item-title="title" item-value="value" color="primary" required></v-select>

                    <v-text-field label="दस्तावेज़ नंबर - Document Number" v-model="form.docNumber" dense
                        outlined></v-text-field>

                    <v-file-input v-model="form.pdfFile" label="दस्तावेज़ अपलोड करें - Upload Document"
                        prepend-icon="mdi-upload" dense outlined accept=".pdf"
                        :rules="[fileSizeRule, fileTypeRule]"></v-file-input>

                    <v-avatar size="120" class="my-3">
                        <!-- PDF Icon -->
                        <v-btn v-if="pdfUrl" icon color="red" @click="openPdf(pdfUrl)"
                            style="width: 120px; height: 120px; overflow: hidden;">
                            <embed :src="pdfUrl" type="application/pdf"
                                style="width: 100%; height: 100%; object-fit: cover; pointer-events: none;" />
                        </v-btn>

                        <v-icon v-else size="64" color="grey">
                            mdi-file-pdf-box
                        </v-icon>
                    </v-avatar>

                    <v-text-field label="श्रम लाइसेंस (वैकल्पिक) - Labour Licence (Optional)"
                        v-model="form.labourLicence" dense outlined></v-text-field>

                    <v-btn block color="primary" large @click="submitForm">Save </v-btn>
                </v-card>
                <!-- PDF Viewer Modal -->
                <v-dialog v-model="pdfDialog" width="800">
                    <v-card>
                        <v-card-title class="headline d-flex justify-space-between">View Document
                            <v-btn icon @click="pdfDialog = false">
                                <v-icon>mdi-close</v-icon>
                            </v-btn>
                        </v-card-title>

                        <v-card-text>
                            <iframe v-if="selectedPdf" :src="selectedPdf" width="100%" height="600px"></iframe>
                        </v-card-text>
                    </v-card>
                </v-dialog>
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
    docType: '',
    docNumber: '',
    labourLicence: '',
    pdfFile: null,
    // pdf_url: "",
})

const pdfUrl = ref(null);          // URL returned from API
const pdfDialog = ref(false);      // Modal state
const selectedPdf = ref(null);     // PDF to view

// -------- OPEN PDF IN MODAL --------
const openPdf = (url) => {
    selectedPdf.value = url;
    pdfDialog.value = true;
};



const docTypes = ref([
    { title: 'PAN Card', value: 'pancard' },
    { title: 'Driving License', value: 'driving_license' },
    { title: 'Voter ID', value: 'voterid' }
])

function viewCertificate(file) {
    if (!file) {
        alert("No File.");
        return;
    }

    showModal.value = true;
}

const employerData = JSON.parse(localStorage.getItem('labour_currentUser'))

onMounted(async () => {
    try {
        const res = await api.get(`${apiRoutes.employerGetById}/${employerData.employer.id}`);
        console.log("Success:", res.data);

        const data = res.data.employer;
        // Populate form values safely
        form.value = {
            docType: data.profile?.docType || '',
            labourLicence: data.profile?.eshram || '',
            docNumber: data.profile?.docNumber || '',
            // pdf_url: data.profile?.profile_doc_base64 || '',
        };
        pdfUrl.value = data.profile?.profile_doc_base64 || '';

    } catch (err) {
        alert(
            "Error !!\n" +
            Object.values(err.response?.data?.errors || {}).flat().join("\n")
        );
        // console.error("Error fetching worker details:", err);
    }
});


// -------------------------------------------
const MAX_SIZE_MB = 2; // 2 MB

// Rule to check file type
const fileTypeRule = (file) => {
    if (!file) return true; // no file selected yet
    return file.type === "application/pdf" || "Only PDF files are allowed.";
};

// Rule to check file size
const fileSizeRule = (file) => {
    if (!file) return true;
    return file.size / 1024 / 1024 <= MAX_SIZE_MB || `File must be smaller than ${MAX_SIZE_MB} MB.`;
};

// Convert PDF to Base64


async function submitForm() {
    if (!form.value.docType) {
        alert('Please select a Document Type.');
        return false;
    }

    if (!form.value.docNumber) {
        alert('Please enter the Document Number.');
        return false;
    }

    // if (!labourLicence) {
    //     alert('Please enter the Eshram no.');
    //     return false;
    // }

    let base64Pdf = null;

    if (form.value.pdfFile) {
        base64Pdf = await toBase64(form.value.pdfFile);
    }

    try {
        const res = await api.put(apiRoutes.employerUpdate + '/' + employerData.employer.id, {

            docType: form.value.docType,
            docNumber: form.value.docNumber,
            eshram: form.value.labourLicence,
            pdf: base64Pdf,
        });

        console.log("Success:", res.data)

        router.push('/employer-dashboard-profile-location')

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


</script>

<style></style>
