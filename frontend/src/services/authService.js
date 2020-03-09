import axios from 'axios'

const prefix = '/auth/'

export default {
  /**
   * Sign in request
   * @returns {Promise}
   */
  signIn: params => axios.post(`${prefix}sign-in`, params),

  me: () => axios.get(`${prefix}me`),

  signOut: () => axios.post(`${prefix}sign-out`),
}
