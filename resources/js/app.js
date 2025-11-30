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
            path: '/academic-years',
            name: 'academic-years',
            component: () => import('./views/AcademicYears.vue'),
            meta: { requiresAuth: true },
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

// Simple navigation guard for auth
router.beforeEach(async (to, from, next) => {
    const token = localStorage.getItem('token');

    // Redirect authenticated users from guest pages
    if (to.meta.guest && token) {
        return next('/dashboard');
    }

    // Redirect unauthenticated users from protected pages
    if (to.meta.requiresAuth && !token) {
        return next('/login');
    }

    // For role-based routes, do a simple check - let component handle detailed auth
    if (to.meta.roles && token) {
        // Just proceed to route, component will handle role checking
        next();
        return;
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