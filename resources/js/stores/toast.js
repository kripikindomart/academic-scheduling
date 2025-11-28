import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useToastStore = defineStore('toast', () => {
  const toasts = ref([]);
  let toastId = 0;

  const showToast = (title, description = '', type = 'info', duration = 5000) => {
    const id = ++toastId;

    const toast = {
      id,
      title,
      description,
      type, // success, error, warning, info
      duration,
      show: false
    };

    toasts.value.push(toast);

    // Auto remove after duration
    if (duration > 0) {
      setTimeout(() => {
        removeToast(id);
      }, duration);
    }

    return id;
  };

  const removeToast = (id) => {
    const index = toasts.value.findIndex(toast => toast.id === id);
    if (index > -1) {
      toasts.value.splice(index, 1);
    }
  };

  const clearAllToasts = () => {
    toasts.value = [];
  };

  // Convenience methods
  const success = (title, description = '', duration = 3000) => {
    return showToast(title, description, 'success', duration);
  };

  const error = (title, description = '', duration = 0) => {
    return showToast(title, description, 'error', duration);
  };

  const warning = (title, description = '', duration = 5000) => {
    return showToast(title, description, 'warning', duration);
  };

  const info = (title, description = '', duration = 5000) => {
    return showToast(title, description, 'info', duration);
  };

  // Global error handler
  const handleError = (error, context = '') => {
    console.error('Error in', context, error);

    let title = 'Terjadi Kesalahan';
    let description = '';
    let duration = 0;

    // Handle different error types
    if (error?.errorType === 'PERMISSION_ERROR') {
      title = 'Akses Ditolak';
      description = error.userMessage || 'Anda tidak memiliki izin untuk melakukan aksi ini.';
    } else if (error?.errorType === 'VALIDATION_ERROR') {
      title = 'Validasi Gagal';
      if (error.validationErrors) {
        const errorMessages = Object.values(error.validationErrors).flat();
        description = errorMessages.join(', ');
      } else {
        description = error.userMessage || 'Silakan periksa kembali input Anda.';
      }
    } else if (error?.errorType === 'SERVER_ERROR') {
      title = 'Kesalahan Server';
      description = error.userMessage || 'Terjadi kesalahan pada server. Silakan coba lagi.';
    } else if (error?.response?.status === 404) {
      title = 'Data Tidak Ditemukan';
      description = 'Data yang Anda cari tidak ditemukan.';
      duration = 5000;
    } else if (error?.response?.status >= 500) {
      title = 'Kesalahan Server Internal';
      description = 'Terjadi kesalahan pada server. Silakan hubungi administrator.';
    } else if (error?.response?.status === 429) {
      title = 'Terlalu Banyak Permintaan';
      description = 'Silakan tunggu beberapa saat sebelum mencoba lagi.';
      duration = 5000;
    } else if (error?.code === 'NETWORK_ERROR' || !error?.response) {
      title = 'Koneksi Error';
      description = 'Tidak dapat terhubung ke server. Silakan periksa koneksi internet Anda.';
    } else {
      title = 'Terjadi Kesalahan';
      description = error?.userMessage || error?.message || 'Terjadi kesalahan yang tidak diketahui.';
    }

    // Add context if provided
    if (context) {
      description = description ? `[${context}] ${description}` : context;
    }

    // Handle validation errors from Laravel
    if (error?.response?.data?.errors) {
      title = 'Validasi Gagal';
      const errorMessages = Object.values(error.response.data.errors).flat();
      description = errorMessages.join(', ');
    }
    // Handle duplicate entry error specifically
    else if (error?.response?.data?.message && error.response.data.message.includes('Duplicate entry')) {
      title = 'Data Duplikat';
      description = 'Data yang Anda masukkan sudah ada di sistem. Silakan periksa kembali input Anda.';
    }

    showToast(title, description, 'error', duration);
  };

  // Handle API response success
  const handleSuccess = (response, message = null) => {
    const defaultMessage = 'Operasi berhasil dilakukan.';
    const title = message || response.data?.message || defaultMessage;
    return success(title);
  };

  // Add toast method for compatibility
  const addToast = (toast) => {
    return showToast(toast.title, toast.message || toast.description, toast.type, toast.duration);
  };

  return {
    toasts,
    showToast,
    removeToast,
    clearAllToasts,
    success,
    error,
    warning,
    info,
    handleError,
    handleSuccess,
    addToast
  };
});