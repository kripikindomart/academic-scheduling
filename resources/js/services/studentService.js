import axios from 'axios';

const API_BASE_URL = '/api/students';

class StudentService {
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

      // Add CSRF token from meta tag or from global axios defaults
      const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content;
      if (csrfToken) {
        config.headers['X-CSRF-TOKEN'] = csrfToken;
      } else if (window.axios?.defaults?.headers?.common?.['X-CSRF-TOKEN']) {
        config.headers['X-CSRF-TOKEN'] = window.axios.defaults.headers.common['X-CSRF-TOKEN'];
      }

      // Add X-Requested-With header for Laravel
      config.headers['X-Requested-With'] = 'XMLHttpRequest';

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

  // Get all students with filters
  async getAll(params = {}) {
    const response = await this.api.get('/', { params });
    return response.data;
  }

  // Get student by ID
  async getById(id) {
    const response = await this.api.get(`/${id}`);
    return response.data;
  }

  // Create new student
  async create(data) {
    // If data is FormData, don't set content-type to let browser set it with boundary
    const config = data instanceof FormData
      ? {}
      : {};
    const response = await this.api.post('/', data, config);
    return response.data;
  }

  // Update student
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

  // Delete student
  async delete(id) {
    const response = await this.api.delete(`/${id}`);
    return response.data;
  }

  // Get statistics
  async getStatistics(params = {}) {
    const response = await this.api.get('/statistics', { params });
    return response.data;
  }

  // Get active students
  async getActive(params = {}) {
    const response = await this.api.get('/active', { params });
    return response.data;
  }

  // Get students by program study
  async getByProgramStudy(programStudyId, params = {}) {
    const response = await this.api.get(`/program-study/${programStudyId}`, { params });
    return response.data;
  }

  // Get students by batch year
  async getByBatchYear(batchYear, params = {}) {
    const response = await this.api.get(`/batch-year/${batchYear}`, { params });
    return response.data;
  }

  // Get students with low GPA
  async getLowGpaStudents(params = {}) {
    const response = await this.api.get('/low-gpa', { params });
    return response.data;
  }

  // Search suggestions
  async searchSuggestions(params = {}) {
    const response = await this.api.get('/search-suggestions', { params });
    return response.data;
  }

  // Get academic progress for a student
  async getAcademicProgress(id) {
    const response = await this.api.get(`/${id}/academic-progress`);
    return response.data;
  }

  // Get attendance summary for a student
  async getAttendanceSummary(id, params = {}) {
    const response = await this.api.get(`/${id}/attendance-summary`, { params });
    return response.data;
  }

  // Update student status
  async updateStatus(id, data) {
    const response = await this.api.put(`/${id}/status`, data);
    return response.data;
  }

  // Bulk operations
  async bulkUpdate(data) {
    const response = await this.api.post('/bulk-update', data);
    return response.data;
  }

  async bulkDelete(data) {
    // Use the format expected by backend - expect { student_ids: [...] }
    const payload = { student_ids: data.student_ids || data.ids || data };
    const response = await this.api.post('/bulk-delete', payload);
    return response.data;
  }

  async bulkToggleStatus(data) {
    const response = await this.api.post('/bulk-toggle-status', data);
    return response.data;
  }

  // Import/Export
  async downloadTemplate() {
    const response = await this.api.get('/download-template');
    return response.data;
  }

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

  async duplicate(id, duplicateData = {}) {
    const response = await this.api.post(`/${id}/duplicate`, duplicateData);
    return response.data;
  }

  // Bulk operations for trash
  async bulkForceDelete(data) {
    const response = await this.api.post('/bulk-force-delete', data);
    return response.data;
  }

  async bulkRestore(data) {
    // Use the format expected by backend - expect { ids: [...] }
    const payload = { ids: data.ids || data };
    const response = await this.api.post('/bulk-restore', payload);
    return response.data;
  }

  // Student-specific methods
  async getByClass(className, params = {}) {
    const response = await this.api.get(`/class/${className}`, { params });
    return response.data;
  }

  async getBySemester(semester, params = {}) {
    const response = await this.api.get(`/semester/${semester}`, { params });
    return response.data;
  }

  async getByGender(gender, params = {}) {
    const response = await this.api.get(`/gender/${gender}`, { params });
    return response.data;
  }

  async getGraduated(params = {}) {
    const response = await this.api.get('/graduated', { params });
    return response.data;
  }

  async getOnLeave(params = {}) {
    const response = await this.api.get('/on-leave', { params });
    return response.data;
  }

  async getDroppedOut(params = {}) {
    const response = await this.api.get('/dropped-out', { params });
    return response.data;
  }

  async getRegular(params = {}) {
    const response = await this.api.get('/regular', { params });
    return response.data;
  }

  async getNonRegular(params = {}) {
    const response = await this.api.get('/non-regular', { params });
    return response.data;
  }

  async getByGpaRange(minGpa, maxGpa = null, params = {}) {
    const queryParams = { min_gpa: minGpa, ...params };
    if (maxGpa) {
      queryParams.max_gpa = maxGpa;
    }
    const response = await this.api.get('/gpa-range', { params: queryParams });
    return response.data;
  }

  async getByAgeRange(minAge, maxAge = null, params = {}) {
    const queryParams = { min_age: minAge, ...params };
    if (maxAge) {
      queryParams.max_age = maxAge;
    }
    const response = await this.api.get('/age-range', { params: queryParams });
    return response.data;
  }

  async getByEnrollmentYear(year, params = {}) {
    const response = await this.api.get(`/enrollment-year/${year}`, { params });
    return response.data;
  }

  async getExpectedGraduation(year, params = {}) {
    const response = await this.api.get(`/expected-graduation/${year}`, { params });
    return response.data;
  }

  // User account management
  async createUserAccount(studentId, password = null) {
    const data = password ? { password } : {};
    const response = await this.api.post(`/${studentId}/create-user-account`, data);
    return response.data;
  }

  async bulkCreateUserAccounts(studentIds) {
    const response = await this.api.post('/bulk-create-user-accounts', { student_ids: studentIds });
    return response.data;
  }

  async removeUserAccount(studentId) {
    const response = await this.api.delete(`/${studentId}/user-account`);
    return response.data;
  }

  // Academic operations
  async enrollInCourse(studentId, courseId, data = {}) {
    const response = await this.api.post(`/${studentId}/enroll-course/${courseId}`, data);
    return response.data;
  }

  async dropFromCourse(studentId, courseId, data = {}) {
    const response = await this.api.delete(`/${studentId}/drop-course/${courseId}`, { data });
    return response.data;
  }

  async getGrades(studentId, params = {}) {
    const response = await this.api.get(`/${studentId}/grades`, { params });
    return response.data;
  }

  async getSchedule(studentId, params = {}) {
    const response = await this.api.get(`/${studentId}/schedule`, { params });
    return response.data;
  }

  async getTranscript(studentId, params = {}) {
    const response = await this.api.get(`/${studentId}/transcript`, { params });
    return response.data;
  }

  // File operations
  async downloadFile(filename) {
    const response = await this.api.get(`/download/${filename}`, {
      responseType: 'blob'
    });
    return response.data;
  }

  async uploadPhoto(studentId, formData) {
    const response = await this.api.post(`/${studentId}/upload-photo`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  }

  async removePhoto(studentId) {
    const response = await this.api.delete(`/${studentId}/remove-photo`);
    return response.data;
  }

  // Reporting
  async getReportData(params = {}) {
    const response = await this.api.get('/reports/data', { params });
    return response.data;
  }

  async getAcademicReport(params = {}) {
    const response = await this.api.get('/reports/academic', { params });
    return response.data;
  }

  async getAttendanceReport(params = {}) {
    const response = await this.api.get('/reports/attendance', { params });
    return response.data;
  }

  async getPerformanceReport(params = {}) {
    const response = await this.api.get('/reports/performance', { params });
    return response.data;
  }

  // Helper methods
  async exportSelected(ids, params = {}) {
    const data = { ids, ...params };
    const response = await this.api.post('/export-selected', data, {
      responseType: 'blob'
    });
    return response.data;
  }

  async printStudentCard(id) {
    const response = await this.api.get(`/${id}/print-card`, {
      responseType: 'blob'
    });
    return response.data;
  }

  async printTranscript(id) {
    const response = await this.api.get(`/${id}/print-transcript`, {
      responseType: 'blob'
    });
    return response.data;
  }
}

export default new StudentService();