import axios from 'axios';

const API_BASE_URL = '/api/classes';

class ClassService {
  constructor() {
    this.api = axios.create({
      baseURL: API_BASE_URL,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
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
          localStorage.removeItem('token');
          window.location.href = '/login';
          return Promise.reject(error);
        }

        if (error.response?.status === 403) {
          const errorMessage = error.response.data?.message || 'Anda tidak memiliki izin untuk mengakses halaman ini.';
          error.userMessage = errorMessage;
          error.errorType = 'PERMISSION_ERROR';

          localStorage.setItem('permissionError', JSON.stringify({
            message: errorMessage,
            timestamp: Date.now()
          }));

          setTimeout(() => {
            window.location.href = '/dashboard';
          }, 500);

          return Promise.reject(error);
        }

        if (error.response?.status === 422) {
          const validationErrors = error.response.data?.errors || error.response.data?.meta || {};
          error.userMessage = 'Validasi gagal. Silakan periksa kembali input Anda.';
          error.validationErrors = validationErrors;
          error.errorType = 'VALIDATION_ERROR';
          return Promise.reject(error);
        }

        const errorMessage = error.response.data?.message || error.message || 'Terjadi kesalahan pada server.';
        error.userMessage = errorMessage;
        error.errorType = 'SERVER_ERROR';
        return Promise.reject(error);
      }
    );
  }

  // Get all classes
  async getAll(params = {}) {
    const response = await this.api.get('/', { params });
    return response.data;
  }

  // Get class by ID
  async getById(id) {
    const response = await this.api.get(`/${id}`);
    return response.data;
  }

  // Create new class
  async create(data) {
    // Add CSRF token only for form submissions
    const config = {
      headers: {
        'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content ||
                      window.axios?.defaults?.headers?.common?.['X-CSRF-TOKEN']
      }
    };

    const response = await this.api.post('/', data, config);
    return response.data;
  }

  // Update class
  async update(id, data) {
    // Add CSRF token only for form submissions
    const config = {
      headers: {
        'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content ||
                      window.axios?.defaults?.headers?.common?.['X-CSRF-TOKEN']
      }
    };

    if (data instanceof FormData) {
      data.append('_method', 'PUT');
      const response = await this.api.post(`/${id}`, data, config);
      return response.data;
    } else {
      const response = await this.api.put(`/${id}`, data, config);
      return response.data;
    }
  }

  // Delete class
  async delete(id) {
    // Add CSRF token for delete operations
    const config = {
      headers: {
        'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content ||
                      window.axios?.defaults?.headers?.common?.['X-CSRF-TOKEN']
      }
    };

    const response = await this.api.delete(`/${id}`, config);
    return response.data;
  }

  // Get statistics
  async getStatistics(params = {}) {
    const response = await this.api.get('/statistics', { params });
    return response.data;
  }

  // Get available classes
  async getAvailable(params = {}) {
    const response = await this.api.get('/available', { params });
    return response.data;
  }

  // Get trashed classes
  async getTrashed(params = {}) {
    const response = await this.api.get('/trashed', { params });
    return response.data;
  }

  // Restore class
  async restore(id) {
    // Add CSRF token for restore operations
    const config = {
      headers: {
        'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content ||
                      window.axios?.defaults?.headers?.common?.['X-CSRF-TOKEN']
      }
    };

    const response = await this.api.post(`/${id}/restore`, {}, config);
    return response.data;
  }

  // Force delete class
  async forceDelete(id) {
    // Add CSRF token for permanent delete operations
    const config = {
      headers: {
        'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content ||
                      window.axios?.defaults?.headers?.common['X-CSRF-TOKEN']
      }
    };

    const response = await this.api.delete(`/${id}/force-delete`, config);
    return response.data;
  }

  // Bulk restore classes
  async bulkRestore(data) {
    // Add CSRF token for bulk operations
    const config = {
      headers: {
        'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content ||
                      window.axios?.defaults?.headers?.common['X-CSRF-TOKEN']
      }
    };

    const response = await this.api.post('/bulk-restore', data, config);
    return response.data;
  }

  // Bulk force delete classes
  async bulkForceDelete(data) {
    // Add CSRF token for bulk operations
    const config = {
      headers: {
        'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content ||
                      window.axios?.defaults?.headers?.common['X-CSRF-TOKEN']
      }
    };

    const payload = { class_ids: data.class_ids || data.ids || data };
    const response = await this.api.post('/bulk-force-delete', payload, config);
    return response.data;
  }

  // Get students in a class
  async getClassStudents(classId, params = {}) {
    const response = await this.api.get(`/${classId}/students`, { params });
    return response.data;
  }

  // Enroll students in a class
  async enrollStudents(classId, data) {
    // Add CSRF token for student operations
    const config = {
      headers: {
        'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content ||
                      window.axios?.defaults?.headers?.common?.['X-CSRF-TOKEN']
      }
    };

    const response = await this.api.post(`/${classId}/enroll-students`, data, config);
    return response.data;
  }

  // Remove student from class
  async removeStudent(classId, studentId, notes = null) {
    // Add CSRF token for student operations
    const config = {
      headers: {
        'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content ||
                      window.axios?.defaults?.headers?.common?.['X-CSRF-TOKEN']
      }
    };

    const data = {
      student_id: studentId,
      notes: notes
    };

    const response = await this.api.post(`/${classId}/remove-student`, data, config);
    return response.data;
  }

  // Transfer student to another class
  async transferStudent(classId, data) {
    // Add CSRF token for student operations
    const config = {
      headers: {
        'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content ||
                      window.axios?.defaults?.headers?.common?.['X-CSRF-TOKEN']
      }
    };

    const response = await this.api.post(`/${classId}/transfer-student`, data, config);
    return response.data;
  }

  // Update student enrollment status
  async updateStudentStatus(classId, data) {
    // Add CSRF token for student operations
    const config = {
      headers: {
        'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content ||
                      window.axios?.defaults?.headers?.common?.['X-CSRF-TOKEN']
      }
    };

    const response = await this.api.post(`/${classId}/update-student-status`, data, config);
    return response.data;
  }

  // Bulk operations
  async bulkUpdate(data) {
    // Add CSRF token for bulk operations
    const config = {
      headers: {
        'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content ||
                      window.axios?.defaults?.headers?.common?.['X-CSRF-TOKEN']
      }
    };

    const response = await this.api.post('/bulk-update', data, config);
    return response.data;
  }

  async bulkDelete(data) {
    // Add CSRF token for bulk operations
    const payload = { class_ids: data.class_ids || data.ids || data };
    const config = {
      headers: {
        'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content ||
                      window.axios?.defaults?.headers?.common?.['X-CSRF-TOKEN']
      }
    };

    const response = await this.api.post('/bulk-delete', payload, config);
    return response.data;
  }

  // Generate class codes for batch
  async generateClassCodes(data) {
    // Add CSRF token for operations that modify data
    const config = {
      headers: {
        'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content ||
                      window.axios?.defaults?.headers?.common?.['X-CSRF-TOKEN']
      }
    };

    const response = await this.api.post('/generate-codes', data, config);
    return response.data;
  }

  // Auto-enroll students
  async autoEnrollStudents(data) {
    // Add CSRF token for operations that modify data
    const config = {
      headers: {
        'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content ||
                      window.axios?.defaults?.headers?.common?.['X-CSRF-TOKEN']
      }
    };

    const response = await this.api.post('/auto-enroll', data, config);
    return response.data;
  }
}

export default new ClassService();