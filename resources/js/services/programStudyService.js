import axios from 'axios';

const API_BASE_URL = '/api/program-studies';

class ProgramStudyService {
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

    // Add response interceptor for error handling
    this.api.interceptors.response.use(
      (response) => response,
      (error) => {
        if (error.response?.status === 401) {
          // Unauthorized - token expired or invalid
          localStorage.removeItem('token');
          window.location.href = '/login';
          return Promise.reject(error);
        }

        if (error.response?.status === 403) {
          // Forbidden - no permission
          const errorMessage = error.response.data?.message || 'Anda tidak memiliki izin untuk mengakses halaman ini.';
          error.userMessage = errorMessage;
          error.errorType = 'PERMISSION_ERROR';
          return Promise.reject(error);
        }

        if (error.response?.status === 422) {
          // Validation error - handle both formats (errors or meta)
          const validationErrors = error.response.data?.errors || error.response.data?.meta || {};
          error.userMessage = 'Validasi gagal. Silakan periksa kembali input Anda.';
          error.validationErrors = validationErrors;
          error.errorType = 'VALIDATION_ERROR';
          return Promise.reject(error);
        }

        // Other errors
        const errorMessage = error.response.data?.message || error.message || 'Terjadi kesalahan pada server.';
        error.userMessage = errorMessage;
        error.errorType = 'SERVER_ERROR';
        return Promise.reject(error);
      }
    );
  }

  // Get all program studies with filters
  async getAll(params = {}) {
    const response = await this.api.get('/', { params });
    return response.data;
  }

  // Get program study by ID
  async getById(id) {
    const response = await this.api.get(`/${id}`);
    return response.data;
  }

  // Create new program study
  async create(data) {
    const response = await this.api.post('/', data);
    return response.data;
  }

  // Update program study
  async update(id, data) {
    const response = await this.api.put(`/${id}`, data);
    return response.data;
  }

  // Delete program study
  async delete(id) {
    const response = await this.api.delete(`/${id}`);
    return response.data;
  }

  // Get faculties list
  async getFaculties() {
    const response = await this.api.get('/faculties');
    return response.data;
  }

  // Get statistics
  async getStatistics() {
    const response = await this.api.get('/statistics');
    return response.data;
  }

  // Bulk operations
  async bulkUpdate(data) {
    const response = await this.api.post('/bulk-update', data);
    return response.data;
  }

  async bulkDelete(data) {
    const response = await this.api.post('/bulk-delete', data);
    return response.data;
  }

  async bulkToggleStatus(data) {
    const response = await this.api.post('/bulk-toggle-status', data);
    return response.data;
  }

  // Assign/remove lecturers
  async assignLecturer(programId, lecturerId) {
    const response = await this.api.post(`/${programId}/lecturers`, { lecturer_id: lecturerId });
    return response.data;
  }

  async removeLecturer(programId, lecturerId) {
    const response = await this.api.delete(`/${programId}/lecturers`, { data: { lecturer_id: lecturerId } });
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

  async toggleStatus(id, isActive) {
    const response = await this.api.put(`/${id}/toggle-status`, { is_active: isActive });
    return response.data;
  }

  async duplicate(id) {
    const response = await this.api.post(`/${id}/duplicate`);
    return response.data;
  }
}

export default new ProgramStudyService();