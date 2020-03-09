import Vue from 'vue'
import Vuex from 'vuex'
import createPersistedState from 'vuex-persistedstate'
import Cookie from 'js-cookie'
import commits from './commits'
import getters from './getters'
import mutations from './mutations'

Vue.use(Vuex)

export const initialState = {
  token: Cookie.get('token') ? Cookie.get('token') : false,

  user: {
    name: null,
    email: null,
    role_id: null,
    _id: null,
    avatar: null,
  }
}

export default new Vuex.Store({
  state: initialState,
  getters,
  mutations,
  actions: commits,
  plugins: [createPersistedState()],
})
