<!-- ---------------------------------- -->
<template>
    <LogoutAppBar />

    <v-container class="pa-4">
        <!-- Title -->
        <h2 class="text-center mb-4">Find Work/Jobs</h2>

        <!-- Filters -->
        <v-row dense>
            <v-col cols="6">
                <v-text-field v-model="filters.location" label="Location" prepend-inner-icon="mdi-map-marker"
                    variant="outlined" density="compact" hide-details />
            </v-col>

            <v-col cols="6">
                <v-text-field v-model="filters.pincode" label="Pincode" prepend-inner-icon="mdi-map-marker"
                    variant="outlined" density="compact" hide-details />
            </v-col>

            <v-col cols="6">
                <v-select v-model="filters.skill" :items="skills" label="Select Skill" item-title="name" item-value="id"
                    prepend-inner-icon="mdi-briefcase" variant="outlined" density="compact" hide-details clearable />
            </v-col>
            <v-col cols="6">
                <v-select v-model="filters.range" :items="ranges" item-title="name" item-value="id" label="Range (km)"
                    prepend-inner-icon="mdi-ruler" variant="outlined" density="compact" clearable />
            </v-col>
        </v-row>

        <!-- Range Filter -->
        <v-row class="align-center mb-3 mt-2">
            <v-col cols="8" class="text-right">
                <v-btn color="primary" prepend-icon="mdi-magnify" class="w-100" @click="filterjobs">
                    Search
                </v-btn>
            </v-col>
            <v-col cols="4" class="text-right">
                <!-- Toggle View -->
                <v-btn variant="tonal" color="primary" @click="toggleMap">
                    <v-icon left>{{ showMap ? 'mdi-format-list-bulleted' : 'mdi-map' }}</v-icon>
                    {{ showMap ? 'List View' : 'Map View' }}
                </v-btn>
            </v-col>
        </v-row>

        <div v-if="loading" class="d-flex justify-center align-center" style="height: 200px;">
            <v-progress-circular indeterminate color="primary" size="50"></v-progress-circular>
        </div>

        <!-- Map View -->
        <div v-show="showMap" id="mapContainer" style="height: 400px; border-radius: 12px;" class="mb-4"></div>
        <!-- List View -->
        <v-row dense v-show="!showMap">
            <v-col v-for="job in jobs" :key="job.id" cols="12" sm="6" md="4">
                <v-card class="pa-3 rounded-lg" elevation="2">
                    <v-row align="center" no-gutters>
                        <!-- Avatar -->
                        <v-col cols="auto">
                            <v-avatar size="60" color="orange lighten-2">
                                <v-img :src="job.employer_image_url" alt="job Image" cover></v-img>
                            </v-avatar>
                        </v-col>

                        <!-- Info -->
                        <v-col class="pl-3">
                            <h4 class="mb-1 text-subtitle-1">{{ job.title }}</h4>
                            <p class="mb-1 text-subtitle-2">{{ job.employer_name }}</p>

                            <p class="mb-0 text-caption">📍 {{ job.employer_phone }}</p>
                            <p class="mb-0 text-caption">💰 {{ job.rate }}</p>
                            <!-- <v-chip-group class="d-flex flex-wrap">
                                <v-chip v-for="skill in job.skills_list" :key="skill" class="ma-1" color=""
                                    text-color="white" pill>
                                    {{ skill.name }}
                                </v-chip>
                            </v-chip-group> -->
                        </v-col>

                        <!-- Button -->
                        <v-col cols="auto" class="pl-2">
                            <v-btn color="primary" size="small" icon @click="showJobDetails(job)">
                                <v-icon>mdi-chevron-right</v-icon>
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-card>
            </v-col>
        </v-row>

        <!-- Load More -->
        <div v-if="has_more && !showMap" class="text-center mt-4">
            <v-btn color="primary" variant="tonal" @click="loadMore">
                Load More
            </v-btn>
        </div>
    </v-container>
</template>

<script>

import LogoutAppBar from "@/components/header/LogoutAppBar.vue";

import api from "@/services/api.js";
import apiRoutes from "@/services/apiRoutes.js";
import { Geolocation } from "@capacitor/geolocation";

export default {
    name: "SearchJobPage",
    components: { LogoutAppBar },
    data() {
        return {
            loading: false,
            map: null,
            markers: [],
            showMap: false,
            visibleCount: 10,
            filters: {
                location: "",
                pincode: "",
                skill: "",
                range: "",
            },
            latitude: "",
            longitude: "",
            skills: [],
            jobs: [],
            ranges: [
                { id: 1, name: '1' },
                { id: 5, name: '5' },
                { id: 10, name: '10' },
                { id: 20, name: '20' },
                { id: 50, name: '50' },
                { id: 100, name: '100' },
            ],
            page: 1,
            has_more: false,
            mapObj: null,
            markerObj: null,
            selectedLocation: null,
        };
    },
    computed: {

    },
    mounted() {
        this.getjobs(),
            this.getskill(),
            this.useCurrentLocation()
        // this.openMap()
    },
    methods: {
        async getskill() {
            try {
                const res = await api.get(apiRoutes.getAllSkill);
                //console.log('skills', res.data)
                if (res.data && res.data.data) {
                    this.skills = res.data.data; // expected: [{id, name}]
                }
            } catch (error) {
                //console.error("Error fetching skills:", error);
                alert("Failed to load skills.");
            }
        },

        async getjobs() {
            this.loading = true;
            try {
                const res = await api.get(apiRoutes.workerSearchjobJobs, {
                    params: {
                        location: this.filters.location,
                        lat: this.latitude,
                        long: this.longitude,
                        skill_id: this.filters.skill,
                        range: this.filters.range,
                        page: this.page,
                    }
                });

                // console.log('job', res.data)

                if (res.data?.jobs) {
                    // APPEND new jobs to old list
                    this.jobs = [...this.jobs, ...res.data.jobs];
                    // update page for next "Load More"

                    this.page = res.data.next_page;
                    this.has_more = res.data.has_more;

                    if (this.showMap) {
                        this.addjobMarkers();
                    }

                }
                this.loading = false;
            } catch (error) {
                //console.error("Error fetching skills:", error);
                alert("Failed to load job.");
                this.loading = false;
            }
        },

        filterjobs() {
            this.page = 1;
            this.jobs = [];
            this.getjobs()

        },

        loadMore() {
            this.getjobs()
        },
        showJobDetails(job) {
            this.$router.push({ name: 'worker-dashboard-employer-detail', params: { id: job.id } });
        },

        // map integrate
        toggleMap() {
            this.showMap = !this.showMap;

            if (this.showMap) {
                setTimeout(() => {
                    this.initMap();

                    // Fix for hidden map inside v-show
                    setTimeout(() => {
                        if (this.mapObj?.invalidateSize) {
                            this.mapObj.invalidateSize();
                        }
                    }, 200);
                }, 200);
            }
        },

        // openMap() {
        //     this.showMap = true;
        //     this.selectedLocation = {
        //         lat: this.latitude,
        //         lng: this.longitude,
        //     };

        //     setTimeout(() => this.initMap(), 350);
        // },

        loadMapplsScript() {
            return new Promise((resolve) => {
                if (window.mappls) return resolve();

                const script = document.createElement("script");
                script.src =
                    "https://apis.mappls.com/advancedmaps/api/" + apiRoutes.mapSecretKey + "/map_sdk?v=3.0&layer=vector";
                script.async = true;
                script.onload = resolve;
                document.body.appendChild(script);
            });
        },
        getPrimaryColor() {
            return getComputedStyle(document.documentElement)
                .getPropertyValue("--v-primary-base") // Vuetify 3 variable
                .trim() || "#1976D2"; // fallback
        },

        // Create SVG marker dynamically
        createMarkerSVG(color) {
            return `
        <svg width="32" height="32" viewBox="0 0 24 24" fill="${color}" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C8.1 2 5 5.1 5 9c0 5.2 7 13 7 13s7-7.8 7-13c0-3.9-3.1-7-7-7zm0 9.5c-1.4 0-2.5-1.1-2.5-2.5S10.6 6.5 12 6.5s2.5 1.1 2.5 2.5S13.4 11.5 12 11.5z"/>
        </svg>
        `;
        },

        // Convert SVG to Base64 for Mappls Marker icon
        createMarkerIcon(color) {
            return "data:image/svg+xml;base64," + btoa(this.createMarkerSVG(color));
        },

        async useCurrentLocation() {
            try {
                const coords = await Geolocation.getCurrentPosition();
                this.latitude = coords.coords.latitude;
                this.longitude = coords.coords.longitude;

                const lat = this.latitude;
                const lng = this.longitude;

                this.markerObj.setPosition({ lat, lng });
                //console.log("GPS allowed");
            } catch (e) {
                //console.log("GPS not allowed");
            }

        },

        confirmLocation() {
            if (!this.selectedLocation || !this.selectedLocation.lat) {
                alert("Please select a location first!");
                return;
            }

            //console.log(this.selectedLocation);
            this.latitude = this.selectedLocation.lat;
            this.longitude = this.selectedLocation.lng;

            this.showMap = false;
        },
        // -------------------------------------

        createUserMarkerSVG() {
            return `
            <svg width="30" height="30" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="8" fill="#E53935" opacity="0.4"/>
                <circle cx="12" cy="12" r="4" fill="#E53935"/>
            </svg>
            `;
        },
        createUserMarkerIcon() {
            return "data:image/svg+xml;base64," + btoa(this.createUserMarkerSVG());
        },

        async initMap() {
            await this.loadMapplsScript();

            this.mapObj = null;
            this.markerObj = null;



            const lat = this.latitude || 28.6139;
            const lng = this.longitude || 77.209;

            this.selectedLocation = { lat, lng };

            this.mapObj = new mappls.Map("mapContainer", {
                center: { lat, lng },
                zoom: 14,
            });

            // Add user marker (blue dot)
            this.markerObj = new mappls.Marker({
                map: this.mapObj,
                position: { lat, lng },
                draggable: true,
                icon: this.createUserMarkerIcon(),
            });

            this.markerObj.on("dragend", (evt) => {
                ////console.log(evt)
                const lat = evt.target._lngLat.lat || evt.latitude;
                const lng = evt.target._lngLat.lng || evt.longitude;

                this.selectedLocation = { lat, lng };

                this.latitude = this.selectedLocation.lat;
                this.longitude = this.selectedLocation.lng;
                //console.log("Dragged:", this.selectedLocation);
            });

            this.mapObj.on("click", (e) => {

                //console.log('dd', e.lngLat)
                const lat = e.lngLat.lat;
                const lng = e.lngLat.lng;

                this.selectedLocation = { lat, lng };

                // If marker exists -> move it
                if (this.markerObj) {
                    this.markerObj.setPosition({ lat, lng });
                } else {
                    // Create new marker
                    this.markerObj = new mappls.Marker({
                        map: this.mapObj,
                        position: { lat, lng },
                        draggable: true,
                        icon: this.createUserMarkerIcon(),

                    });

                    // Add dragend event only once
                    this.markerObj.on("dragend", (evt) => {
                        ////console.log(evt)
                        const lat = evt.target._lngLat.lat || evt.latitude;
                        const lng = evt.target._lngLat.lng || evt.longitude;

                        this.selectedLocation = { lat, lng };
                        //console.log("Dragged:", this.selectedLocation);
                    });
                }

                this.latitude = this.selectedLocation.lat;
                this.longitude = this.selectedLocation.lng;

                //console.log("Clicked:", this.selectedLocation);
            });


            // Add job markers
            this.addjobMarkers();
        },
        addjobMarkers() {
            if (!this.jobs || this.jobs.length === 0) return;


            this.clearjobMarkers();

            this.markers = [];

            this.jobs.forEach(job => {
                const marker = new mappls.Marker({
                    map: this.mapObj,
                    position: { lat: job.lat, lng: job.long },
                    icon: this.createMarkerIcon(this.getPrimaryColor()),
                    // fitbounds: true,
                    popupHtml: `
                                   <div style="padding: 8px; font-size: 14px;">
                    <strong>${job.title}</strong><br>
                    📞 ${job.employer_phone}<br><br>
                    <button id="viewDetailsBtn_${job.id}"
                        style="padding:6px 10px; background:#1976D2; color:#fff; border:none; border-radius:4px;">
                        View Details
                    </button>
                </div>
                                `
                });

                this.markers.push(marker);

                // Attach click event for popup button after marker is clicked
                marker.addListener("click", () => {
                    // Wait a tiny bit for popup to render
                    setTimeout(() => {
                        const btn = document.getElementById(`viewDetailsBtn_${job.id}`);
                        if (btn) {
                            btn.onclick = () => {
                                // Navigate to detail page with job id
                                this.$router.push({ name: "worker-dashboard-employer-detail", params: { id: job.id } });
                            };
                        }
                    }, 50);
                });
            });

        },
        clearjobMarkers() {
            if (this.markers && this.markers.length > 0) {
                this.markers.forEach(marker => {
                    marker.remove(); // remove from map
                });
                this.markers = [];
            }
        },


    },
};
</script>

<style scoped>
.v-card {
    transition: 0.2s ease;
}

.v-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
}
</style>
