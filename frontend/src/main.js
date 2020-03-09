// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import Vue from 'vue'
import App from './App'
import router from './router'
import './bootstrap'
import store from './store/store'

Vue.config.productionTip = false

// Suppress all Vue logs and warnings.
if (process.env.NODE_ENV === 'production') {
  Vue.config.silent = true
}

/* eslint-disable no-new */
new Vue({
  el: '#app',
  store,
  router,
  components: { App },
  template: '<App/>'
})
