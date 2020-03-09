<template>
  <dynamic-default-layout>
    <div class="container">
      <div class="d-flex justify-content-between">
        <h3>Quản lý Location type</h3>
        <router-link to="/location-types/" class="btn btn-secondary">Hủy</router-link>
      </div>

      <div class="mt-2">
        <form @submit.stop.prevent="updateType">
          <div class="form-group">
            <label>Name</label>
            <input v-model="type.name" class="form-control">
          </div>

          <div class="form-group">
            <label>Slug</label>
            <input v-model="type.slug" class="form-control">
          </div>

          <div class="form-group">
            <label>Description</label>
            <input v-model="type.description" class="form-control">
          </div>

          <div class="form-group">
            <label>Language</label>
            <select v-model="language" class="form-control">
            <!--<select v-model="type.language" class="form-control">-->
              <option value="">--Default--</option>
              <option value="en">English</option>
              <option value="vi">Tiếng việt</option>
            </select>
          </div>

          <btn-loading :loading="loading" class="btn btn-success" type="submit">Update</btn-loading>
        </form>
      </div>
    </div>
  </dynamic-default-layout>
</template>

<script>
import service from 'services/locationTypeService'
import { slugify } from 'utils/common'

export default {
  data: () => ({
    type: {},
    loading: false,
    language: null,
  }),

  created () {
    this.getType()
  },

  methods: {
    getType () {
      service.get(this.id)
        .then(data => this.type = data.data)
    },

    updateType() {
      this.loading = true

      service.update(this.id, this.formatTranslation())
        .then(data => {
          this.$toasted.success(data.message)
        })
        .finally(() => this.loading = false)
    },

    formatTranslation() {
      return Object.assign({}, this.type, { language: this.language })
    },
  },

  computed: {
    id() {
      return this.$route.params.id
    },
  },

  watch: {
    'type.name': function (val) {
      // https://stackoverflow.com/questions/42133894/vue-js-how-to-properly-watch-for-nested-data
      this.type.slug = slugify(val)
    },

    language (val) {
      if (_.isEmpty(val)) {
        this.getType()
        return
      }

      service.getTranslation(this.id, val)
        .then(data => this.type = _.isEmpty(data.data) ? {} : data.data)
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
