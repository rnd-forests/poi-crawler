<template>
  <dynamic-default-layout>
    <div class="container">
      <h2>Lấy dữ liệu từ trang chi tiết của một địa điểm (Vd: foody.vn)</h2>
      <form @submit.stop.prevent="create" role="form" class="mt-5">
        <div class="form-group row">
          <div class="col-10">
            <input v-model.lazy.trim="payload.crawl_url" class="form-control" placeholder="URL trang chi tiết địa điểm">
          </div>
          <btn-loading :loading="loading" type="submit" class="btn btn-outline-success col-2">Thêm mới</btn-loading>
        </div>
      </form>

      <div class="mt-5">
        <h4 v-if="successUrls.length">Danh sách các URLs đã được đẩy vào hàng đợi. Bạn có kiểm tra trong mục Locations sau ít phút.</h4>
        <div v-for="(e, i) in successUrls" :key="i" class="text-success" role="alert">{{ e }}</div>
      </div>
    </div>
  </dynamic-default-layout>
</template>

<script>
import service from 'services/crawlerService'

export default {
  data: () => ({
    loading: false,
    payload: {
      crawl_url: null,
    },
    successUrls: [],
  }),

  methods: {
    create () {
      this.loading = true

      service.create(this.payload)
        .then(() => {
          this.successUrls.unshift(this.payload.crawl_url)
          this.payload.crawl_url = null
          // this.$toasted.success(data.message)
        })
        .finally(() => this.loading = false)
    },
  },
}
</script>

<style lang="scss" scoped>
</style>
