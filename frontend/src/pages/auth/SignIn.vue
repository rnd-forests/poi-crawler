<template>
  <layout-empty>
    <div class="px-3 pt-6 bg-gray h-95vh">
      <div class="modal-dialog">
        <div class="modal-content border-0">
          <h4 class="mt-2 text-center font-weight-bold text-gray-800 text-uppercase">Đăng nhập</h4>
          <div class="modal-body">
            <form @submit.prevent="signin()" class="form" role="form">
              <div class="form-group">
                <input v-model.lazy.trim="payload.email"
                       v-validate="'required|email'"
                       name="email"
                       type="text"
                       autocomplete="username"
                       placeholder="Email"
                       class="form-control"
                       autofocus
                >
                <p v-if="errors.has('email')" class="text-danger">{{ errors.first('email') }}</p>
              </div>
              <div class="form-group">
                <input v-model.lazy.trim="payload.password"
                       v-validate="'required|min:6'"
                       name="password"
                       type="password"
                       autocomplete="current-password"
                       placeholder="Mật khẩu"
                       class="form-control"
                >
                <p v-if="errors.has('password')" class="text-danger">{{ errors.first('password') }}</p>
              </div>

              <btn-loading :loading="loading" type="submit" class="btn btn-primary w-100 text-white font-weight-bold mb-2">Đăng nhập</btn-loading>
            </form>
          </div>
        </div>
      </div>
    </div>
  </layout-empty>
</template>

<script>
export default {
  components: {
  },

  data: () => ({
    loading: false,
    payload: {
      email: null,
      password: null,
    }
  }),

  methods: {
    signin () {
      this.$validator.validateAll().then(() => {
        if (!this.errors.any()) {
          this.loading = true

          this.$store.dispatch('SIGN_IN', this.payload)
            .then(() => this.onSuccess())
            .finally(() => this.loading = false)
        }
      })
    },

    onSuccess () {
      const redirectUrl = this.$route.query.return_url ? this.$route.query.return_url : '/'

      this.$router.push(redirectUrl)
    },
  },
}
</script>
