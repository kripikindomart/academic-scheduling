import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
  state: () => {
    const token = localStorage.getItem('token') || null;

    // Set axios default header if token exists
    if (token) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    }

    return {
      user: null,
      token: token,
      loading: false,
      error: null,
    };
  },

  getters: {
    isAuthenticated: (state) => !!state.token,
    userRole: (state) => {
      // Use Spatie Laravel Permission roles
      if (state.user?.roles && state.user.roles.length > 0) {
        const primaryRole = state.user.roles[0].name;
        return primaryRole.toLowerCase();
      }
      return 'user'; // Default role if no roles assigned
    },

    isAdmin: (state) => {
      // Check if user has admin role or super admin role
      if (state.user?.roles && state.user.roles.length > 0) {
        const hasAdminRole = state.user.roles.some(role =>
          ['admin', 'super admin', 'super_admin'].includes(role.name.toLowerCase())
        );
        console.log('Admin check:', {
          userRoles: state.user.roles.map(r => r.name),
          hasAdminRole
        });
        return hasAdminRole;
      }
      console.log('No roles found for user');
      return false;
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
        console.log('User roles:', this.user?.roles?.map(r => r.name));
        console.log('Is admin:', this.isAdmin);

        // Force flush Spatie permission cache (if method exists)
        if (this.user && typeof this.user.flushCache === 'function') {
            this.user.flushCache();
        }

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
      if (!this.token) {
        console.log('No token found during checkAuth');
        return;
      }

      try {
        // Set authorization header before making request
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;

        console.log('Checking auth with token:', this.token);
        const response = await axios.get('/api/auth/user');
        this.user = response.data.data;
        console.log('Auth check successful, user loaded:', this.user);

        // Force flush Spatie permission cache to ensure roles are loaded
        if (this.user && typeof this.user.flushCache === 'function') {
            this.user.flushCache();
        }
      } catch (error) {
        console.error('Auth check failed:', error);
        console.error('Error response:', error.response?.data);

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