import store from '@/store/store'

export default {
  computed: {
    isAdmin () {
      return store.getters.user.role_id
    }
  }
}
