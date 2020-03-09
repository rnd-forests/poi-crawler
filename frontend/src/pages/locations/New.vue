<template>
  <dynamic-default-layout>
    <div>
      <div class="d-flex justify-content-between">
        <h3>Quản lý Location</h3>
        <router-link to="/locations/" class="btn btn-secondary">Hủy</router-link>
      </div>

      <div class="row mt-4 mb-5">
        <div class="col-9">
          <div>
            <div class="card">
              <div class="card-body">
              <h3 class="card-title">Quick import</h3>
                <div class="form-group">
                  <label>Địa chỉ cụ thể (1)</label>
                  <form @submit.stop.prevent="detectLocation({ address: importAddress })" class="row">
                    <div class="col-9">
                      <input v-model.trim.lazy="importAddress" class="form-control" placeholder="Dịa chỉ, VD: Công viên Thủ Lệ, Đường vào Thủ Lệ, Ngọc Khánh, Ba Đình, Hà Nội">
                    </div>
                    <div class="col-3">
                      <button type="submit" class="btn btn-outline-success">Lấy từ địa chỉ</button>
                    </div>
                  </form>
                </div>

                <div class="form-group ">
                  <label>Latitude, Longitude</label>
                  <form @submit.stop.prevent="detectLocation({ latlng })" class="row">
                    <div class="col-9">
                      <input v-model.trim.lazy="latlng" class="form-control" placeholder="Latitude trước, Longitude sau. Cách nhau bởi dấu phẩy (Giống trên Google Maps)">
                    </div>
                    <div class="col-3">
                      <button type="submit" class="btn btn-outline-success">Lấy từ tọa độ</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="form-group mt-3">
              <label>Tên<span class="text-danger">&nbsp;*</span></label>
              <div class="row">
                <div class="col-10">
                  <input v-model.trim="location.name" class="form-control">
                </div>

                <div class="col-2">
                  <button @click="getNameFromAddress()" class="btn btn-outline-success">Lấy từ (1)</button>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label>Slug<span class="text-danger">&nbsp;*</span></label>
              <input v-model.trim.lazy="location.slug" class="form-control">
            </div>

            <div class="form-group row">
              <div class="col-3">
                <label>Nguồn</label>
                <input v-model.trim="location.source" class="form-control">
              </div>

              <div class="col-9">
                <label>URL</label>
                <input v-model.trim.lazy="location.source_url" class="form-control">
              </div>
            </div>

            <div class="form-group">
              <label>Địa chỉ cụ thể<span class="text-danger">&nbsp;*</span></label>
              <input v-model.trim.lazy="location.formatted_address" class="form-control">
            </div>

            <div class="text-danger">Bạn cần kiểm tra lại Địa chỉ địa giới hành chính do có thể có sai sót</div>

            <div class="form-row">
              <div class="form-group col">
                <label>Tỉnh/Thành phố<span class="text-danger">&nbsp;*</span></label>
                <v-select v-model="location.area.province" :options="provinces" label="name" />
              </div>
              <div class="form-group col">
                <label>Quận/Huyện<span class="text-danger">&nbsp;*</span></label>
                <v-select v-model="location.area.district" :options="districts" label="name" />
              </div>
              <div class="form-group col">
                <label>Xã/Phường</label>
                <v-select v-model="location.area.ward" :options="wards" label="name" />
              </div>
            </div>

            <div class="form-group">
              <label>Trọng số (1 là thấp nhất, 4 là cao nhất)<span class="text-danger">&nbsp;*</span></label>
              <select v-model.lazy.number="location.weight" class="form-control">
                <option v-for="i in 4" :key="i" :value="i">{{ i }}</option>
              </select>
            </div>

            <div class="mb-2">
              <label>Kiểu<span class="text-danger">&nbsp;*</span></label>
              <v-select v-model="selectedTypes" :options="locationTypes" label="name" multiple/>
            </div>

            <div v-if="location.geometry.coordinates" class="form-row">
              <div class="form-group col">
                <label>Kinh độ (Longitude)<span class="text-danger">&nbsp;*</span></label>
                <input v-model.number="location.geometry.coordinates[0]" class="form-control">
              </div>
              <div class="form-group col">
                <label>Vĩ độ (Latitude)<span class="text-danger">&nbsp;*</span></label>
                <input v-model.number="location.geometry.coordinates[1]" class="form-control">
              </div>
            </div>

            <div class="form-group">
              <label>Avatar</label>
              <input v-model.trim.lazy="location.avatar" class="form-control">
              <img :src="location.avatar" class="avatar-64 mt-2">
            </div>

            <div class="form-group">
              <label>Description</label>
              <textarea v-model.trim.lazy="location.description" class="form-control" rows="5"></textarea>
            </div>

            <div class="form-group">
              <label>Keywords</label>
              <textarea v-model.trim.lazy="location.keywords" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-group">
              <label>Price range</label>
              <input v-model.trim.lazy="location.price_range" class="form-control">
            </div>

            <div class="d-flex justify-content-around mt-4">
              <router-link to="/locations/" class="btn btn-secondary px-5">Hủy</router-link>
              <button @click="createLocation(false)" class="btn btn-success">Lưu và tiếp tục tạo</button>
              <button @click="createLocation" class="btn btn-success px-5">Lưu</button>
            </div>
          </div>
        </div>

        <div class="col-3">
          <h4>Địa điểm xung quanh đã có (bán kính 1 km)</h4>
          <div v-if="nearLocations.length">
            <p v-for="(e, i) in nearLocations" :key="e._id">
              <span :class="i < 3 ? 'text-danger' : 'text-warning'">{{ e.name }}</span> <span class="text-muted">({{ e.formatted_address }})</span>
            </p>
          </div>
          <!--<h4>Địa điểm tên tương tự</h4>-->
        </div>
      </div>
      <full-loading :show="loading" />
    </div>
  </dynamic-default-layout>
</template>

<script>
import vSelect from 'vue-select'
import service from 'services/locationService'
import areaService from 'services/areaService'
import locationTypeService from 'services/locationTypeService'
import { slugify } from 'utils/common'
import FullLoading from 'vue-full-loading'

// reset data Object.assign(this.$data, this.$options.data())
export default {
  components: {
    vSelect,
    FullLoading,
  },

  data: () => ({
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
    selectedTypes: null,
    selectedProvince: null,
    selectedDistrict: null,
    selectedWard: null,
    provinces: [],
    districts: [],
    wards: [],
    latlng: null,
    importAddress: '',
    nearLocations: [],
  }),

  created () {
    locationTypeService.getAll({ per_page: 100 })
      .then(data => this.locationTypes = data.data)

    this.getProvinces()
  },

  methods: {
    createLocation(redirect = true) {
      this.loading = true

      service.create(this.location)
        .then(data => {
          this.$toasted.success(data.message)
          if (redirect) {
            this.$router.push('/locations')
          } else {
            const oldData = _.pick(this.$data, ['locationTypes', 'provinces'])
            Object.assign(this.$data, this.$options.data(), oldData)
          }
        })
        .finally(() => this.loading = false)
    },

    detectLocation (options) {
      this.loading = true

      service.detect(options)
        .then(data => {
          if (options.address) this.getNameFromAddress()

          this.location.map_info = data.data

          // const info = data.data[0] // take first result
          const info = data.data

          this.location.formatted_address = info.formatted_address

          if (info.area) Object.assign(this.location.area, info.area)

          this.location.geometry.coordinates = [info.geometry.location.lng, info.geometry.location.lat]

          this.nearLocations = info.near_locations
        })
        .finally(() => this.loading = false)
    },

    getNameFromAddress () {
      const address = this.importAddress

      if (address) {
        this.location.name = address.split(',')[0]
      }
    },

    getProvinces() {
      areaService.getProvinces()
        .then(data => this.provinces = data.data)
    }
  },

  watch: {
    selectedTypes (val) {
      if (_.isEmpty(val)) return

      this.location.type = val.map(e => _.pick(e, ['_id', 'name', 'slug']))
    },

    'location.area.province': function (value) {
      if (_.isEmpty(value)) return

      areaService.getDistricts(value._id)
        .then(data => this.districts = data.data)
    },

    'location.area.district': function (value) {
      if (_.isEmpty(value)) return

      areaService.getWards(value._id)
        .then(data => this.wards = data.data)
    },

    'location.name': function (name) {
      this.location.slug = slugify(name)
    },
  }
}
</script>

<style lang="scss" scoped>
</style>
