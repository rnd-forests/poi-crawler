<template>
  <dynamic-default-layout>
    <div>
      <div class="d-flex justify-content-between">
        <h3>Quản lý User</h3>
        <router-link to="/users/create" class="btn btn-success">Tạo User</router-link>
      </div>
      <div v-if="users" class="mt-2">
        <table class="table table-striped scroll-x">
          <thead class="thead-dark">
          <tr>
            <th>Email</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Created at</th>
            <th>Updated At</th>
            <th>Hành động</th>
          </tr>
          </thead>

          <tbody>
          <tr v-for="e in users" :key="e._id">
            <td>{{ e.email }}</td>
            <td>{{ e.name }}</td>
            <td>{{ e.phone }}</td>
            <td>{{ e.role_id == 1 ? 'Admin' : 'Editor' }}</td>
            <td>{{ e.created_at }}</td>
            <td>{{ e.updated_at }}</td>
            <td>
              <!--<router-link :to="'/users/edit/' + e._id" class="btn btn-sm btn-primary"><fa-icon icon="edit"/></router-link>-->
              <span @click="confirmDelete(e._id)" class="btn btn-sm btn-danger"><fa-icon icon="trash"/></span>
            </td>
          </tr>
          </tbody>
        </table>
      </div>

      <div>
        <b-pagination :total-rows="metaData.total" v-model="query.page" :per-page="metaData.per_page" align="center"/>
      </div>
    </div>
  </dynamic-default-layout>
</template>

<script>
import service from 'services/userService'
import BPagination from 'bootstrap-vue'

export default {
  components: {
    BPagination,
  },

  data: () => ({
    users: null,
    metaData: {
      total: null,
      per_page: 15,
    },
    query: {
      page: 1,
    }
  }),

  methods: {
    getUsers() {
      service.getUsers(this.query)
        .then(data => {
          this.users = data.data
          this.metaData = data.meta
        })
    },

    confirmDelete(id) {
      const yes = confirm('Bạn có chắc?')

      if (yes) {
        service.delete(id)
          .then(data => {
            this.$toasted.success(data.message)
            this.getUsers()
          })
      }
    },
  },

  watch: {
    'query.page': {
      handler: function () {
        this.getUsers()
      },
      immediate: true,
    },
  }
}
</script>

<style lang="scss" scoped>
</style>
