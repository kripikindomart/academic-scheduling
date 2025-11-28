import { useToastStore } from '../stores/toast';

export const errorHandler = {
  install(app) {
    // Global error handler for Vue
    app.config.errorHandler = (error, vm, info) => {
      const toastStore = useToastStore();
      console.error('Global Vue Error:', error, info);
      toastStore.handleError(error, `Component: ${vm?.$options.name || 'Unknown'}`);
    };

    // Global warning handler
    app.config.warnHandler = (msg, vm, trace) => {
      console.warn('Global Vue Warning:', msg, trace);
    };

    // Add global $toast property
    app.config.globalProperties.$toast = useToastStore();

    // Provide globally
    app.provide('toast', useToastStore());
  }
};

// Utility function for error handling
export const handleAsyncError = async (asyncFn, context = '') => {
  try {
    return await asyncFn();
  } catch (error) {
    const toastStore = useToastStore();
    toastStore.handleError(error, context);
    throw error; // Re-throw if caller needs to handle it
  }
};

// Response error handler for API calls
export const handleApiResponse = (response, successMessage = null) => {
  const toastStore = useToastStore();

  if (response.data?.success === false) {
    // API returned error but with 200 status
    const error = {
      userMessage: response.data?.message || 'Operation failed',
      errorType: 'API_ERROR'
    };
    toastStore.handleError(error);
    return false;
  }

  if (successMessage || response.data?.message) {
    toastStore.handleSuccess(response, successMessage);
  }

  return true;
};