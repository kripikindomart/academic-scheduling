import axios from 'axios';

const API_BASE_URL = '/api/lecturers';

class LecturerService {
  constructor() {
    this.api = axios.create({
      baseURL: API_BASE_URL,
      // Don't set default Content-Type here to allow FormData to set multipart/form-data
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

          // Store error info for handling by components
          localStorage.setItem('permissionError', JSON.stringify({
            message: errorMessage,
            timestamp: Date.now()
          }));

          // Redirect to dashboard after a short delay
          setTimeout(() => {
            window.location.href = '/dashboard';
          }, 500);

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

  // Get all lecturers with filters
  async getAll(params = {}) {
    const response = await this.api.get('/', { params });
    return response.data;
  }

  // Get lecturer by ID
  async getById(id) {
    const response = await this.api.get(`/${id}`);
    return response.data;
  }

  // Create new lecturer
  async create(data) {
    // If data is FormData, don't set content-type to let browser set it with boundary
    const config = data instanceof FormData
      ? {}
      : {};
    const response = await this.api.post('/', data, config);
    return response.data;
  }

  // Update lecturer
  async update(id, data) {
    // Use POST method for FormData to properly handle multipart data with _method override
    if (data instanceof FormData) {
      // Laravel will automatically handle _method field to route to PUT method
      const response = await this.api.post(`/${id}?_method=PUT`, data);
      return response.data;
    } else {
      const response = await this.api.put(`/${id}`, data);
      return response.data;
    }
  }

  // Delete lecturer
  async delete(id) {
    const response = await this.api.delete(`/${id}`);
    return response.data;
  }

  // Get statistics
  async getStatistics(includeTrash = false) {
    const params = includeTrash ? { include_trash: true } : {};
    const response = await this.api.get('/statistics', { params });
    return response.data;
  }

  // Get active lecturers
  async getActive() {
    const response = await this.api.get('/active');
    return response.data;
  }

  // Get lecturers with high workload
  async getHighWorkload() {
    const response = await this.api.get('/high-workload');
    return response.data;
  }

  // Search suggestions
  async searchSuggestions(params = {}) {
    const response = await this.api.get('/search-suggestions', { params });
    return response.data;
  }

  // Get lecturers for scheduling
  async getForScheduling() {
    const response = await this.api.get('/for-scheduling');
    return response.data;
  }

  // Bulk operations
  async bulkUpdate(data) {
    const response = await this.api.post('/bulk-update', data);
    return response.data;
  }

  async bulkDelete(data) {
    // Use the format expected by backend - expect { ids: [...] }
    const payload = { ids: data.ids || data };
    const response = await this.api.post('/bulk-delete', payload);
    return response.data;
  }

  async bulkToggleStatus(data) {
    const response = await this.api.post('/bulk-toggle-status', data);
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
    const response = await this.api.delete(`/force-delete/${id}`);
    return response.data;
  }

  async toggleStatus(id, data) {
    const response = await this.api.put(`/${id}/status`, data);
    return response.data;
  }

  async duplicate(id) {
    const response = await this.api.post(`/${id}/duplicate`);
    return response.data;
  }

  // Lecturer-specific methods
  async getByProgramStudy(programStudyId) {
    const response = await this.api.get(`/program-study/${programStudyId}`);
    return response.data;
  }

  async getByFaculty(faculty) {
    const response = await this.api.get(`/faculty/${faculty}`);
    return response.data;
  }

  async getByEmploymentType(type) {
    const response = await this.api.get(`/employment-type/${type}`);
    return response.data;
  }

  async getAvailableForCourse(courseId) {
    const response = await this.api.get(`/available-for-course/${courseId}`);
    return response.data;
  }

  async getTeachingLoad(id) {
    const response = await this.api.get(`/${id}/teaching-load`);
    return response.data;
  }

  async getAttendanceSummary(id) {
    const response = await this.api.get(`/${id}/attendance-summary`);
    return response.data;
  }

  async assignCourse(lecturerId, courseId) {
    const response = await this.api.post(`/${lecturerId}/assign-course/${courseId}`);
    return response.data;
  }

  async createUserAccount(lecturerId, password = null) {
    const data = password ? { password } : {};
    const response = await this.api.post(`/${lecturerId}/create-user-account`, data);
    return response.data;
  }

  async bulkCreateUserAccounts(lecturerIds) {
    const response = await this.api.post('/bulk-create-user-accounts', { ids: lecturerIds });
    return response.data;
  }

  // Bulk operations for trash
  async bulkForceDelete(data) {
    const response = await this.api.post('/bulk-force-delete', data);
    return response.data;
  }

  async bulkRestore(ids) {
    const response = await this.api.post('/bulk-restore', { ids });
    return response.data;
  }

  // File operations
  async downloadFile(filename) {
    const response = await this.api.get(`/download/${filename}`, {
      responseType: 'blob'
    });
    return response.data;
  }
}

export default new LecturerService();