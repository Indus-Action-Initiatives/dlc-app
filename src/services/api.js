import axios from "axios";

const api = axios.create({
    //baseURL: "https://apps.labour.gov.in/labourchowk/api/public/api/", // 🌐 your backend base URL
    // baseURL: "http://13.235.48.250/api/", // 🌐 DocumentRoot is /var/www/dlc-app/public — app served at server root
    baseURL: "https://dlc-app-api.indusaction.org/api/",
    timeout: 10000, // 10 sec timeout
});

// Optional: add auth token automatically
api.interceptors.request.use((config) => {
    const userData = JSON.parse(localStorage.getItem('labour_currentUser'))
    const token = userData?.token // optional chaining avoids errors
    //console.log(token);
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
});


// Intercept responses
api.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 401) {
            // Token expired or unauthorized — redirect to home/login page
            alert('Authorization Failled!!')
            window.location.href = '/';
        }
        return Promise.reject(error);
    }
);


export default api;
