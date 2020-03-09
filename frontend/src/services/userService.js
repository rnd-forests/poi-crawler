import axios from 'axios'

const prefix = '/users/'

export default {
  getUsers: (params = {}) => axios.get(`${prefix}`, { params }),

  createUser: params => axios.post(`${prefix}`, params),

  delete: id => axios.delete(`${prefix}${id}`),
}
