import axios from 'axios';

const API_BASE_URL = '/api/class-schedules';

class ScheduleService {
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

    // Enhanced response interceptor for better error handling
    this.api.interceptors.response.use(
      (response) => response,
      (error) => {
        // Handle network errors
        if (!error.response) {
          error.code = 'NETWORK_ERROR';
          error.userMessage = 'Tidak dapat terhubung ke server. Silakan periksa koneksi internet Anda.';
          error.errorType = 'NETWORK_ERROR';
          return Promise.reject(error);
        }

        const status = error.response.status;
        const responseData = error.response.data;

        switch (status) {
          case 401:
            // Unauthorized - token expired or invalid
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '/login';
            return Promise.reject(error);

          case 403:
            // Forbidden - no permission
            error.userMessage = responseData?.message || 'Anda tidak memiliki izin untuk melakukan aksi ini.';
            error.errorType = 'PERMISSION_ERROR';
            break;

          case 404:
            // Not found
            error.userMessage = responseData?.message || 'Data yang dicari tidak ditemukan.';
            error.errorType = 'NOT_FOUND_ERROR';
            break;

          case 422:
            // Validation error
            const validationErrors = responseData?.errors || responseData?.meta || {};
            error.userMessage = 'Validasi gagal. Silakan periksa kembali input Anda.';
            error.validationErrors = validationErrors;
            error.errorType = 'VALIDATION_ERROR';
            break;

          case 429:
            // Too many requests
            error.userMessage = 'Terlalu banyak permintaan. Silakan tunggu beberapa saat sebelum mencoba lagi.';
            error.errorType = 'RATE_LIMIT_ERROR';
            break;

          case 500:
          case 502:
          case 503:
          case 504:
            // Server errors
            error.userMessage = responseData?.message || 'Terjadi kesalahan pada server. Silakan coba lagi nanti.';
            error.errorType = 'SERVER_ERROR';
            break;

          default:
            // Other errors
            error.userMessage = responseData?.message || error.message || 'Terjadi kesalahan yang tidak diketahui.';
            error.errorType = 'UNKNOWN_ERROR';
        }

        return Promise.reject(error);
      }
    );
  }

  // Get all class schedules with filters
  async getAll(params = {}) {
    const response = await this.api.get('/', { params });
    return response.data;
  }

  // Get class schedule by ID
  async getById(id) {
    const response = await this.api.get(`/${id}`);
    return response.data;
  }

  // Create new class schedule
  async create(data) {
    const response = await this.api.post('/', data);
    return response.data;
  }

  // Update class schedule
  async update(id, data) {
    const response = await this.api.put(`/${id}`, data);
    return response.data;
  }

  // Delete class schedule
  async delete(id) {
    const response = await this.api.delete(`/${id}`);
    return response.data;
  }

  // Get statistics
  async getStatistics() {
    const response = await this.api.get('/statistics');
    return response.data;
  }

  // Quick check conflicts for reschedule
  async quickCheckConflicts(data) {
    const response = await this.api.post('/quick-check-conflicts', data);
    return response.data;
  }

  // Bulk operations
  async bulkDelete(data) {
    const response = await this.api.delete('/bulk-delete', { data });
    return response.data;
  }

  async bulkUpdate(data) {
    const response = await this.api.put('/bulk-update', data);
    return response.data;
  }

  // Import/Export
  async import(formData) {
    const response = await this.api.post('/import', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  }

  async export(params = {}) {
    const response = await this.api.get('/export', {
      params,
      responseType: 'blob'
    });
    return response.data;
  }

  // Trash Management
  async getTrash(params = {}) {
    const response = await this.api.get('/trash', { params });
    return response.data;
  }

  async restore(id) {
    const response = await this.api.post(`/${id}/restore`);
    return response.data;
  }

  async forceDelete(id) {
    const response = await this.api.delete(`/${id}/force-delete`);
    return response.data;
  }

  async duplicate(id) {
    const response = await this.api.post(`/${id}/duplicate`);
    return response.data;
  }

  // Course Enrollment
  async addCourse(scheduleId, data) {
    const response = await this.api.post(`/${scheduleId}/add-course`, data);
    return response.data;
  }

  async removeCourse(scheduleId, detailId) {
    const response = await this.api.delete(`/${scheduleId}/remove-course/${detailId}`);
    return response.data;
  }

  // Generate schedules from enrolled courses (uses existing ClassScheduleDetail data)
  async autoGenerate(scheduleId) {
    const response = await this.api.post(`/${scheduleId}/generate-schedules`);
    return response.data;
  }

  // Get room usage statistics
  async getRoomUsage(scheduleId) {
    const response = await this.api.get(`/${scheduleId}/room-usage`);
    return response.data;
  }
}

export default new ScheduleService();