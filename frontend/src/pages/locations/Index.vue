<template>
  <dynamic-default-layout>
    <div>
      <div class="d-flex justify-content-between">
        <h3>Quản lý Locations</h3>
        <router-link to="/locations/new" class="btn btn-success">Thêm mới</router-link>
      </div>

      <div class="my-2">
        <div>
          <form @submit.stop.prevent="getLocations" class="" role="form">
            <div class="form-row">
              <div class="col">
                <label>Tên địa điểm</label>
                <input v-model.trim.lazy="query.q" class="form-control" placeholder="Tên địa điểm">
              </div>

              <div class="col">
                <label>Kiểu</label>
                <v-select v-model="selectedTypes" :options="types" label="name" multiple/>
              </div>

              <div class="col">
                <label>Tỉnh/Thành phố</label>
                <v-select v-model="selectedProvince" :options="provinces" label="name"/>
              </div>

              <div class="col">
                <label>Quận/Huyện</label>
                <v-select v-model="selectedDistrict" :options="districts" label="name" />
              </div>
            </div>

            <div class="form-row">
              <div class="col">
                <label>Trọng số</label>
                <select v-model.number="query.weight" class="form-control">
                  <option v-for="i in 4" :key="i" :value="i">{{ i }}</option>
                </select>
              </div>

              <div class="col">
                <label>Người dùng</label>
                <v-select v-model="selectedUser" :options="users" label="name"/>
              </div>

              <div class="col">
                <label>Ngày tạo</label>
                <input v-model="query.created_at" type="date" class="form-control">
              </div>

              <div class="col">
                <label>&nbsp;</label>
                <div class="d-flex justify-content-center">
                  <btn-loading :loading="loading" type="submit" class="btn btn-success px-4">Search</btn-loading>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="d-flex align-items-center">
        <div>Tổng số kết quả: {{ metaData.total }}</div>
      </div>

      <div class="mt-4">
        <table class="table table-striped scroll-x">
          <thead class="thead-dark">
          <tr>
            <th>Chọn</th>
            <th>Avatar</th>
            <th class="">Tên</th>
            <th>Kiểu</th>
            <th>Nguồn</th>
            <!--<th>Slug</th>-->
            <th>Mô tả</th>
            <!--<th class="w-150px">Đánh giá</th>-->
            <!--<th>Từ khóa</th>-->
            <th class="">Địa chỉ</th>
            <!--<th>Giá</th>-->
            <!--<th>Tọa độ</th>-->
            <!--<th>Tạo lúc</th>-->
            <th>Cập nhật lần cuối</th>
            <th>Hành động</th>
          </tr>
          </thead>

          <tbody>
          <tr v-for="e in locations" :key="e._id">
            <td>
              <input @click="toggleDeleteItem(e._id)" type="checkbox" class="w-1_5 h-1_5">
            </td>
            <td><img :src="e.avatar" class="avatar-64"></td>
            <td>{{ e.name }}</td>
            <td>{{ transformLocationType(e.type) }}</td>
            <td>
              <div>{{ e.source }}</div>
              <div v-if="e.source_url"><a :href="e.source_url" target="_blank">Xem</a></div>
            </td>
            <!--<td>{{ e.slug }}</td>-->
            <td>{{ e.description }}</td>
            <!--<td>-->
              <!--<div>Trung bình: {{ e.review.avg }}</div>-->
              <!--<div>Tổng: {{ e.review.total }}</div>-->
              <!--<div v-if="e.review.fields">-->
                <!--<div v-for="(r, i) in e.review.fields" :key="i">{{ r.name }}: {{ r.value }}</div>-->
              <!--</div>-->
            <!--</td>-->
            <!--<td>{{ e.keywords }}</td>-->
            <td>{{ e.formatted_address }}</td>
            <!--<td>{{ e.price_range }}</td>-->
            <!--<td>-->
              <!--<div>Longitude: {{ e.longitude }}</div>-->
              <!--<div>Latitude: {{ e.latitude }}</div>-->
            <!--</td>-->
            <!--<td>{{ e.created_at }}</td>-->
            <td>{{ formatLastEdit(e.edited_by) }}</td>

            <td>
              <router-link :to="'/locations/edit/' + e._id" class="btn btn-sm btn-primary"><fa-icon icon="edit"/></router-link>
              <span @click="confirmDelete(e._id)" class="btn btn-sm btn-danger"><fa-icon icon="trash"/></span>
            </td>
          </tr>
          </tbody>
        </table>

        <div class="row">
          <div class="col-2">
            <button @click="deleteItems" class="btn btn-danger d-block">Xóa dòng đã chọn</button>
          </div>

          <div class="col-10">
            <b-pagination :total-rows="metaData.total" v-model="query.page" :per-page="metaData.per_page" align="center"/>
          </div>
        </div>
      </div>
    </div>
  </dynamic-default-layout>
</template>

<script>
import vSelect from 'vue-select'
import service from 'services/locationService'
import userService from 'services/userService'
import areaService from 'services/areaService'
import typeService from 'services/locationTypeService'
import bPagination from 'bootstrap-vue/es/components/pagination/pagination'
// import role from 'mixins/role'
import formatting from 'mixins/formatting'

export default {
  mixins: [formatting],

  components: {
    bPagination,
    vSelect,
  },

  data: () => ({
    locations: null,
    metaData: {},
    loading: false,
    query: {
      q: null,
      page: 1,
      weight: null,
    },
    delete_items: [],
    users: [],
    provinces: [],
    districts: [],
    types: [],
    selectedProvince: {},
    selectedDistrict: {},
    selectedUser: {},
    selectedTypes: [],
  }),

  created () {
    // this.getLocations()
    this.getProvinces()

    this.getTypes()

    this.getUsers()
  },

  methods: {
    // formatAddress(address) {
    //   return `${address.address}, ${address.district_name}, ${address.province_name}`
    // },

    getLocations() {
      service.getLocations(this.query)
        .then(data => {
          this.locations = data.data
          // this.metaData = _.omit(data, ['data'])
          this.metaData = data.meta
        })
    },

    getUsers() {
      userService.getUsers()
        .then(data => {
          this.users = data.data
        })
    },

    getProvinces() {
      areaService.getProvinces()
        .then(data => this.provinces = data.data)
    },

    getTypes() {
      typeService.getAll({ per_page: 100 })
        .then(data => this.types = data.data)
    },

    confirmDelete(id) {
      const yes = confirm('Bạn có chắc?')

      if (yes) {
        service.delete(id)
          .then(data => {
            this.$toasted.success(data.message)
            this.getLocations()
          })
      }
    },

    transformLocationType(type) {
      if (_.isEmpty(type)) return ''

      // if (typeof type === 'string') return type

      return type.map(e => e.name).join(', ')
    },

    toggleDeleteItem(id) {
      const index = this.delete_items.indexOf(id)

      if (index === -1) {
        this.delete_items.push(id)
      } else {
        this.delete_items.splice(index, 1)
      }
    },

    deleteItems() {
      const yes = confirm('Bạn có chắc?')

      if (yes) {
        service.bulkUpdate({ action: 'delete', data: this.delete_items })
          .then(data => {
            this.$toasted.success(data.message)
            this.getLocations()
          })
      }
    },
  },

  watch: {
    'query.page': {
      // TODO: change to watch current page
      handler: function () {
        this.getLocations()
      },
      immediate: true,
    },

    'query.province': function (value) {
      // if (!_.isEmpty(this.location.area.district)) return

      this.getDistricts(value._id)
    },

    selectedProvince(value) {
      if (value) {
        this.query.province_id = value._id

        areaService.getDistricts(value._id)
          .then(data => this.districts = data.data)
      } else {
        this.query.province_id = null
      }
    },

    selectedDistrict(value) {
      this.query.district_id = value ? value._id : null
    },

    selectedUser(value) {
      this.query.user_id = value ? value._id : null
    },

    selectedTypes(value) {
      this.query.type_ids = _.isEmpty(value) ? null : value.map(e => e._id).join(',')
    },
  }
}
</script>

<style lang="scss" scoped>
</style>
