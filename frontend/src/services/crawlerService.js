import axios from 'axios'

const prefix = '/crawlers/locations/'

export default {
  create: params => axios.post(`${prefix}`, params)
}
