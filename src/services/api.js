import axios from "axios";

const api = axios.create({
    // Now pulls from your .env file automatically
    baseURL: import.meta.env.VITE_API_BASE_URL, 
    timeout: 10000, // 10 sec timeout
});

// Optional: add auth token automatically
api.interceptors.request.use((config) => {
    const userData = JSON.parse(localStorage.getItem('labour_currentUser'))
    const token = userData?.token 
    
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
});

// Intercept responses
api.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 401) {
            // Token expired or unauthorized — redirect to home/login page
            alert('Authorization Failed!!')
            window.location.href = '/';
        }
        return Promise.reject(error);
    }
);

export default api;