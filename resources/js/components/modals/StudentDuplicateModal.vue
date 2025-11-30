<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" aria-hidden="true" @click="closeModal"></div>

            <!-- Center the modal -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div class="inline-block w-full max-w-3xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl sm:align-middle">

                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">
                                Duplicate Student
                            </h3>
                            <p class="text-sm text-gray-500 mt-0.5">
                                Create a copy of <span class="font-medium text-gray-700">{{ student.name }}</span>
                            </p>
                        </div>
                    </div>
                    <button
                        @click="closeModal"
                        class="p-2 text-gray-400 transition-colors rounded-lg hover:text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Required Fields Section -->
                    <div class="space-y-4">
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                            <h4 class="text-base font-semibold text-gray-900">Required Information</h4>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Student Number -->
                            <div class="space-y-2">
                                <label for="student_number" class="flex items-center text-sm font-medium text-gray-700">
                                    Student Number
                                    <span class="ml-1 text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="student_number"
                                        v-model="formData.student_number"
                                        :class="[
                                            'w-full px-4 py-3 border rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200',
                                            errors.student_number ? 'border-red-500 bg-red-50' : 'border-gray-300'
                                        ]"
                                        placeholder="Enter unique student number"
                                        required
                                        maxlength="20"
                                    >
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                        </svg>
                                    </div>
                                </div>
                                <p v-if="errors.student_number" class="text-sm text-red-600 mt-1">{{ errors.student_number }}</p>
                                <p class="text-xs text-gray-500">Max 20 characters</p>
                            </div>

                            <!-- Full Name -->
                            <div class="space-y-2">
                                <label for="name" class="flex items-center text-sm font-medium text-gray-700">
                                    Full Name
                                    <span class="ml-1 text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="name"
                                        v-model="formData.name"
                                        :class="[
                                            'w-full px-4 py-3 border rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200',
                                            errors.name ? 'border-red-500 bg-red-50' : 'border-gray-300'
                                        ]"
                                        placeholder="Enter student's full name"
                                        required
                                    >
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                </div>
                                <p v-if="errors.name" class="text-sm text-red-600 mt-1">{{ errors.name }}</p>
                            </div>

                            <!-- Email -->
                            <div class="space-y-2">
                                <label for="email" class="flex items-center text-sm font-medium text-gray-700">
                                    Email Address
                                    <span class="ml-1 text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        type="email"
                                        id="email"
                                        v-model="formData.email"
                                        :class="[
                                            'w-full px-4 py-3 border rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200',
                                            errors.email ? 'border-red-500 bg-red-50' : 'border-gray-300'
                                        ]"
                                        placeholder="student@example.com"
                                        required
                                    >
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <p v-if="errors.email" class="text-sm text-red-600 mt-1">{{ errors.email }}</p>
                            </div>

                            <!-- Phone -->
                            <div class="space-y-2">
                                <label for="phone" class="flex items-center text-sm font-medium text-gray-700">
                                    Phone Number
                                </label>
                                <div class="relative">
                                    <input
                                        type="tel"
                                        id="phone"
                                        v-model="formData.phone"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        placeholder="+62 812-3456-7890"
                                        maxlength="50"
                                    >
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                </div>
                                <p v-if="errors.phone" class="text-sm text-red-600 mt-1">{{ errors.phone }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information Section -->
                    <div class="space-y-4">
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                            <h4 class="text-base font-semibold text-gray-900">Additional Information</h4>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <!-- Batch Year -->
                            <div class="space-y-2">
                                <label for="batch_year" class="flex items-center text-sm font-medium text-gray-700">
                                    Batch Year
                                </label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="batch_year"
                                        v-model="formData.batch_year"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        placeholder="YYYY"
                                        maxlength="4"
                                    >
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <p v-if="errors.batch_year" class="text-sm text-red-600 mt-1">{{ errors.batch_year }}</p>
                            </div>

                            <!-- Class -->
                            <div class="space-y-2">
                                <label for="class" class="flex items-center text-sm font-medium text-gray-700">
                                    Class
                                </label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="class"
                                        v-model="formData.class"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        placeholder="e.g., 3A, 2B"
                                        maxlength="50"
                                    >
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <p v-if="errors.class" class="text-sm text-red-600 mt-1">{{ errors.class }}</p>
                            </div>

                            <!-- Gender -->
                            <div class="space-y-2">
                                <label for="gender" class="flex items-center text-sm font-medium text-gray-700">
                                    Gender
                                </label>
                                <div class="relative">
                                    <select
                                        id="gender"
                                        v-model="formData.gender"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 appearance-none"
                                    >
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                <p v-if="errors.gender" class="text-sm text-red-600 mt-1">{{ errors.gender }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Status and Options Section -->
                    <div class="space-y-4">
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <h4 class="text-base font-semibold text-gray-900">Status & Options</h4>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Status -->
                            <div class="space-y-2">
                                <label for="status" class="flex items-center text-sm font-medium text-gray-700">
                                    Status
                                </label>
                                <div class="relative">
                                    <select
                                        id="status"
                                        v-model="formData.status"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 appearance-none"
                                    >
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="graduated">Graduated</option>
                                        <option value="dropped_out">Dropped Out</option>
                                        <option value="on_leave">On Leave</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                <p v-if="errors.status" class="text-sm text-red-600 mt-1">{{ errors.status }}</p>
                            </div>
                        </div>

                        <!-- Optional Settings -->
                        <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                            <div class="space-y-3">
                                <h5 class="text-sm font-semibold text-blue-900 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Optional Settings
                                </h5>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <!-- Create User Account -->
                                    <label class="flex items-center p-3 bg-white rounded-lg cursor-pointer border border-blue-200 hover:bg-blue-50 transition-colors">
                                        <input
                                            type="checkbox"
                                            v-model="formData.create_user_account"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        >
                                        <div class="ml-3">
                                            <span class="text-sm font-medium text-gray-900">Create User Account</span>
                                            <p class="text-xs text-gray-500 mt-0.5">Generate login credentials</p>
                                        </div>
                                    </label>

                                    <!-- Copy Photo -->
                                    <label class="flex items-center p-3 bg-white rounded-lg cursor-pointer border border-blue-200 hover:bg-blue-50 transition-colors">
                                        <input
                                            type="checkbox"
                                            v-model="formData.copy_photo"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        >
                                        <div class="ml-3">
                                            <span class="text-sm font-medium text-gray-900">Copy Photo</span>
                                            <p class="text-xs text-gray-500 mt-0.5">Duplicate profile photo</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end sm:space-x-3 pt-6 border-t border-gray-200">
                        <button
                            type="button"
                            @click="closeModal"
                            :disabled="isSubmitting"
                            class="w-full px-6 py-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed sm:w-auto"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="isSubmitting"
                            class="w-full px-6 py-3 text-base font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 border border-transparent rounded-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed sm:w-auto flex items-center justify-center"
                        >
                            <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-5 h-5 -ml-1 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            {{ isSubmitting ? 'Creating Duplicate...' : 'Create Duplicate' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import { useToastStore } from '../../stores/toast';
import studentService from '../../services/studentService';

const props = defineProps({
    show: {
        type: Boolean,
        required: true
    },
    student: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['close', 'duplicated']);

const toastStore = useToastStore();
const isSubmitting = ref(false);
const errors = reactive({});

const formData = reactive({
    student_number: '',
    name: '',
    email: '',
    phone: '',
    batch_year: '',
    class: '',
    status: 'active',
    gender: '',
    create_user_account: false,
    copy_photo: true
});

const populateForm = (student) => {
    // Generate unique student number
    const originalNumber = student.student_number || '';
    const suffix = '_COPY' + Date.now().toString().slice(-4);
    const maxOriginalLength = 20 - suffix.length;

    formData.student_number = originalNumber.slice(0, maxOriginalLength) + suffix;
    formData.name = student.name || '';
    formData.email = 'copy' + Date.now().toString().slice(-4) + '_' + (student.email || '');
    formData.phone = student.phone || '';
    formData.batch_year = student.batch_year || new Date().getFullYear().toString();
    formData.class = student.class || '';
    formData.status = 'active'; // Default to active for new duplicates
    formData.gender = student.gender || '';
    formData.create_user_account = false;
    formData.copy_photo = true;
};

// Watch for student changes to populate form
watch(() => props.student, (newStudent) => {
    if (newStudent) {
        populateForm(newStudent);
    }
}, { immediate: true });

const closeModal = () => {
    emit('close');
    resetForm();
};

const resetForm = () => {
    Object.keys(errors).forEach(key => delete errors[key]);
    isSubmitting.value = false;
};

const validateForm = () => {
    Object.keys(errors).forEach(key => delete errors[key]);

    let isValid = true;

    // Student number validation
    if (!formData.student_number.trim()) {
        errors.student_number = 'Student number is required';
        isValid = false;
    } else if (formData.student_number.length > 20) {
        errors.student_number = 'Student number cannot exceed 20 characters';
        isValid = false;
    }

    // Name validation
    if (!formData.name.trim()) {
        errors.name = 'Name is required';
        isValid = false;
    }

    // Email validation
    if (!formData.email.trim()) {
        errors.email = 'Email is required';
        isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
        errors.email = 'Invalid email format';
        isValid = false;
    }

    // Batch year validation (if provided)
    if (formData.batch_year && !/^\d{4}$/.test(formData.batch_year)) {
        errors.batch_year = 'Batch year must be a 4-digit year';
        isValid = false;
    }

    return isValid;
};

const handleSubmit = async () => {
    if (!validateForm()) {
        return;
    }

    isSubmitting.value = true;

    try {
        const duplicateData = {
            ...formData,
            original_student_id: props.student.id,
            program_study_id: props.student.program_study_id // Keep the same program study
        };

        const response = await studentService.duplicate(props.student.id, duplicateData);

        if (response.success) {
            toastStore.success('Student duplicated successfully!');
            emit('duplicated', response.data);
            closeModal();
        } else {
            toastStore.error(response.message || 'Failed to duplicate student');
        }
    } catch (error) {
        console.error('Duplicate error:', error);

        if (error.response?.data?.errors) {
            Object.assign(errors, error.response.data.errors);
            toastStore.error('Please correct the errors in the form');
        } else {
            toastStore.error(error.response?.data?.message || 'Failed to duplicate student');
        }
    } finally {
        isSubmitting.value = false;
    }
};

onMounted(() => {
    if (props.student) {
        populateForm(props.student);
    }
});
</script>