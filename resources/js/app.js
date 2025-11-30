import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import { createPinia } from 'pinia';
import { useAuthStore } from './stores/auth';

// Import plugins - disabled for now
// import { errorHandler } from './plugins/errorHandler';

// Import components
import App from './App.vue';

// Create Vue app
const app = createApp(App);

// Create router
const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            name: 'home',
            component: () => import('./views/Home.vue'),
        },
        {
            path: '/login',
            name: 'login',
            component: () => import('./views/Login.vue'),
            meta: { guest: true },
        },
        {
            path: '/dashboard',
            name: 'dashboard',
            component: () => import('./views/DashboardFixed.vue'),
            meta: { requiresAuth: true },
        },
        {
            path: '/schedule',
            name: 'schedule',
            component: () => import('./views/Schedule.vue'),
            meta: { requiresAuth: true },
        },
        {
            path: '/courses',
            name: 'courses',
            component: () => import('./views/CoursesSimple.vue'),
            meta: { requiresAuth: true },
        },
        {
            path: '/students',
            name: 'students',
            component: () => import('./views/Students.vue'),
            meta: { requiresAuth: true },
        },
        {
            path: '/program-studies',
            name: 'program-studies',
            component: () => import('./views/ProgramStudies.vue'),
            meta: { requiresAuth: true },
        },
        {
            path: '/lecturers',
            name: 'lecturers',
            component: () => import('./views/Lecturers.vue'),
            meta: { requiresAuth: true },
        },
        {
            path: '/rooms',
            name: 'rooms',
            component: () => import('./views/Rooms.vue'),
            meta: { requiresAuth: true },
        },
        {
            path: '/room-dashboard',
            name: 'room-dashboard',
            component: () => import('./views/RoomDashboard.vue'),
            meta: { requiresAuth: true },
        },
        {
            path: '/class-dashboard',
            name: 'class-dashboard',
            component: () => import('./views/ClassDashboard.vue'),
            meta: { requiresAuth: true },
        },
                {
            path: '/users',
            name: 'users',
            component: () => import('./views/Users.vue'),
            meta: { requiresAuth: true, roles: ['admin', 'super admin'] },
        },
        {
            path: '/settings',
            name: 'settings',
            component: () => import('./views/Settings.vue'),
            meta: { requiresAuth: true },
        },
        {
            path: '/profile',
            name: 'profile',
            component: () => import('./views/Profile.vue'),
            meta: { requiresAuth: true },
        },
    ],
});

// Enhanced navigation guard for auth
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    const token = localStorage.getItem('token');

    // Initialize auth store if token exists but user is not loaded
    if (token && !authStore.user) {
        try {
            await authStore.checkAuth();
        } catch (error) {
            console.error('Auth check failed:', error);
        }
    }

    const isAuthenticated = authStore.isAuthenticated;
    const userRole = authStore.userRole?.toLowerCase();

    // Redirect authenticated users from guest pages
    if (to.meta.guest && isAuthenticated) {
        return next('/dashboard');
    }

    // Redirect unauthenticated users from protected pages
    if (to.meta.requiresAuth && !isAuthenticated) {
        return next('/login');
    }

    // Check role-based access
    if (to.meta.roles && isAuthenticated) {
        const hasRequiredRole = to.meta.roles.some(role =>
            userRole === role.toLowerCase() || userRole === role
        );

        if (!hasRequiredRole) {
            return next('/dashboard'); // Redirect to dashboard if user doesn't have required role
        }
    }

    next();
});

// Create pinia store
const pinia = createPinia();

// Use plugins
app.use(router);
app.use(pinia);
// app.use(errorHandler); // disabled

// Mount app
app.mount('#app');