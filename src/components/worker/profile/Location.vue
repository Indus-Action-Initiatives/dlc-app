<template>
    <BackButtonAppBar />
    <v-container class="fill-height d-flex align-center justify-center" fluid>
        <v-row>
            <v-col cols="12">
                <!-- Location Information -->
                <v-card outlined class="pa-6">
                    <!-- <h4 class="mb-4">Current plot_no</h4> -->

                    <!-- plot_no -->
                    <v-text-field label="House Number / Plot Number" v-model="form.plot_no" class="mb-3"></v-text-field>

                    <!-- street_area_village -->
                    <v-text-field label="Area/  Village / शहर - street_area_village" v-model="form.street_area_village"
                        class="mb-3"></v-text-field>
                    <!-- State -->
                    <v-select label="राज्य - State" v-model="form.state" :items="states" item-title="state_name"
                        item-value="lgd_code" @update:modelValue="getDistrict"></v-select>

                    <!-- District -->
                    <v-select label="जिला - District" v-model="form.district" :items="districts" item-value="lgd_code"
                        item-title="district_name" class="mb-3"></v-select>

                    <v-text-field label="Pincode" v-model="form.pincode" item-value="value"></v-text-field>

                    <!-- Map Selector -->

                    <v-btn class="mb-2" color="primary" @click="openMap">Select Location on Map</v-btn>

                    <v-text-field v-model="form.latitude" label="Latitude" readonly outlined dense />

                    <v-text-field v-model="form.longitude" label="Longitude" readonly outlined dense />


                    <v-btn block color="primary" large @click="submitForm">Save </v-btn>
                </v-card>


                <!-- MAP DIALOG -->
                <v-dialog v-model="showMap" max-width="600px">
                    <v-card>
                        <v-card-title>Select Location</v-card-title>

                        <v-card-text>
                            <div id="mapContainer" style="width:100%; height:400px;"></div>
                            <v-btn class="mt-3" small color="primary" @click="useCurrentLocation">Use My
                                Location</v-btn>
                        </v-card-text>

                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="primary" @click="confirmLocation" variant="tonal">Confirm</v-btn>
                            <v-btn color="error" @click="showMap = false" variant="tonal">Cancel</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import BackButtonAppBar from "@/components/header/BackButtonAppBar.vue";
import { useRouter } from 'vue-router'

import { Geolocation } from "@capacitor/geolocation";

import api from "@/services/api.js";
import apiRoutes from "@/services/apiRoutes.js";

const router = useRouter()

const form = ref({
    plot_no: "",
    street_area_village: "",
    district: "",
    state: "",
    pincode: "",
    latitude: "",
    longitude: ""

});

const showMap = ref(false);
const mapObj = ref(null);
const markerObj = ref(null);
const selectedLocation = ref(null);

const states = ref([]);
const districts = ref([]);

// -------------------------------------------------
//map

function openMap() {
    showMap.value = true;
    selectedLocation.value = {
        lat: form.value.latitude,
        lng: form.value.longitude,
    };

    form.value.latitude = selectedLocation.value.lat;
    form.value.longitude = selectedLocation.value.lng;

    setTimeout(() => initMap(), 100);
}

function loadMapplsScript() {
    return new Promise((resolve) => {
        if (window.mappls) return resolve();

        const script = document.createElement("script");
        script.src =
            "https://apis.mappls.com/advancedmaps/api/" + apiRoutes.mapSecretKey + "/map_sdk?v=3.0&layer=vector";
        script.async = true;
        script.onload = resolve;
        document.body.appendChild(script);
    });
}
function getPrimaryColor() {
    return getComputedStyle(document.documentElement)
        .getPropertyValue("--v-primary-base") // Vuetify 3 variable
        .trim() || "#1976D2"; // fallback
}

// Create SVG marker dynamically
function createMarkerSVG(color) {
    return `
        <svg width="32" height="32" viewBox="0 0 24 24" fill="${color}" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C8.1 2 5 5.1 5 9c0 5.2 7 13 7 13s7-7.8 7-13c0-3.9-3.1-7-7-7zm0 9.5c-1.4 0-2.5-1.1-2.5-2.5S10.6 6.5 12 6.5s2.5 1.1 2.5 2.5S13.4 11.5 12 11.5z"/>
        </svg>
        `;
}

// Convert SVG to Base64 for Mappls Marker icon
function createMarkerIcon(color) {
    return "data:image/svg+xml;base64," + btoa(createMarkerSVG(color));
}
async function initMap() {
    await loadMapplsScript();

    // Always reset when opening dialog
    mapObj.value = null;
    markerObj.value = null;

    const lat = form.value.latitude || 28.6139;
    const lng = form.value.longitude || 77.209;

    selectedLocation.value = { lat, lng };

    mapObj.value = new window.mappls.Map("mapContainer", {
        center: { lat, lng },
        zoom: 14,
    });

    // Draw initial marker
    markerObj.value = new window.mappls.Marker({
        map: mapObj.value,
        position: { lat, lng },
        draggable: true,
        icon: createMarkerIcon(getPrimaryColor()),
    });

    markerObj.value.on("dragend", (evt) => {
        ////console.log(evt)
        const lat = evt.target._lngLat.lat || evt.latitude;
        const lng = evt.target._lngLat.lng || evt.longitude;

        selectedLocation.value = { lat, lng };

        form.value.latitude = selectedLocation.value.lat;
        form.value.longitude = selectedLocation.value.lng;
        //console.log("Dragged:", this.selectedLocation);
    });

    mapObj.value.on("click", (e) => {

        //console.log('dd', e.lngLat)
        const lat = e.lngLat.lat;
        const lng = e.lngLat.lng;

        selectedLocation.value = { lat, lng };

        // If marker exists -> move it
        if (markerObj.value) {
            markerObj.value.setPosition({ lat, lng });
        } else {
            // Create new marker
            markerObj.value = new mappls.Marker({
                map: mapObj.value,
                position: { lat, lng },
                draggable: true,
                icon: createMarkerIcon(getPrimaryColor())
            });

            // Add dragend event only once
            markerObj.value.on("dragend", (evt) => {
                ////console.log(evt)
                const lat = evt.target._lngLat.lat || evt.latitude;
                const lng = evt.target._lngLat.lng || evt.longitude;

                selectedLocation.value = { lat, lng };
                //console.log("Dragged:", this.selectedLocation);
            });
        }

        form.value.latitude = selectedLocation.value.lat;
        form.value.longitude = selectedLocation.value.lng;

        //console.log("Clicked:", this.selectedLocation);
    });

    // setTimeout(() => this.mapObj.invalidateSize(), 300);
}

async function useCurrentLocation() {
    try {
        const coords = await Geolocation.getCurrentPosition();
        form.value.latitude = coords.coords.latitude;
        form.value.longitude = coords.coords.longitude;

        const lat = form.value.latitude;
        const lng = form.value.longitude;

        markerObj.value.setPosition({ lat, lng });

        console.log("GPS allowed");
    } catch (e) {
        console.log("GPS not allowed");
    }

}

function confirmLocation() {
    if (!selectedLocation.value || !selectedLocation.value.lat) {
        alert("Please select a location first!");
        return;
    }

    //console.log(this.selectedLocation);
    form.value.latitude = selectedLocation.value.lat;
    form.value.longitude = selectedLocation.value.lng;

    showMap.value = false;
}
// ------------------------------------------------

async function submitForm() {
    if (!form.value.state) {
        alert("Please select a State");
        return;
    }

    if (!form.value.district) {
        alert("Please select a District");
        return;
    }

    if (!form.value.plot_no.trim()) {
        alert("Please enter plot_no");
        return;
    }

    // if (!form.value.street_area_village.trim()) {
    //     alert("Please enter street_area_village");
    //     return;
    // }

    if (!form.value.pincode) {
        alert("Please enter Pincode");
        return;
    }

    // Pincode validation (6 digits, doesn’t start with 0)
    const pinRegex = /^[1-9][0-9]{5}$/;
    if (!pinRegex.test(form.value.pincode)) {
        alert("Please enter a valid 6-digit Pincode");
        return;
    }

    try {
        const workerData = JSON.parse(localStorage.getItem('labour_currentUser'))
        const res = await api.put(apiRoutes.workerUpdate + '/' + workerData.worker.id, {
            plot_no: form.value.plot_no,
            street_area_village: form.value.street_area_village,
            district: form.value.district.toString(),
            state: form.value.state.toString(),
            pin_code: form.value.pincode,
            lat: form.value.latitude.toString(),
            long: form.value.longitude.toString(),
        });

        console.log("Success:", res.data)

        router.push('/worker-dashboard-profile-skill')

    } catch (err) {
        alert(
            "Error !!\n" +
            Object.values(err.response?.data?.errors || {}).flat().join("\n")
        )
        console.error("Error login:", err)
    }

    console.log(form.value);

}

async function getState() {

    try {
        const res = await api.get(apiRoutes.getState);
        //console.log("Success:", res.data);

        states.value = res.data.data;
        form.value.state = "";
        form.value.district = "";

        //console.log(form.value);
        //console.log("Success: states:", states);

    } catch (err) {
        alert(
            "Error !!\n" +
            Object.values(err.response?.data?.errors || {}).flat().join("\n")
        );
        //console.error("Error fetching worker details:", err);
    }

}

async function getDistrict() {
    form.value.district = "";
    if (!form.value.state) return
    try {
        const res = await api.post(apiRoutes.getDistrict, { state_id: form.value.state });
        //console.log("Success:district", res.data);

        districts.value = res.data.data;
        //console.log("Success: states:", districts);

    } catch (err) {
        alert(
            "Error !!\n" +
            Object.values(err.response?.data?.errors || {}).flat().join("\n")
        );
        //console.error("Error fetching worker details:", err);
    }
}

async function getData() {

    try {
        const workerData = JSON.parse(localStorage.getItem('labour_currentUser'))
        const res = await api.get(`${apiRoutes.workerGetWorkerByid}/${workerData.worker.id}`);

        console.log("Success: location -", res.data);

        const data = res.data.worker;
        // Populate form values safely
        form.value = {
            plot_no: data.profile?.plot_no || '',
            street_area_village: data.profile?.street_area_village || '',
            // district: data.profile?.district || '',
            state: parseInt(data.profile?.state) || '',
            pincode: data.profile?.pin_code || '',
            latitude: parseInt(data.profile?.lat) || '',
            longitude: parseInt(data.profile?.long) || '',
        };

        await getDistrict(form.value.state);

        // Now that districts are loaded, set district value
        form.value.district = parseInt(data.profile?.district);

    } catch (err) {
        alert(
            "Error !!\n" +
            Object.values(err.response?.data?.errors || {}).flat().join("\n")
        );
        console.error("Error fetching worker details:", err);
    }

}

onMounted(() => {
    useCurrentLocation();
    getState();
    getData();
});

</script>

<style scoped>
.v-card {
    border-radius: 12px;
}
</style>
