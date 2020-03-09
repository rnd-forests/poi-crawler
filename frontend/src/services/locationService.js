import axios from 'axios'

const prefix = '/locations/'

export default {
  getLocations: (params = {}) => axios.get(`${prefix}`, { params }),

  get: id => axios.get(`${prefix}${id}`),

  create: params => axios.post(`${prefix}`, params),

  update: (id, params) => axios.put(`${prefix}${id}`, params),

  delete: id => axios.delete(`${prefix}${id}`),

  bulkUpdate: params => axios.put(`${prefix}`, params),

  detect: params => axios.post(`detect-location`, params),

  getTranslation: (id, lang) => axios.get(`${prefix}${id}/locales/${lang}`),
}
