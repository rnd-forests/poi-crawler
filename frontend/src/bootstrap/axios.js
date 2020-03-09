import axios from 'axios'
import store from 'store/store'
import env from 'config/env'
import Vue from 'vue'

Object.assign(axios.defaults, {
  baseURL: env.API_END_POINT,
  timeout: 60000,
  // headers: { 'Cache-Control': 'no-cache' },
})

axios.interceptors.request.use(function (config) {
  if (!config.headers.Authorization) {
    const token = store.getters.token

    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
  }

  return config
}, function (error) {
  return Promise.reject(error)
})

axios.interceptors.response.use(response => response.data, error => {
  if (error.response) {
    const status = Number(error.response.status)

    if (status) {
      if (status === 503) {
        Vue.toasted.error('Server đang được bảo trì. Xin liên hệ bộ phận kỹ thuật')
      } else if (String(status).startsWith('5')) {
        Vue.toasted.error(`${status} Lỗi server, hãy thông báo cho ban quản trị` + error.response.data ? ' :' + error.response.data.message : '')
      } else if (status === 401) {
        Vue.toasted.error('Bạn cần đăng nhập lại')
        // store.dispatch('SIGN_OUT', { return_url: encodeURI(location.href) })
      } else {
        Vue.toasted.error(error.response.data.message)
      }
    } else {
      Vue.toasted.error(`Xin thử lại. (${error.response.status})`)
    }
  } else if (error.request) {
    console.log(error.request)
    Vue.toasted.error('Xin kiểm tra kết nối mạng')
  } else {
    // Something happened in setting up the request that triggered an Error
    console.log('Error', error.message)
  }

  return Promise.reject(error)
})
