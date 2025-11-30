import axios from 'axios'

const courseService = {
  // Get all courses with filtering and pagination
  async getAll(params = {}) {
    const response = await axios.get('/api/courses', { params })
    return response.data
  },

  // Get course by ID
  async getById(id) {
    const response = await axios.get(`/api/courses/${id}`)
    return response.data
  },

  // Create new course
  async create(data) {
    const response = await axios.post('/api/courses', data)
    return response.data
  },

  // Update course
  async update(id, data) {
    const response = await axios.put(`/api/courses/${id}`, data)
    return response.data
  },

  // Delete course
  async delete(id) {
    const response = await axios.delete(`/api/courses/${id}`)
    return response.data
  },

  // Restore deleted course
  async restore(id) {
    const response = await axios.post(`/api/courses/${id}/restore`)
    return response.data
  },

  // Force delete course
  async forceDelete(id) {
    const response = await axios.delete(`/api/courses/${id}/force`)
    return response.data
  },

  // Get available courses for enrollment
  async getAvailable(params = {}) {
    const response = await axios.get('/api/courses/available', { params })
    return response.data
  },

  // Get trashed courses
  async getTrash(params = {}) {
    const response = await axios.get('/api/courses/trash', { params })
    return response.data
  },

  // Add prerequisite to course
  async addPrerequisite(courseId, prerequisiteId) {
    const response = await axios.post(`/api/courses/${courseId}/prerequisites`, {
      prerequisite_course_id: prerequisiteId
    })
    return response.data
  },

  // Remove prerequisite from course
  async removePrerequisite(courseId, prerequisiteId) {
    const response = await axios.delete(`/api/courses/${courseId}/prerequisites/${prerequisiteId}`)
    return response.data
  },

  // Get course statistics
  async getStatistics() {
    const response = await axios.get('/api/courses/statistics')
    return response.data
  },

  // Bulk update courses
  async bulkUpdate(courseIds, updates) {
    const response = await axios.put('/api/courses/bulk', {
      course_ids: courseIds,
      updates: updates
    })
    return response.data
  },

  // Bulk delete courses
  async bulkDelete(courseIds) {
    const response = await axios.delete('/api/courses/bulk', {
      data: { course_ids: courseIds }
    })
    return response.data
  },

  // Bulk toggle course status
  async bulkToggleStatus(courseIds, isActive) {
    const response = await axios.put('/api/courses/bulk/status', {
      course_ids: courseIds,
      is_active: isActive
    })
    return response.data
  },

  // Import courses from file
  async import(file) {
    const formData = new FormData()
    formData.append('file', file)

    const response = await axios.post('/api/courses/import', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    return response.data
  },

  // Export courses
  async export(params = {}) {
    const response = await axios.get('/api/courses/export', {
      params,
      responseType: 'blob'
    })
    return response.data
  },

  // Duplicate course
  async duplicate(id) {
    const response = await axios.post(`/api/courses/${id}/duplicate`)
    return response.data
  },

  // Toggle active status
  async toggleStatus(id) {
    const response = await axios.patch(`/api/courses/${id}/toggle-status`)
    return response.data
  },

  // Download import template
  async downloadTemplate() {
    try {
      // Create unique filename with current date
      const now = new Date()
      const dateStr = now.toISOString().split('T')[0] // YYYY-MM-DD format
      const timeStr = now.toTimeString().split(' ')[0].replace(/:/g, '-') // HH-MM-SS format
      const randomSuffix = Math.random().toString(36).substring(7)

      const filename = `template-import-mata-kuliah-${dateStr}-${timeStr}-${randomSuffix}.xlsx`

      // Multiple cache-busting parameters
      const timestamp = now.getTime()
      const random = randomSuffix

      const response = await axios.get(`/api/courses/template-download?t=${timestamp}&r=${random}&v=${timestamp}`, {
        responseType: 'blob',
        headers: {
          'Cache-Control': 'no-cache, no-store, must-revalidate',
          'Pragma': 'no-cache',
          'Expires': '0',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })

      // Verify we got Excel content, not HTML
      const contentType = response.headers['content-type']
      if (!contentType.includes('excel') && !contentType.includes('openxmlformats')) {
        console.error('Invalid content type:', contentType)
        throw new Error('Server returned HTML instead of Excel file')
      }

      // Create blob with correct MIME type
      const blob = new Blob([response.data], {
        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
      })

      // Force download using temporary link
      const url = window.URL.createObjectURL(blob)
      const link = document.createElement('a')
      link.href = url
      link.download = filename
      link.style.display = 'none'

      // Append to body, click, then cleanup
      document.body.appendChild(link)
      link.click()

      // Extended cleanup delay
      setTimeout(() => {
        try {
          document.body.removeChild(link)
          window.URL.revokeObjectURL(url)
        } catch (e) {
          console.warn('Cleanup warning:', e)
        }
      }, 200)

      return response.data
    } catch (error) {
      console.error('Download error:', error)
      // If we get HTML error, try to parse it
      if (error.response && error.response.data) {
        const reader = new FileReader()
        reader.onload = function() {
          console.error('Received HTML instead of Excel:', reader.result)
        }
        reader.readAsText(error.response.data)
      }
      throw error
    }
  }
}

export default courseService