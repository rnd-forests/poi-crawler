import Vue from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import BaseButton from '../components/base/BaseButton.js'
import Default from '@/layouts/Default'
import Empty from '@/layouts/Empty'
import DynamicDefault from '@/layouts/DynamicDefault'

Vue.component('fa-icon', FontAwesomeIcon)
Vue.component('btn-loading', BaseButton)
Vue.component('layout-default', Default)
Vue.component('layout-empty', Empty)
Vue.component('dynamic-default-layout', DynamicDefault)
