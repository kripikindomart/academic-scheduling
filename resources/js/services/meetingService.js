import axios from 'axios';

const API_BASE_URL = '/api/schedules';

class MeetingService {
    constructor() {
        this.api = axios.create({
            baseURL: API_BASE_URL,
            headers: {
                'Content-Type': 'application/json',
            },
        });

        // Add auth token to requests
        this.api.interceptors.request.use((config) => {
            const token = localStorage.getItem('token');
            if (token) {
                config.headers.Authorization = `Bearer ${token}`;
            }
            return config;
        });

        // Response interceptor
        this.api.interceptors.response.use(
            (response) => response,
            (error) => {
                if (!error.response) {
                    error.userMessage = 'Tidak dapat terhubung ke server.';
                    return Promise.reject(error);
                }

                const status = error.response.status;

                if (status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                }

                return Promise.reject(error);
            }
        );
    }

    // Get all meetings
    async getAll(params = {}) {
        const response = await this.api.get('/', { params });
        return response.data;
    }

    // Get meeting by ID
    async getById(id) {
        const response = await this.api.get(`/${id}`);
        return response.data;
    }

    async update(id, data) {
        const response = await this.api.put(`/${id}`, data);
        return response.data;
    }

    async delete(id) {
        const response = await this.api.delete(`/${id}`);
        return response.data;
    }
}

export default new MeetingService();
