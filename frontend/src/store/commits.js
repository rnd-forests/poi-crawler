import Cookie from 'js-cookie'
import authService from 'services/authService'
// import { userService } from 'services/userService'
// import store from 'store/store'
import router from 'router/index'

export const SIGN_OUT = 'SIGN_OUT'
export const SIGN_IN = 'SIGN_IN'

// TODO: modules: https://vuex.vuejs.org/guide/modules.html
export default {
  [SIGN_OUT]: ({ commit }, options = {}) => new Promise((resolve, reject) => {
    try {
      authService.signOut()
        .finally(() => {
          commit('token', false)

          commit('user', {})

          Cookie.remove('token') // remove if have

          resolve()
          router.push('/auth/sign-in')
        })
    } catch (e) {
      reject(e)
    }
  }),

  [SIGN_IN]: ({ commit }, payload) => new Promise((resolve, reject) => {
    // payload.grant_type = password
    authService.signIn(payload).then(data => {
      const d = data.data
      const token = d.access_token

      // authService.me().then(data => commit('user', data.data))
      commit('user', d.user)

      commit('token', token)

      Cookie.set('token', token, { expires: 30 }) // days

      resolve(data)
    }).catch(err => {
      // logging or do something
      Cookie.remove('token') // remove if have
      reject(err)
    })
  }),
}
