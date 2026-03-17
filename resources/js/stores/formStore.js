import { defineStore } from 'pinia'
import { ref } from 'vue'
import formApi from '@/api/forms'

export const useFormStore = defineStore('form', () => {
  const forms = ref([])
  const currentForm = ref(null)
  const analytics = ref(null)
  const loading = ref(false)

  const fetchForms = async (params = {}) => {
    loading.value = true
    try {
      const { data } = await formApi.getForms(params)
      forms.value = data.data
      return data
    } finally {
      loading.value = false
    }
  }

  const fetchForm = async (id) => {
    loading.value = true
    try {
      const { data } = await formApi.getForm(id)
      currentForm.value = data.data
      return data.data
    } finally {
      loading.value = false
    }
  }

  const fetchAnalytics = async (id) => {
    const { data } = await formApi.getAnalytics(id)
    analytics.value = data.data
    return data.data
  }

  return {
    forms,
    currentForm,
    analytics,
    loading,
    fetchForms,
    fetchForm,
    fetchAnalytics,
  }
})
