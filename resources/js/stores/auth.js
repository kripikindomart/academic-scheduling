import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    userRole: (state) => {
      // Check if user has roles and get the first role name
      if (state.user?.roles && state.user.roles.length > 0) {
        return state.user.roles[0].name?.toLowerCase();
      }
      return 'user'; // Default role
    },
  },

  actions: {
    async login(credentials) {
      this.loading = true;
      this.error = null;

      try {
        console.log('Attempting login with:', credentials);

        // First get CSRF token
        await axios.get('/sanctum/csrf-cookie');

        const response = await axios.post('/api/auth/login', credentials);
        console.log('API Response received:', response);

        // Check if we have a response with data
        if (!response || !response.data) {
          throw new Error('No response data received from server');
        }

        const data = response.data;
        console.log('Response data:', data);

        // Extract token and user from response
        if (!data.token) {
          throw new Error('No token in response');
        }

        this.token = data.token;
        this.user = data.user;

        localStorage.setItem('token', this.token);

        // Set default axios header
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;

        console.log('Login successful, user:', this.user);
        console.log('Token stored:', this.token);
        console.log('User role:', this.userRole);

        return { success: true, user: this.user };
      } catch (error) {
        console.error('Login error:', error);
        console.error('Error details:', {
          message: error.message,
          response: error.response?.data,
          status: error.response?.status
        });

        this.error = error.response?.data?.message || error.message || 'Login failed';
        return { success: false, error: this.error };
      } finally {
        this.loading = false;
      }
    },

    async logout() {
      try {
        await axios.post('/api/auth/logout');
      } catch (error) {
        console.error('Logout error:', error);
      } finally {
        this.token = null;
        this.user = null;
        localStorage.removeItem('token');
        delete axios.defaults.headers.common['Authorization'];
      }
    },

    async checkAuth() {
      if (!this.token) return;

      try {
        const response = await axios.get('/api/auth/user');
        this.user = response.data.data;
      } catch (error) {
        // Token is invalid, clear auth
        this.token = null;
        this.user = null;
        localStorage.removeItem('token');
        delete axios.defaults.headers.common['Authorization'];
      }
    },

    async register(userData) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.post('/api/auth/register', userData);

        return { success: true, message: 'Registration successful' };
      } catch (error) {
        this.error = error.response?.data?.message || 'Registration failed';
        return { success: false, error: this.error };
      } finally {
        this.loading = false;
      }
    },

    clearError() {
      this.error = null;
    },
  },
});