<template>
  <div v-show="modelValue" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="$emit('update:modelValue', false)"></div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <!-- Header -->
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
              <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                {{ readonly ? 'Detail Jadwal Perkuliahan' : 'Edit Jadwal Perkuliahan' }}
              </h3>
              <div class="mt-1 text-sm text-gray-500">
                {{ readonly ? 'Informasi detail pertemuan.' : 'Ubah detail pertemuan ini.' }}
              </div>
            </div>
            <button @click="$emit('update:modelValue', false)" class="text-gray-400 hover:text-gray-500">
               <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
               </svg>
            </button>
          </div>
        </div>

        <!-- Form -->
<!-- Form -->
        <div class="px-4 py-5 bg-white sm:p-6">
            <form @submit.prevent="save">
              <div class="space-y-4">
                  <!-- Meeting Number -->
                  <div class="grid grid-cols-2 gap-4">
                      <div>
                          <label class="block text-sm font-medium text-gray-700">Pertemuan Ke-</label>
                          <input type="number" v-model="form.meeting_number" min="1" :disabled="readonly" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm disabled:bg-gray-100 disabled:text-gray-500">
                      </div>
                      <div>
                          <label class="block text-sm font-medium text-gray-700">Total Sesi</label>
                          <input type="number" v-model="form.total_sessions" min="1" :disabled="readonly" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm disabled:bg-gray-100 disabled:text-gray-500">
                      </div>
                  </div>

                  <!-- Date -->
                  <div>
                      <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                      <input type="date" v-model="form.date" :disabled="readonly" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm disabled:bg-gray-100 disabled:text-gray-500">
                  </div>

                  <!-- Time -->
                  <div class="grid grid-cols-2 gap-4">
                      <div>
                          <label class="block text-sm font-medium text-gray-700">Waktu Mulai</label>
                          <input type="time" v-model="form.start_time" :disabled="readonly" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm disabled:bg-gray-100 disabled:text-gray-500">
                      </div>
                      <div>
                          <label class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
                          <input type="time" v-model="form.end_time" :disabled="readonly" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm disabled:bg-gray-100 disabled:text-gray-500">
                      </div>
                  </div>

                  <!-- Session Type -->
                  <div>
                      <label class="block text-sm font-medium text-gray-700">Tipe Perkuliahan</label>
                      <select v-model="form.session_type" :disabled="readonly" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm disabled:bg-gray-100 disabled:text-gray-500">
                          <option value="kuliah">Kuliah</option>
                          <option value="uts">UTS</option>
                          <option value="uas">UAS</option>
                      </select>
                  </div>

                  <!-- Schedule Status (Terjadwal/Reschedule) -->
                  <div class="mb-4">
                      <label class="block text-sm font-medium text-gray-700 mb-1">Status Jadwal</label>
                      <div class="flex items-center space-x-4">
                          <label class="inline-flex items-center">
                              <input type="radio" v-model="form.is_rescheduled" :value="false" class="form-radio text-blue-600" :disabled="readonly">
                              <span class="ml-2 text-sm text-gray-700">Terjadwal</span>
                          </label>
                          <label class="inline-flex items-center">
                              <input type="radio" v-model="form.is_rescheduled" :value="true" class="form-radio text-orange-600" :disabled="readonly">
                              <span class="ml-2 text-sm text-orange-700">Reschedule</span>
                          </label>
                      </div>
                  </div>

                  <!-- Reschedule Date (if reschedule) -->
                  <div v-if="form.is_rescheduled" class="mb-4 p-3 bg-orange-50 border border-orange-200 rounded-md">
                      <label class="block text-sm font-medium text-orange-700">Tanggal Baru (Reschedule)</label>
                      <input type="date" v-model="form.date" :disabled="readonly" class="mt-1 block w-full border-orange-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                      <p class="mt-1 text-xs text-orange-600">Ubah tanggal jadwal baru di sini.</p>
                  </div>

                  <!-- Lecturer Selection -->
                  <div>
                      <SearchableSelect
                        label="Dosen Pengajar (Sesi Ini)"
                        v-model="form.lecturer_id"
                        :options="lecturerOptions"
                        :disabled="readonly"
                        placeholder="Cari dosen..."
                        empty-message="Dosen tidak ditemukan"
                      />
                      <p v-if="!readonly" class="mt-1 text-xs text-gray-500">Pilih satu dosen yang mengajar untuk jadwal ini.</p>
                  </div>

                   <!-- Mode Selection -->
                  <div class="mb-4">
                      <label class="block text-sm font-medium text-gray-700 mb-1">Mode Perkuliahan</label>
                      <div class="flex items-center space-x-4">
                          <label class="inline-flex items-center">
                              <input type="radio" v-model="form.is_online" :value="false" class="form-radio text-blue-600" :disabled="readonly">
                              <span class="ml-2 text-sm text-gray-700">Offline</span>
                          </label>
                          <label class="inline-flex items-center">
                              <input type="radio" v-model="form.is_online" :value="true" class="form-radio text-blue-600" :disabled="readonly">
                              <span class="ml-2 text-sm text-gray-700">Online</span>
                          </label>
                      </div>
                  </div>

                  <!-- Link Meeting (Online Only) -->
                  <div v-if="form.is_online" class="mb-4">
                      <label class="block text-sm font-medium text-gray-700">Link Meeting (Opsional)</label>
                      <input type="text" v-model="form.meeting_link" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="https://zoom.us/..." />
                  </div>

                  <!-- Room Selection -->
                  <div>
                      <label class="block text-sm font-medium text-gray-700">Ruangan</label>
                      <select v-model="form.room_id" :disabled="readonly || form.is_online" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm disabled:bg-gray-100 disabled:text-gray-500">
                          <option value="">Pilih Ruangan</option>
                          <option v-for="r in rooms" :key="r.id" :value="r.id">{{ r.name }} ({{ r.building }})</option>
                      </select>
                  </div>

                  <!-- Lecturers Team Teaching Info (Always visible for context) -->
                  <div v-if="schedule && (schedule.class_schedule_detail?.lecturers?.length || schedule.lecturers?.length)">
                      <label class="block text-sm font-medium text-gray-700 mt-2">Team Teaching Kelas</label>
                       <div class="mt-1 p-3 bg-gray-50 rounded-md border border-gray-200 text-sm">
                          <template v-if="schedule.class_schedule_detail?.lecturers?.length">
                              <div v-for="l in schedule.class_schedule_detail.lecturers" :key="l.id" class="mb-1 last:mb-0">
                                   {{ l.name }}
                              </div>
                          </template>
                          <template v-else-if="schedule.lecturers?.length">
                              <div v-for="l in schedule.lecturers" :key="l.id" class="mb-1 last:mb-0">
                                   {{ l.name }}
                              </div>
                          </template>
                       </div>
                  </div>

                  <!-- Link Meeting (if online) -->
                  <div v-if="schedule && (schedule.is_online || form.meeting_link)">
                      <label class="block text-sm font-medium text-gray-700">Link Meeting</label>
                      <input type="text" v-model="form.meeting_link" :disabled="readonly" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm disabled:bg-gray-100 disabled:text-gray-500">
                  </div>
              </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            v-if="!readonly"
            type="button"
            @click="save"
            :disabled="loading"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
          >
            {{ loading ? 'Menyimpan...' : 'Simpan Perubahan' }}
          </button>
          <button
            type="button"
            @click="$emit('update:modelValue', false)"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
          >
            {{ readonly ? 'Tutup' : 'Batal' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import SearchableSelect from '@/components/SearchableSelect.vue';

const props = defineProps({
  modelValue: Boolean,
  schedule: Object,
  readonly: {
      type: Boolean,
      default: false
  },
  lecturers: {
      type: Array,
      default: () => []
  },
  rooms: {
      type: Array,
      default: () => []
  }
});

const emit = defineEmits(['update:modelValue', 'save']);

const formattedRooms = computed(() => {
    return props.rooms.map(r => ({
        id: r.id,
        name: `${r.name} (${r.building})`
    }));
});

const lecturerOptions = computed(() => {
    const detail = props.schedule?.class_schedule_detail || props.schedule?.classScheduleDetail;
    if (detail?.lecturers?.length > 0) {
        return detail.lecturers;
    }
    return props.lecturers;
});

const loading = ref(false);
const form = ref({
    date: '',
    start_time: '',
    end_time: '',
    meeting_link: '',
    lecturer_id: '',
    room_id: '',
    is_online: false,
    session_type: 'kuliah',
    is_rescheduled: false,
    meeting_number: 1,
    total_sessions: 1
});

watch(() => props.schedule, (newVal) => {
    if (newVal) {
        // Fix date parsing if it contains time
        let parsedDate = newVal.date;
        if (parsedDate && parsedDate.includes('T')) {
            parsedDate = parsedDate.split('T')[0];
        } else if (parsedDate && parsedDate.length > 10) {
            parsedDate = parsedDate.substring(0, 10);
        }

        form.value = {
            date: parsedDate,
            start_time: newVal.start_time ? newVal.start_time.substring(0, 5) : '',
            end_time: newVal.end_time ? newVal.end_time.substring(0, 5) : '',
            meeting_link: newVal.meeting_link || '',
            lecturer_id: newVal.lecturer_id || '',
            room_id: newVal.room_id || '',
            is_online: !!newVal.is_online,
            session_type: newVal.session_type || 'kuliah',
            is_rescheduled: !!newVal.rescheduled_from,
            meeting_number: newVal.meeting_number || 1,
            total_sessions: newVal.total_sessions || 1
        };
    }
}, { immediate: true });

watch(() => form.value.is_online, (val) => {
    if (val) {
        form.value.room_id = '';
    }
});

const save = async () => {
    loading.value = true;
    try {
        await emit('save', {
            id: props.schedule.id,
            ...form.value
        });
        emit('update:modelValue', false);
    } catch (error) {
        console.error('Failed to save', error);
    } finally {
        loading.value = false;
    }
};
</script>
