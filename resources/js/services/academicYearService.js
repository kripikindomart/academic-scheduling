import axios from 'axios';

const API_BASE_URL = '/api/academic-years';

class AcademicYearService {
  constructor() {
    this.api = axios.create({
      baseURL: API_BASE_URL,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
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

    // Handle response errors
    this.api.interceptors.response.use(
      (response) => response,
      (error) => {
        if (error.response?.status === 401) {
          localStorage.removeItem('token');
          localStorage.removeItem('user');
          window.location.href = '/login';
        }
        return Promise.reject(error);
      }
    );
  }

  /**
   * Get all academic years
   */
  async getAll(params = {}) {
    try {
      const response = await this.api.get('/', { params });
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      };
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch academic years',
        errors: error.response?.data?.errors
      };
    }
  }

  /**
   * Get active academic year
   */
  async getActive() {
    try {
      const response = await this.api.get('/active');
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      };
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch active academic year',
        errors: error.response?.data?.errors
      };
    }
  }

  /**
   * Get academic year statistics
   */
  async getStatistics() {
    try {
      console.log('📊 Fetching academic year statistics from:', this.api.defaults.baseURL + '/statistics');
      const response = await this.api.get('/statistics');
      console.log('✅ Statistics response:', response.data);
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      };
    } catch (error) {
      console.error('❌ Statistics error:', error);
      console.error('❌ Error response:', error.response?.data);
      console.error('❌ Error status:', error.response?.status);
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch academic year statistics',
        errors: error.response?.data?.errors
      };
    }
  }

  /**
   * Set active academic year
   */
  async setActive(academicYearId) {
    try {
      const response = await this.api.post('/set-active', {
        academic_year_id: academicYearId
      });
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      };
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to set active academic year',
        errors: error.response?.data?.errors,
        errorType: error.response?.data?.errorType,
        userMessage: error.response?.data?.userMessage
      };
    }
  }

  /**
   * Duplicate academic year
   */
  async duplicate(id) {
    try {
      const response = await this.api.post(`/${id}/duplicate`);
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      };
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to duplicate academic year',
        errors: error.response?.data?.errors
      };
    }
  }

  /**
   * Toggle active status
   */
  async toggleActive(id) {
    try {
      const response = await this.api.post(`/${id}/toggle-active`);
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      };
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to toggle academic year status',
        errors: error.response?.data?.errors
      };
    }
  }

  /**
   * Create academic year
   */
  async create(data) {
    try {
      const response = await this.api.post('/', data);
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      };
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to create academic year',
        errors: error.response?.data?.errors
      };
    }
  }

  /**
   * Update academic year
   */
  async update(id, data) {
    try {
      const response = await this.api.put(`/${id}`, data);
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      };
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to update academic year',
        errors: error.response?.data?.errors
      };
    }
  }

  /**
   * Delete academic year
   */
  async delete(id) {
    try {
      const response = await this.api.delete(`/${id}`);
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      };
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to delete academic year',
        errors: error.response?.data?.errors
      };
    }
  }

  /**
   * Get academic year by ID
   */
  async getById(id) {
    try {
      const response = await this.api.get(`/${id}`);
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      };
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch academic year',
        errors: error.response?.data?.errors
      };
    }
  }
}

// Create singleton instance
const academicYearService = new AcademicYearService();

export default academicYearService;