// Mock schedule service for build purposes
const scheduleService = {
  async getAll(params = {}) {
    return { data: [], total: 0 }
  },

  async getStatistics() {
    return {
      total_schedules: 0,
      today_schedules: 0,
      available_rooms: 0,
      conflicts: 0
    }
  },

  async getById(id) {
    return { data: null }
  },

  async delete(id) {
    return true
  },

  async bulkDelete(data) {
    return true
  }
}

export default scheduleService