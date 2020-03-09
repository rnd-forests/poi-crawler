<template>
  <dynamic-default-layout>
    <div>
      <div class="d-flex justify-content-between">
        <h3>Quản lý Location type</h3>
        <button @click="$refs.addNew.show()" class="btn btn-success">Thêm mới</button>
      </div>

      <div v-if="types" class="mt-2">
        <table class="table table-striped scroll-x">
          <thead class="thead-dark">
          <tr>
            <th>Id</th>
            <th>Tên</th>
            <th>Slug</th>
            <th>Mô tả</th>
            <th>Tạo lúc</th>
            <th>Cập nhật lúc</th>
            <th class="w-100px">Hành động</th>
          </tr>
          </thead>

          <tbody>
          <tr v-for="e in types" :key="e._id">
            <td>{{ e._id }}</td>
            <td>{{ e.name }}</td>
            <td>{{ e.slug }}</td>
            <td>{{ e.description }}</td>
            <td>{{ e.created_at }}</td>
            <td>{{ e.updated_at }}</td>

            <!--TODO: Move to a component-->
            <td>
              <router-link :to="'/location-types/' + e._id" class="btn btn-sm btn-primary"><fa-icon icon="edit"/></router-link>
              <span @click="confirmDelete(e._id)" class="btn btn-sm btn-danger"><fa-icon icon="trash"/></span>
            </td>
          </tr>
          </tbody>
        </table>
      </div>

      <b-modal ref="addNew" hide-footer title="Thêm mới">
        <form @submit.stop.prevent="createType">
          <div class="form-group">
            <input v-model.trim="newTypeData.name" class="form-control" placeholder="Name">
          </div>

          <div class="form-group">
            <input v-model.trim="newTypeData.slug" class="form-control" placeholder="Slug">
          </div>

          <div class="form-group">
            <textarea v-model.lazy.trim="newTypeData.description" class="w-100 form-control" rows="5" placeholder="Description"></textarea>
          </div>

          <div class="d-flex justify-content-between">
            <button @click="$refs.addNew.hide()" type="reset" class="btn btn-secondary">Hủy</button>
            <btn-loading type="submit" :loading="isCreating" class="btn btn-success">Thêm mới</btn-loading>
          </div>
        </form>
      </b-modal>
    </div>
  </dynamic-default-layout>
</template>

<script>
import service from 'services/locationTypeService'
import bModal from 'bootstrap-vue/es/components/modal/modal'
import { slugify } from 'utils/common'

// It must be a function: https://github.com/vuejs/vue/issues/702
// value should be an empty string
const initialType = () => ({
  name: '',
  slug: '',
  description: '',
})

export default {
  components: {
    bModal,
  },

  data: () => ({
    showFields: [ 'name', 'slug', 'description', 'created_at', 'updated_at' ],
    types: null,
    isCreating: false,
    newTypeData: initialType(),
  }),

  created () {
    this.getAllTypes()
  },

  methods: {
    createType () {
      this.isCreating = true

      service.create(this.newTypeData)
        .then(data => {
          this.getAllTypes()
          this.newTypeData = initialType()
          this.$refs.addNew.hide()
          this.$toasted.success(data.message)
        })
        .finally(() => this.isCreating = false)
    },

    getAllTypes () {
      // service.getAll({ per_page: 100 })
      service.getAll()
        .then(data => {
          this.types = data.data
        })
    },

    confirmDelete(id) {
      const yes = confirm('Bạn có chắc?')

      if (yes) {
        service.delete(id)
          .then(data => {
            this.$toasted.success(data.message)
            this.getAllTypes()
          })
      }
    },
  },

  computed: {
    newTypeName () {
      return this.newTypeData.name
    }
  },

  watch: {
    newTypeName () {
      // https://stackoverflow.com/questions/42133894/vue-js-how-to-properly-watch-for-nested-data
      this.newTypeData.slug = slugify(this.newTypeData.name)
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
