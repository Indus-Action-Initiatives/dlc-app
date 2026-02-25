<template>
    <BackButtonAppBar />

    <v-container class="pa-4">
        <v-card elevation="2" class="pa-4">
            <v-card-title class="text-h6 font-weight-bold">
                🧱 Edit Job
            </v-card-title>
            <v-divider class="mb-4"></v-divider>

            <v-form ref="jobForm" lazy-validation>

                <!-- SECTION 1: JOB INFO -->
                <h4 class="text-subtitle-1 mb-2">Job Information</h4>

                <v-row dense>

                    <!-- Job Title -->
                    <v-col cols="12" sm="6">
                        <v-text-field v-model="form.title" label="Job Title" prepend-inner-icon="mdi-briefcase"
                            :error="v$.form.title.$error"
                            :error-messages="v$.form.title.$errors.map(() => 'Job title is required')" outlined dense />
                    </v-col>

                    <!-- Category -->
                    <v-col cols="12" sm="6">
                        <v-select v-model="form.category" label="Job Category" :items="categories" multiple
                            item-title="name" item-value="id" prepend-inner-icon="mdi-hammer-wrench"
                            :error="v$.form.category.$error"
                            :error-messages="v$.form.category.$errors.map(() => 'Category is required')" outlined
                            dense />
                    </v-col>

                    <!-- Dates -->
                    <v-col cols="12" sm="6">
                        <v-text-field v-model="form.startDate" label="Start Date" type="date"
                            prepend-inner-icon="mdi-calendar-start" :error="v$.form.startDate.$error"
                            :error-messages="v$.form.startDate.$errors.map(() => 'Start date required')" outlined
                            dense />
                    </v-col>

                    <v-col cols="12" sm="6">
                        <v-text-field v-model="form.endDate" label="End Date" type="date"
                            prepend-inner-icon="mdi-calendar-end" :error="v$.form.endDate.$error"
                            :error-messages="v$.form.endDate.$errors.map(() => 'End date required')" outlined dense />
                    </v-col>

                    <!-- Duration -->
                    <v-col cols="12" sm="6">
                        <v-text-field v-model="form.duration" label="Work Duration (Days)" type="number"
                            prepend-inner-icon="mdi-clock-outline" outlined dense />
                    </v-col>
                </v-row>

                <v-divider class="my-4"></v-divider>

                <!-- SECTION 2: ADDRESS -->
                <h4 class="text-subtitle-1 mb-2">Job Address</h4>

                <v-row dense>

                    <v-col cols="12">
                        <v-textarea v-model="form.address" label="Full Address" prepend-inner-icon="mdi-map-marker"
                            :error="v$.form.address.$error"
                            :error-messages="v$.form.address.$errors.map(() => 'Address required')" outlined rows="2"
                            dense />
                    </v-col>

                    <!-- State -->
                    <v-col cols="12" sm="6">
                        <v-select v-model="form.state" label="राज्य - State" :items="states" item-title="state_name"
                            item-value="lgd_code" :error="v$.form.state.$error"
                            :error-messages="v$.form.state.$errors.map(() => 'State required')"
                            @update:modelValue="getDistrict" />
                    </v-col>

                    <!-- District -->
                    <v-col cols="12" sm="6">
                        <v-select v-model="form.district" label="जिला - District" :items="districts"
                            item-value="lgd_code" item-title="district_name" :error="v$.form.district.$error"
                            :error-messages="v$.form.district.$errors.map(() => 'District required')" />
                    </v-col>

                    <!-- Pincode -->
                    <v-col cols="12" sm="6">
                        <v-text-field v-model="form.pincode" label="Pincode" type="number"
                            prepend-inner-icon="mdi-map-marker-radius" :error="v$.form.pincode.$error" :error-messages="v$.form.pincode.$errors.map(e =>
                            e.$validator === 'minLength' ? 'Pincode must be 6 digits' : 'Pincode required'
                        )" outlined dense />
                    </v-col>

                    <!-- Map Selector -->
                    <v-col cols="12" sm="6">
                        <v-btn color="primary" @click="openMap">Select Location on Map</v-btn>
                    </v-col>

                    <v-col cols="12" sm="6">
                        <v-text-field v-model="form.latitude" label="Latitude" readonly outlined dense />
                    </v-col>

                    <v-col cols="12" sm="6">
                        <v-text-field v-model="form.longitude" label="Longitude" readonly outlined dense />
                    </v-col>
                </v-row>

                <v-divider class="my-4"></v-divider>

                <!-- SECTION 3: DESCRIPTION -->
                <h4 class="text-subtitle-1 mb-2">Work Description</h4>

                <v-row dense>

                    <v-col cols="12">
                        <v-textarea v-model="form.description" label="Job Description" prepend-inner-icon="mdi-text"
                            :error="v$.form.description.$error"
                            :error-messages="v$.form.description.$errors.map(() => 'Description is required')" outlined
                            rows="3" dense />
                    </v-col>

                    <v-col cols="12" sm="6">
                        <v-text-field v-model="form.tools" label="Tools Required" prepend-inner-icon="mdi-toolbox"
                            outlined dense hint="Separate with commas" />
                    </v-col>

                    <v-col cols="12" sm="3">
                        <v-text-field v-model="form.requiredPeople" label="Required People" type="number"
                            prepend-inner-icon="mdi-account-group" :error="v$.form.requiredPeople.$error"
                            :error-messages="v$.form.requiredPeople.$errors.map(() => 'Required people is mandatory')"
                            outlined dense />
                    </v-col>

                    <v-col cols="12" sm="3">
                        <v-text-field v-model="form.workTime" label="Work Time (e.g. 9 AM - 6 PM)"
                            prepend-inner-icon="mdi-clock-time-four" outlined dense />
                    </v-col>

                    <!-- Work Type -->
                    <v-col cols="12" sm="6">
                        <v-select v-model="form.workType" label="Preferred Work Type"
                            :items="['Daily Wage', 'Contract']" :error="v$.form.workType.$error"
                            :error-messages="v$.form.workType.$errors.map(() => 'Work type required')" outlined dense />
                    </v-col>
                    <!-- Duration -->
                    <v-col cols="12" sm="6">
                        <v-text-field v-model="form.rate" label="Work Rate/Day Rs" type="number"
                            prepend-inner-icon="mdi-clock-outline" outlined dense />
                    </v-col>
                </v-row>

                <v-divider class="my-4"></v-divider>

                <!-- Facilities -->
                <h4 class="text-subtitle-1 mb-2">Facilities Provided</h4>

                <v-row dense>
                    <v-col cols="12" sm="6">
                        <v-select v-model="form.facilitie" label="Facilities" :items="facilities" item-title="name"
                            item-value="id" multiple chips />
                    </v-col>

                    <v-col cols="12" sm="6">
                        <v-select v-model="form.status" label="Status" :items="statuses" item-title="name"
                            item-value="id" />
                    </v-col>
                </v-row>

                <!-- Submit -->
                <v-btn class="mt-4" color="primary" block @click="submitForm">
                    <v-icon left>mdi-content-save</v-icon> Post Job
                </v-btn>

            </v-form>

            <!-- MAP DIALOG -->
            <v-dialog v-model="showMap" max-width="600px">
                <v-card>
                    <v-card-title>Select Location</v-card-title>

                    <v-card-text>
                        <div id="mapContainer" style="width:100%; height:400px;"></div>
                        <v-btn class="mt-3" small color="primary" @click="useCurrentLocation">Use My Location</v-btn>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="primary" @click="confirmLocation" variant="tonal">Confirm</v-btn>
                        <v-btn color="error" @click="showMap = false" variant="tonal">Cancel</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

        </v-card>
    </v-container>
</template>

<script>
import BackButtonAppBar from "@/components/header/BackButtonAppBar.vue";
import { Geolocation } from "@capacitor/geolocation";
import api from "@/services/api.js";
import apiRoutes from "@/services/apiRoutes.js";

// Vuelidate
import useVuelidate from "@vuelidate/core";
import { required, minLength, numeric } from "@vuelidate/validators";


export default {
    components: { BackButtonAppBar },

    data() {
        return {
            v$: null,

            showMap: false,
            mapObj: null,
            markerObj: null,
            selectedLocation: null,

            categories: [

            ],

            // facilities: [
            //     { name: "Water", icon: "mdi-water" },
            //     { name: "Food", icon: "mdi-food" },
            //     { name: "Shelter", icon: "mdi-home" },
            //     { name: "Safety", icon: "mdi-shield-check" },
            //     { name: "First Aid", icon: "mdi-hospital-box" },
            // ],

            form: {
                title: "",
                category: null,
                startDate: "",
                endDate: "",
                duration: "",
                address: "",
                pincode: "",
                state: null,
                description: "",
                tools: "",
                requiredPeople: "",
                workTime: "",
                facilitie: null,
                latitude: "",
                longitude: "",
                district: null,
                workType: "",
                rate: "",
                status: ""
            },
            states: [],
            districts: [],
            facilities: [],
            job: {
            },
            statuses: [
                { id: true, name: "Open" },
                { id: false, name: "Closed" }
            ],
        };
    },
    validations() {
        return {
            form: {
                title: { required },
                category: { required },
                address: { required },
                pincode: { required, numeric, minLength: minLength(6) },
                state: { required },
                district: { required },
                description: { required },
                requiredPeople: { required, numeric },
                workType: { required },
                startDate: { required },
                endDate: { required },
                //facilitie: { required }
            }
        };
    },
    created() {
        this.v$ = useVuelidate();
    },


    methods: {
        // toggleFacility(fac) {
        //     const i = this.form.facilities.indexOf(fac);
        //     if (i > -1) this.form.facilities.splice(i, 1);
        //     else this.form.facilities.push(fac);
        // },

        async submitForm() {
            // if (this.$refs.jobForm.validate()) {
            //     //console.log("Job Posted:", this.form);
            //     alert("Job posted successfully!");
            // }

            const isValid = await this.v$.$validate();

            if (!isValid) {
                alert("Please fill all required fields.");
                return;
            }


            try {
                const employerData = JSON.parse(localStorage.getItem("labour_currentUser"));
                const jobId = this.$route.params.id;
                const res = await api.post(apiRoutes.employerJobUpdate + jobId, {

                    employer_id: employerData.employer.id,
                    title: this.form.title,
                    description: this.form.description,
                    skills_id: this.form.category,
                    address: this.form.address,
                    // qualification :
                    job_type: this.form.workType,
                    tools_required: this.form.tools,
                    required_people: this.form.requiredPeople,
                    work_time: this.form.workTime,
                    work_duration: this.form.duration,
                    lat: this.form.latitude,
                    long: this.form.longitude,
                    start_date: this.form.startDate,
                    end_date: this.form.endDate,
                    state: this.form.state.toString(),
                    district: this.form.district.toString(),
                    pincode: this.form.pincode,
                    facilities: this.form.facilitie,
                    rate: this.form.rate,
                    status: this.form.status,
                });
                alert('Success !!');
                //console.log("Success:", res.data)
                this.$router.push('/employer-dashboard-home-myjob');

            } catch (err) {
                alert(
                    "Error !!\n" +
                    Object.values(err.response?.data?.errors || {}).flat().join("\n")
                )
                //console.error("Error post:", err)
            }
        },
        openMap() {
            this.showMap = true;
            this.selectedLocation = {
                lat: this.form.latitude,
                lng: this.form.longitude,
            };

            setTimeout(() => this.initMap(), 350);
        },

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
        async initMap() {
            await this.loadMapplsScript();

            // Always reset when opening dialog
            this.mapObj = null;
            this.markerObj = null;

            const lat = this.form.latitude || 28.6139;
            const lng = this.form.longitude || 77.209;

            this.selectedLocation = { lat, lng };

            this.mapObj = new window.mappls.Map("mapContainer", {
                center: { lat, lng },
                zoom: 14,
            });

            // Draw initial marker
            this.markerObj = new window.mappls.Marker({
                map: this.mapObj,
                position: { lat, lng },
                draggable: true,
                icon: this.createMarkerIcon(this.getPrimaryColor()),
            });

            this.markerObj.on("dragend", (evt) => {
                ////console.log(evt)
                const lat = evt.target._lngLat.lat || evt.latitude;
                const lng = evt.target._lngLat.lng || evt.longitude;

                this.selectedLocation = { lat, lng };
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
                        icon: this.createMarkerIcon(this.getPrimaryColor())
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

                //console.log("Clicked:", this.selectedLocation);
            });



            // setTimeout(() => this.mapObj.invalidateSize(), 300);
        },

        async useCurrentLocation() {
            try {
                const coords = await Geolocation.getCurrentPosition();
                this.form.latitude = coords.coords.latitude;
                this.form.longitude = coords.coords.longitude;
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
            this.form.latitude = this.selectedLocation.lat;
            this.form.longitude = this.selectedLocation.lng;

            this.showMap = false;
        },
        ///get data for form
        async getState() {
            try {
                const res = await api.get(apiRoutes.getState);
                //console.log("state:", res.data);

                this.states = res.data.data;
                this.form.state = "";
                this.form.district = "";

                ////console.log(form.value);
                ////console.log("Success: states:", states);

            } catch (err) {
                alert(
                    "Error !!\n" +
                    Object.values(err.response?.data?.errors || {}).flat().join("\n")
                );
                //console.error("Error fetching state", err);
            }

        },
        async getDistrict() {
            this.form.district = "";
            if (!this.form.state) return
            try {
                const res = await api.post(apiRoutes.getDistrict, { state_id: this.form.state });
                ////console.log("Success:district", res.data);

                this.districts = res.data.data;
                //console.log("Success: districts:", this.districts);

            } catch (err) {
                alert(
                    "Error !!\n" +
                    Object.values(err.response?.data?.errors || {}).flat().join("\n")
                );
                //console.error("Error fetching districts:", err);
            }

        },
        async getFacilities() {
            try {
                const res = await api.get(apiRoutes.getFacilities);
                //console.log('Facilities', res.data)
                if (res.data && res.data.data) {
                    this.facilities = res.data.data; // expected: [{id, name}]
                }
            } catch (error) {
                //console.error("Error fetching facilities:", error);
                alert("Failed to load facilities.");
            }

        },
        async getSkills() {

            try {
                const res = await api.get(apiRoutes.getAllSkill);
                //console.log('skills', res.data)
                if (res.data && res.data.data) {
                    this.categories = res.data.data; // expected: [{id, name}]
                }
            } catch (error) {
                //console.error("Error fetching skills:", error);
                alert("Failed to load skills.");
            }

        },
        async getMyjob() {
            const jobId = this.$route.params.id;
            try {
                const res = await api.get(apiRoutes.employerJobGetByJobId + jobId);
                console.log("my single  job:", res.data);
                this.job = res.data.data;

                this.form.title = this.job.title;
                this.form.description = this.job.description;
                this.form.category = JSON.parse(this.job.skills_id); // because skills_id = "[27,19]"
                this.form.address = this.job.address;
                this.form.workType = this.job.job_type;
                this.form.tools = this.job.tools_required;
                this.form.requiredPeople = this.job.required_people;
                this.form.workTime = this.job.work_time;
                this.form.duration = this.job.work_duration;
                this.form.latitude = this.job.lat;
                this.form.longitude = this.job.long;
                this.form.startDate = this.job.start_date;
                this.form.endDate = this.job.end_date;
                this.form.state = this.job.state?.lgd_code ?? this.job.state; // handles object or string

                this.form.pincode = this.job.pincode;
                this.form.facilitie = this.job.facilities?.map(f => f.id) ?? [];
                this.form.rate = this.job.rate;
                this.form.status = this.job.status;


                await this.getDistrict();

                // Now that districts are loaded, set district value
                this.form.district = this.job.district?.lgd_code ?? this.job.district;



            } catch (err) {
                alert(
                    "Error !!\n" +
                    Object.values(err.response?.data?.errors || {}).flat().join("\n")
                );
                console.error("Error fetching state", err);
            }

        }

    },

    mounted() {
        this.useCurrentLocation(),
            this.getState(),

            this.getFacilities(),
            this.getSkills(),
            this.getMyjob()

    },
};
</script>

<style scoped>
.v-card {
    transition: 0.2s ease;
}

.v-card:hover {
    transform: translateY(-3px);
}
</style>
