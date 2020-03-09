import axios from 'axios'

const prefix = '/location-types/'

export default {
  getAll: (params = {}) => axios.get(`${prefix}`, { params }),

  get: id => axios.get(`${prefix}${id}`),

  create: params => axios.post(`${prefix}`, params),

  update: (id, params) => axios.put(`${prefix}${id}`, params),

  delete: id => axios.delete(`${prefix}${id}`),

  getTranslation: (id, lang) => axios.get(`${prefix}${id}/locales/${lang}`),

  // createTranslation: (id, params) => axios.post(`${prefix}${id}/locales`, params),
  //
  // updateTranslation: (id, params) => axios.patch(`${prefix}${id}/locales`, params),
}
