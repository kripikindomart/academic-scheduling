<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">WhatsApp Gateway</h1>
            <p class="mt-2 text-gray-600">Manage WhatsApp connections and send notifications</p>
          </div>
          <div class="flex space-x-3">
            <button
              @click="showProcessLogs = !showProcessLogs"
              class="inline-flex items-center px-4 py-2 border border-indigo-300 rounded-md shadow-sm text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <BarChart3 class="w-4 h-4 mr-2" />
              {{ showProcessLogs ? 'Hide' : 'Show' }} Logs
            </button>
            <button
              @click="downloadLogs"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              :disabled="processLogs.length === 0"
            >
              <MessageSquare class="w-4 h-4 mr-2" />
              Download Logs
            </button>
            <button
              @click="processLogs = []"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              :disabled="processLogs.length === 0"
            >
              <Trash2 class="w-4 h-4 mr-2" />
              Clear Logs
            </button>
            <button
              @click="refreshSessions"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <RefreshCw class="w-4 h-4 mr-2" />
              Refresh
            </button>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
              <MessageSquare class="h-6 w-6 text-blue-600" />
            </div>
            <div class="ml-5">
              <p class="text-sm font-medium text-gray-500">Total Messages</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statistics.total || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
              <CheckCircle class="h-6 w-6 text-green-600" />
            </div>
            <div class="ml-5">
              <p class="text-sm font-medium text-gray-500">Sent Successfully</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statistics.sent || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
              <XCircle class="h-6 w-6 text-red-600" />
            </div>
            <div class="ml-5">
              <p class="text-sm font-medium text-gray-500">Failed</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statistics.failed || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
              <Clock class="h-6 w-6 text-yellow-600" />
            </div>
            <div class="ml-5">
              <p class="text-sm font-medium text-gray-500">Pending</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statistics.pending || 0 }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Process Logs -->
      <div v-if="showProcessLogs" class="mb-8">
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-900">Process Logs</h2>
            <span class="text-sm text-gray-500">{{ processLogs.length }} logs</span>
          </div>
          <div class="p-6">
            <div v-if="processLogs.length === 0" class="text-center text-gray-500 py-8">
              <BarChart3 class="h-12 w-12 mx-auto text-gray-400 mb-3" />
              <p>No logs available. Start a WhatsApp connection to see detailed process logs.</p>
            </div>
            <div v-else class="space-y-2 max-h-96 overflow-y-auto">
              <div
                v-for="log in processLogs"
                :key="log.id"
                class="p-3 rounded-lg border-l-4 text-sm font-mono"
                :class="{
                  'border-green-500 bg-green-50': log.type === 'success',
                  'border-red-500 bg-red-50': log.type === 'error',
                  'border-yellow-500 bg-yellow-50': log.type === 'warning',
                  'border-blue-500 bg-blue-50': log.type === 'info'
                }"
              >
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <span class="text-xs text-gray-500 mr-3">{{ log.timestamp }}</span>
                    <span v-html="log.message"></span>
                  </div>
                  <span class="ml-3 text-xs px-2 py-1 rounded" :class="{
                    'bg-green-200 text-green-800': log.type === 'success',
                    'bg-red-200 text-red-800': log.type === 'error',
                    'bg-yellow-200 text-yellow-800': log.type === 'warning',
                    'bg-blue-200 text-blue-800': log.type === 'info'
                  }">{{ log.type }}</span>
                </div>
                <div v-if="log.data" class="mt-2 text-xs text-gray-600 bg-gray-100 p-2 rounded">
                  <pre>{{ JSON.stringify(log.data, null, 2) }}</pre>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sessions Management -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900">WhatsApp Sessions</h2>
            </div>
            <div class="p-6">
              <!-- Add Session -->
              <div class="mb-6">
                <div class="flex items-center space-x-3">
                  <input
                    v-model="newSessionId"
                    type="text"
                    placeholder="Enter session ID (optional)"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                  <button
                    @click="initializeSession"
                    :disabled="loading.initialize"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                  >
                    <Plus class="w-4 h-4 mr-2" />
                    {{ loading.initialize ? 'Initializing...' : 'Add Session' }}
                  </button>
                </div>
              </div>

              <!-- Sessions List -->
              <div class="space-y-4">
                <div
                  v-for="session in sessions"
                  :key="session.session_id"
                  class="border border-gray-200 rounded-lg p-4"
                >
                  <div class="flex items-center justify-between">
                    <div class="flex-1">
                      <div class="flex items-center">
                        <h3 class="text-sm font-medium text-gray-900">{{ session.session_id }}</h3>
                        <span
                          :class="[
                            'ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                            getStatusColor(session.status)
                          ]"
                        >
                          {{ getStatusText(session.status) }}
                        </span>
                        <span
                          v-if="session.is_active"
                          class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                        >
                          Active
                        </span>
                      </div>
                      <div class="mt-1 text-sm text-gray-500">
                        <p>Phone: {{ session.phone_number || 'Not connected' }}</p>
                        <p>Messages: {{ session.messages_count || 0 }}</p>
                        <p v-if="session.last_activity">
                          Last activity: {{ formatDate(session.last_activity) }}
                        </p>
                      </div>
                    </div>
                    <div class="flex items-center space-x-2">
                      <button
                        v-if="session.status === 'disconnected' || session.status === 'qr_generated'"
                        @click="connectWithQR(session.session_id)"
                        :disabled="loading.connect"
                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                      >
                        <QrCode class="w-3 h-3 mr-1" />
                        Connect
                      </button>
                      <button
                        v-if="session.status === 'qr_generated'"
                        @click="refreshQRCode(session.session_id)"
                        :disabled="loading.refreshQR"
                        class="inline-flex items-center px-3 py-1.5 border border-blue-300 rounded-md text-xs font-medium text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                      >
                        <RefreshCw class="w-3 h-3 mr-1" />
                        Refresh QR
                      </button>
                      <button
                        v-if="session.status === 'connected'"
                        @click="testConnection(session.session_id)"
                        :disabled="loading.test"
                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                      >
                        <Zap class="w-3 h-3 mr-1" />
                        Test
                      </button>
                      <button
                        v-if="session.status === 'connected'"
                        @click="disconnectSession(session.session_id)"
                        :disabled="loading.disconnect"
                        class="inline-flex items-center px-3 py-1.5 border border-red-300 rounded-md text-xs font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
                      >
                        <Power class="w-3 h-3 mr-1" />
                        Disconnect
                      </button>
                      <button
                        @click="deleteSession(session.session_id)"
                        :disabled="loading.delete"
                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 disabled:opacity-50"
                      >
                        <Trash2 class="w-3 h-3 mr-1" />
                        Delete
                      </button>
                    </div>
                  </div>
                </div>

                <div v-if="sessions.length === 0" class="text-center py-8 text-gray-500">
                  No WhatsApp sessions configured
                </div>
              </div>
            </div>
          </div>

          <!-- Send Message -->
          <div class="bg-white rounded-lg shadow mt-8">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900">Send Message</h2>
            </div>
            <div class="p-6">
              <form @submit.prevent="sendMessage" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Recipient Phone Number</label>
                  <input
                    v-model="messageForm.to"
                    type="tel"
                    placeholder="e.g., 08123456789"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    required
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                  <textarea
                    v-model="messageForm.message"
                    rows="4"
                    placeholder="Enter your message..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    required
                  ></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Message Type</label>
                  <select
                    v-model="messageForm.type"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="text">Text Message</option>
                    <option value="image">Image Message</option>
                    <option value="document">Document Message</option>
                  </select>
                </div>
                <div class="flex justify-end">
                  <button
                    type="submit"
                    :disabled="loading.sendMessage"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                  >
                    <Send class="w-4 h-4 mr-2" />
                    {{ loading.sendMessage ? 'Sending...' : 'Send Message' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Recent Notifications -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900">Recent Notifications</h2>
            </div>
            <div class="p-6">
              <div class="space-y-4">
                <div
                  v-for="notification in recentNotifications"
                  :key="notification.id"
                  class="border-l-4 border-blue-400 bg-blue-50 p-3"
                >
                  <div class="flex">
                    <div class="flex-1">
                      <p class="text-sm font-medium text-gray-900">
                        {{ notification.recipient }}
                      </p>
                      <p class="text-sm text-gray-600">
                        {{ notification.message_preview }}
                      </p>
                      <p class="text-xs text-gray-500 mt-1">
                        {{ formatDate(notification.created_at) }}
                      </p>
                    </div>
                    <div class="ml-2">
                      <span
                        :class="[
                          'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                          getNotificationStatusColor(notification.status)
                        ]"
                      >
                        {{ notification.status }}
                      </span>
                    </div>
                  </div>
                </div>

                <div v-if="recentNotifications.length === 0" class="text-center py-4 text-gray-500">
                  No recent notifications
                </div>
              </div>

              <div class="mt-4">
                <button
                  @click="loadRecentNotifications"
                  class="w-full text-center text-sm text-indigo-600 hover:text-indigo-500 font-medium"
                >
                  View all notifications
                </button>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white rounded-lg shadow mt-8">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900">Quick Actions</h2>
            </div>
            <div class="p-6">
              <div class="space-y-3">
                <button
                  @click="showCustomNotificationModal = true"
                  class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <MessageSquarePlus class="w-4 h-4 mr-2" />
                  Send Custom Notification
                </button>
                <button
                  @click="refreshStatistics"
                  :disabled="loading.statistics"
                  class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                >
                  <BarChart3 class="w-4 h-4 mr-2" />
                  {{ loading.statistics ? 'Refreshing...' : 'Refresh Statistics' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- QR Code Modal -->
    <div
      v-if="qrModal.show"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
      @click="qrModal.show = false"
    >
      <div class="bg-white rounded-lg p-6 max-w-md w-full" @click.stop>
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900">Scan QR Code</h3>
          <button
            @click="qrModal.show = false"
            class="text-gray-400 hover:text-gray-500"
          >
            <X class="h-6 w-6" />
          </button>
        </div>
        <div class="text-center">
          <div v-if="qrModal.qrCode" class="mb-4">
            <img :src="qrModal.qrCode" alt="WhatsApp QR Code" class="mx-auto" />
          </div>
          <p class="text-sm text-gray-600 mb-4">
            Scan this QR code with WhatsApp to connect your session.
          </p>
          <div class="text-xs text-gray-500">
            QR code expires in {{ qrModal.expiresIn }} seconds
          </div>
        </div>
      </div>
    </div>

    <!-- Custom Notification Modal -->
    <div
      v-if="showCustomNotificationModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
      @click="showCustomNotificationModal = false"
    >
      <div class="bg-white rounded-lg p-6 max-w-md w-full" @click.stop>
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900">Send Custom Notification</h3>
          <button
            @click="showCustomNotificationModal = false"
            class="text-gray-400 hover:text-gray-500"
          >
            <X class="h-6 w-6" />
          </button>
        </div>
        <form @submit.prevent="sendCustomNotification" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input
              v-model="customNotificationForm.title"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              required
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
            <textarea
              v-model="customNotificationForm.message"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              required
            ></textarea>
          </div>
          <div class="flex justify-end space-x-3">
            <button
              type="button"
              @click="showCustomNotificationModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="loading.customNotification"
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
            >
              {{ loading.customNotification ? 'Sending...' : 'Send to All' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import {
  RefreshCw,
  Plus,
  QrCode,
  Zap,
  Power,
  Send,
  MessageSquarePlus,
  BarChart3,
  MessageSquare,
  CheckCircle,
  XCircle,
  Clock,
  Trash2,
  X
} from 'lucide-vue-next';

// State
const sessions = ref([]);
const statistics = ref({});
const recentNotifications = ref([]);
const processLogs = ref([]); // For detailed process logging
const showProcessLogs = ref(false);

// Active intervals tracking for cleanup
const activeIntervals = ref([]);
const loading = reactive({
  initialize: false,
  connect: false,
  refreshQR: false,
  disconnect: false,
  test: false,
  sendMessage: false,
  statistics: false,
  customNotification: false,
  delete: false,
});

const newSessionId = ref('');
const qrModal = reactive({
  show: false,
  qrCode: null,
  expiresIn: 60,
});

const showCustomNotificationModal = ref(false);

const messageForm = reactive({
  to: '',
  message: '',
  type: 'text',
});

const customNotificationForm = reactive({
  title: '',
  message: '',
});

// Logging function
function addLog(message, type = 'info', data = null) {
  const timestamp = new Date().toLocaleTimeString();
  const log = {
    id: Date.now() + Math.random(),
    timestamp,
    message,
    type,
    data
  };

  processLogs.value.unshift(log);

  // Keep only last 50 logs
  if (processLogs.value.length > 50) {
    processLogs.value = processLogs.value.slice(0, 50);
  }

  // Also log to console
  console.log(`[${timestamp}] ${message}`, data);
}

// Methods
async function loadSessions() {
  try {
    const response = await fetch('/api/whatsapp/sessions', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Accept': 'application/json',
      },
    });
    const data = await response.json();
    sessions.value = data.sessions || [];
  } catch (error) {
    console.error('Failed to load sessions:', error);
  }
}

async function loadStatistics() {
  try {
    loading.statistics = true;
    const response = await fetch('/api/whatsapp/notifications/statistics', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Accept': 'application/json',
      },
    });
    const data = await response.json();
    statistics.value = data.statistics || {};
  } catch (error) {
    console.error('Failed to load statistics:', error);
  } finally {
    loading.statistics = false;
  }
}

async function loadRecentNotifications() {
  try {
    const response = await fetch('/api/whatsapp/notifications/recent?limit=10', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Accept': 'application/json',
      },
    });
    const data = await response.json();
    recentNotifications.value = data.notifications || [];
  } catch (error) {
    console.error('Failed to load recent notifications:', error);
  }
}

async function initializeSession() {
  try {
    loading.initialize = true;
    const response = await fetch('/api/whatsapp/sessions/initialize', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        session_id: newSessionId.value || undefined,
      }),
    });

    const data = await response.json();
    if (data.success) {
      await loadSessions();
      newSessionId.value = '';
      // Show success message
    }
  } catch (error) {
    console.error('Failed to initialize session:', error);
  } finally {
    loading.initialize = false;
  }
}

async function connectWithQR(sessionId) {
  try {
    const startTime = Date.now();
    addLog(`🚀 Starting QR connection process for session: ${sessionId}`, 'info', { sessionId });
    loading.connect = true;

    addLog('📡 Sending API request to WhatsApp gateway...', 'info');
    const fetchStartTime = Date.now();

    const response = await fetch('/api/whatsapp/sessions/connect-qr', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ session_id: sessionId }),
    });

    const fetchTime = Date.now() - fetchStartTime;
    addLog(`📨 API response received (${fetchTime}ms)`, 'info', {
      status: response.status,
      statusText: response.statusText,
      ok: response.ok,
      responseTime: `${fetchTime}ms`
    });

    addLog('📋 Parsing response data...', 'info');
    const data = await response.json();
    addLog('📊 Response data parsed', 'info', {
      success: data.success,
      has_qr_code: !!data.qr_code,
      has_error: !!data.error,
      error_message: data.error
    });

    if (data.success) {
      const totalTime = Date.now() - startTime;
      addLog(`✅ QR code generated successfully! Total time: ${totalTime}ms`, 'success', {
        qr_code_length: data.qr_code ? data.qr_code.length : 0,
        expires_in: data.expires_in || 60,
        total_time: `${totalTime}ms`
      });

      qrModal.show = true;
      qrModal.qrCode = data.qr_code;
      qrModal.expiresIn = data.expires_in || 60;

      addLog('🖼️ Opening QR modal for scanning...', 'info', {
        sessionId,
        qr_code_length: data.qr_code ? data.qr_code.length : 0,
        expires_in: qrModal.expiresIn
      });

      // Start countdown
      const countdown = createTrackedInterval(() => {
        qrModal.expiresIn--;
        if (qrModal.expiresIn <= 0) {
          clearInterval(countdown);
          qrModal.show = false;
          addLog('⏰ QR code expired', 'warning');
        }
      }, 1000);

      // Periodically check connection status with optimized polling
      let pollInterval = 5000; // Start with 5 seconds (reduced from 2 seconds)
      const maxPollInterval = 15000; // Max 15 seconds

      const createStatusChecker = (interval) => createTrackedInterval(async () => {
        try {
          const statusResponse = await fetch(`/api/whatsapp/sessions/status?session_id=${sessionId}`, {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`,
              'Accept': 'application/json',
            },
          });

          if (!statusResponse.ok) {
            throw new Error(`HTTP ${statusResponse.status}: ${statusResponse.statusText}`);
          }

          const statusData = await statusResponse.json();

          if (statusData.success && statusData.status === 'connected') {
            clearInterval(countdown);
            clearInterval(statusCheck);
            qrModal.show = false;
            addLog('🎉 WhatsApp connected successfully!', 'success', {
              sessionId,
              phone: statusData.phone_number
            });
            await loadSessions();
            await loadStatistics();
          } else if (statusData.success && (statusData.status === 'qr_generated' || statusData.status === 'connecting')) {
            // Still waiting, no action needed - continue polling
            addLog(`🔄 Still waiting for connection... (${Math.round((Date.now() - startTime) / 1000)}s)`, 'info');
          }
        } catch (error) {
          console.warn('Status check failed:', error);
          addLog(`⚠️ Status check failed: ${error.message}`, 'warning');
        }
      }, interval);

      let statusCheck = createStatusChecker(pollInterval);
    } else {
      const totalTime = Date.now() - startTime;
      addLog(`❌ QR generation failed after ${totalTime}ms`, 'error', {
        error: data.error,
        sessionId,
        total_time: `${totalTime}ms`
      });
    }
  } catch (error) {
    const totalTime = Date.now() - startTime;
    addLog(`💥 Connection failed after ${totalTime}ms`, 'error', {
      error: error.message,
      sessionId,
      total_time: `${totalTime}ms`,
      stack: error.stack
    });

    console.error('Failed to connect with QR:', {
      error: error.message,
      stack: error.stack,
      sessionId: sessionId
    });

    // Show user-friendly error message
    alert('Failed to connect with QR code: ' + error.message);
  } finally {
    loading.connect = false;
  }
}

async function disconnectSession(sessionId) {
  if (!confirm('Are you sure you want to disconnect this session?')) {
    return;
  }

  try {
    loading.disconnect = true;
    const response = await fetch('/api/whatsapp/sessions/disconnect', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ session_id: sessionId }),
    });

    const data = await response.json();
    if (data.success) {
      await loadSessions();
      await loadStatistics();
    }
  } catch (error) {
    console.error('Failed to disconnect session:', error);
  } finally {
    loading.disconnect = false;
  }
}

async function refreshQRCode(sessionId) {
  try {
    loading.refreshQR = true;
    const response = await fetch('/api/whatsapp/sessions/connect-qr', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ session_id: sessionId }),
    });

    const data = await response.json();
    if (data.success) {
      qrModal.show = true;
      qrModal.qrCode = data.qr_code;
      qrModal.expiresIn = data.expires_in || 60;

      // Start countdown
      const countdown = createTrackedInterval(() => {
        qrModal.expiresIn--;
        if (qrModal.expiresIn <= 0) {
          clearInterval(countdown);
          qrModal.show = false;
        }
      }, 1000);

      // Periodically check connection status with optimized polling (reuse same logic)
      let pollInterval = 5000; // Start with 5 seconds
      const maxPollInterval = 15000; // Max 15 seconds

      const createStatusChecker = (interval) => createTrackedInterval(async () => {
        try {
          const statusResponse = await fetch(`/api/whatsapp/sessions/status?session_id=${sessionId}`, {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`,
              'Accept': 'application/json',
            },
          });

          if (!statusResponse.ok) {
            throw new Error(`HTTP ${statusResponse.status}: ${statusResponse.statusText}`);
          }

          const statusData = await statusResponse.json();

          if (statusData.success && statusData.status === 'connected') {
            clearInterval(countdown);
            clearInterval(statusCheck);
            qrModal.show = false;
            addLog('🎉 WhatsApp connected successfully!', 'success', {
              sessionId,
              phone: statusData.phone_number
            });
            await loadSessions();
            await loadStatistics();
          }
        } catch (error) {
          console.warn('Status check failed:', error);
        }
      }, interval);

      let statusCheck = createStatusChecker(pollInterval);
    }
  } catch (error) {
    console.error('Failed to refresh QR code:', error);
  } finally {
    loading.refreshQR = false;
  }
}

async function deleteSession(sessionId) {
  if (!confirm('Are you sure you want to delete this session? This action cannot be undone.')) {
    return;
  }

  try {
    loading.delete = true;
    const response = await fetch(`/api/whatsapp/sessions/delete`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ session_id: sessionId }),
    });

    const data = await response.json();
    if (data.success) {
      await loadSessions();
      await loadStatistics();
      // Show success message
      alert('Session deleted successfully');
    } else {
      // Show error message
      alert('Failed to delete session: ' + (data.error || 'Unknown error'));
    }
  } catch (error) {
    console.error('Failed to delete session:', error);
    alert('Failed to delete session: ' + error.message);
  } finally {
    loading.delete = false;
  }
}

async function testConnection(sessionId) {
  try {
    loading.test = true;
    const response = await fetch('/api/whatsapp/test-connection', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ session_id: sessionId }),
    });

    const data = await response.json();
    if (data.success) {
      // Show success message
    }
  } catch (error) {
    console.error('Failed to test connection:', error);
  } finally {
    loading.test = false;
  }
}

async function sendMessage() {
  try {
    loading.sendMessage = true;
    const response = await fetch('/api/whatsapp/send-message', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(messageForm),
    });

    const data = await response.json();
    if (data.success) {
      // Reset form
      messageForm.to = '';
      messageForm.message = '';
      messageForm.type = 'text';

      await loadStatistics();
      await loadRecentNotifications();

      // Show success message
    }
  } catch (error) {
    console.error('Failed to send message:', error);
  } finally {
    loading.sendMessage = false;
  }
}

async function sendCustomNotification() {
  try {
    loading.customNotification = true;
    const response = await fetch('/api/whatsapp/notifications/custom', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        title: customNotificationForm.title,
        message: customNotificationForm.message,
        recipients: [], // Send to all users
      }),
    });

    const data = await response.json();
    if (data.success) {
      showCustomNotificationModal.value = false;
      customNotificationForm.title = '';
      customNotificationForm.message = '';

      await loadStatistics();
      await loadRecentNotifications();

      // Show success message
    }
  } catch (error) {
    console.error('Failed to send custom notification:', error);
  } finally {
    loading.customNotification = false;
  }
}

function refreshSessions() {
  addLog('🔄 Refreshing WhatsApp sessions...', 'info');
  loadSessions();
}

function refreshStatistics() {
  addLog('📊 Refreshing statistics...', 'info');
  loadStatistics();
}

function downloadLogs() {
  if (processLogs.value.length === 0) return;

  const logsText = processLogs.value.map(log => {
    return `[${log.timestamp}] [${log.type.toUpperCase()}] ${log.message}${log.data ? '\n  Data: ' + JSON.stringify(log.data, null, 2) : ''}`;
  }).join('\n\n');

  const blob = new Blob([logsText], { type: 'text/plain' });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `whatsapp-logs-${new Date().toISOString().split('T')[0]}.txt`;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  window.URL.revokeObjectURL(url);

  addLog('📥 Logs downloaded successfully', 'success', {
    logs_count: processLogs.value.length,
    filename: `whatsapp-logs-${new Date().toISOString().split('T')[0]}.txt`
  });
}

// Utility functions
function getStatusColor(status) {
  const colors = {
    connected: 'bg-green-100 text-green-800',
    connecting: 'bg-yellow-100 text-yellow-800',
    disconnected: 'bg-gray-100 text-gray-800',
    qr_generated: 'bg-blue-100 text-blue-800',
    error: 'bg-red-100 text-red-800',
  };
  return colors[status] || 'bg-gray-100 text-gray-800';
}

function getStatusText(status) {
  const texts = {
    connected: 'Connected',
    connecting: 'Connecting',
    disconnected: 'Disconnected',
    qr_generated: 'QR Generated',
    error: 'Error',
  };
  return texts[status] || status;
}

function getNotificationStatusColor(status) {
  const colors = {
    sent: 'bg-green-100 text-green-800',
    pending: 'bg-yellow-100 text-yellow-800',
    failed: 'bg-red-100 text-red-800',
  };
  return colors[status] || 'bg-gray-100 text-gray-800';
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleString();
}

// Helper function to track and cleanup intervals
function createTrackedInterval(callback, interval) {
  const intervalId = setInterval(callback, interval);
  activeIntervals.value.push(intervalId);
  return intervalId;
}

function clearAllIntervals() {
  activeIntervals.value.forEach(intervalId => {
    clearInterval(intervalId);
  });
  activeIntervals.value = [];
}

// Lifecycle
onMounted(() => {
  addLog('🚀 WhatsApp Gateway loaded successfully', 'success', {
    timestamp: new Date().toISOString(),
    page: 'WhatsAppGateway'
  });

  loadSessions();
  loadStatistics();
  loadRecentNotifications();
});

onUnmounted(() => {
  addLog('🧹 Cleaning up WhatsApp Gateway resources', 'info');
  clearAllIntervals();
});
</script>