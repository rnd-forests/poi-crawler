<template>
  <dynamic-default-layout>
    <div>
      <div class="d-flex justify-content-between">
        <h3>Quản lý Location</h3>
        <router-link to="/locations/" class="btn btn-secondary px-4">Hủy</router-link>
      </div>

      <div class="row mt-3 mb-5">
        <div class="col-2">
          <template v-if="isAdmin">
            <div class="h3">Lịch sử chỉnh sửa</div>
            <ul class="list-group list-group-flush">
              <li v-for="(e, i) in formatLastEdit(location.edited_by, false)" :key="i" class="list-group-item">{{ e }}</li>
            </ul>
          </template>
        </div>

        <div class="col-9">
          <form @submit.stop.prevent="updateLocation">
            <div class="form-group">
              <label>Nguồn</label>
              <span class="mr-2">{{location.source}}</span>
              <a :href="location.source_url" target="_blank">{{location.source_url}}</a>
            </div>

            <div class="form-group">
              <label>Language</label>
              <select v-model="language" class="form-control">
                <option value="">--Default--</option>
                <option value="en">English</option>
                <option value="vi">Tiếng việt</option>
              </select>
            </div>

            <div class="form-group">
              <label>Tên<span class="text-danger"> *</span></label>
              <input v-model="location.name" class="form-control">
            </div>

            <div class="form-group">
              <label>Slug<span class="text-danger"> *</span></label>
              <input v-model="location.slug" class="form-control">
            </div>

            <div class="form-group">
              <label>Địa chỉ cụ thể<span class="text-danger"> *</span></label>
              <input v-model="location.formatted_address" class="form-control">
            </div>

            <div v-if="location.area" class="form-row">
              <div class="form-group col">
                <label>Tỉnh/Thành phố<span class="text-danger"> *</span></label>
                <v-select v-model="location.area.province" :options="provinces" label="name" />
              </div>
              <div class="form-group col">
                <label>Quận/Huyện<span class="text-danger"> *</span></label>
                <v-select v-model="location.area.district" :options="districts" label="name" />
              </div>
              <div class="form-group col">
                <label>Xã/Phường</label>
                <v-select v-model="location.area.ward" :options="wards" label="name" />
              </div>
            </div>

            <div class="form-group">
              <label>Trọng số (1 là thấp nhất, 4 là cao nhất)<span class="text-danger"> *</span></label>
              <select v-model.number="location.weight" class="form-control">
                <option v-for="i in 4" :key="i" :value="i">{{ i }}</option>
              </select>
            </div>

            <div v-if="locationTypes" class="mb-2">
              <label>Kiểu<span class="text-danger"> *</span></label>
              <v-select v-model="selectedTypes" :options="locationTypes" label="name" multiple/>
            </div>

            <div class="form-group">
              <label>Avatar</label>
              <input v-model="location.avatar" class="form-control">
              <img :src="location.avatar" class="avatar-64 mt-2">
            </div>

            <div class="form-group">
              <label>Mô tả</label>
              <textarea v-model="location.description" class="form-control" rows="5"></textarea>
            </div>

            <div class="form-group">
              <label>Từ khóa</label>
              <textarea v-model="location.keywords" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-group">
              <label>Giá</label>
              <input v-model="location.price_range" class="form-control">
            </div>

            <div v-if="location.geometry.coordinates" class="form-row">
              <div class="form-group col">
                <label>Kinh độ (Longitude)<span class="text-danger"> *</span></label>
                <input v-model.number="location.geometry.coordinates[0]" class="form-control">
              </div>
              <div class="form-group col">
                <label>Vĩ độ (Latitude)<span class="text-danger"> *</span></label>
                <input v-model.number="location.geometry.coordinates[1]" class="form-control">
              </div>
            </div>

            <div class="d-flex justify-content-around mt-4">
              <!--<router-link to="/locations/" class="btn btn-secondary px-5">Hủy</router-link>-->
              <btn-loading :loading="loading" class="btn btn-success px-5" type="submit">Update</btn-loading>
            </div>
          </form>
        </div>
      </div>
    </div>
  </dynamic-default-layout>
</template>

<script>
import vSelect from 'vue-select'
import service from 'services/locationService'
import locationTypeService from 'services/locationTypeService'
import { slugify } from 'utils/common'
import areaService from 'services/areaService'
import formatting from 'mixins/formatting'
import role from 'mixins/role'

export default {
  mixins: [formatting, role],

  components: {
    vSelect,
  },

  data: () => ({
    language: null,
    location: {
      name: '',
      geometry: {},
      area: {
        province: {},
        district: {},
        ward: {},
      },
    },
    loading: false,
    locationTypes: [],
    selectedTypes: [],
    provinces: [],
    districts: [],
    wards: [],
  }),

  created () {
    this.getLocation()

    locationTypeService.getAll({ per_page: 100 })
      .then(data => this.locationTypes = data.data)

    areaService.getProvinces()
      .then(data => this.provinces = data.data)
  },

  methods: {
    getLocation () {
      service.get(this.id)
        .then(data => {
          this.location = data.data
          this.selectedTypes = Array.isArray(data.data.type) ? data.data.type : [data.data.type]
        })
    },

    updateLocation() {
      this.loading = true

      service.update(this.id, this.formatTranslation())
        .then(data => {
          this.$toasted.success(data.message)
          // this.$router.push('/locations')
        })
        .finally(() => this.loading = false)
    },

    getDistricts(provinceId) {
      areaService.getDistricts(provinceId)
        .then(data => this.districts = data.data)
    },

    formatTranslation() {
      return Object.assign({}, this.location, { language: this.language })
    },
  },

  computed: {
    id() {
      return this.$route.params.id
    },
  },

  watch: {
    // https://stackoverflow.com/questions/42133894/vue-js-how-to-properly-watch-for-nested-data
    'location.name': function (name) {
      this.location.slug = slugify(name)
    },

    selectedTypes (val) {
      this.location.type = val.map(e => _.pick(e, ['_id', 'name', 'slug']))
    },

    'location.area.province': function (value) {
      // if (!_.isEmpty(this.location.area.district)) return

      this.getDistricts(value._id)
    },

    'location.area.district': function (value) {
      areaService.getWards(value._id)
        .then(data => this.wards = data.data)
    },

    language (val) {
      if (_.isEmpty(val)) {
        this.getLocation()
        return
      }

      service.getTranslation(this.id, val)
        .then(data => {
          const oldData = _.omit(this.location, ['name', 'description', 'slug', 'keywords', 'price_range', 'formatted_address'])
          if (_.isEmpty(data.data)) {
            this.location = Object.assign({ name: '' }, oldData)
          } else {
            this.location = Object.assign(data.data, oldData)
          }
        })
    }
  }
}
</script>
