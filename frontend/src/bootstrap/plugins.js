// import VueSVGIcon from 'vue-svgicon'
import Vue from 'vue'
// import vClickOutside from 'v-click-outside'
import lodash from './lodash'
import VeeValidate from 'vee-validate'
import Toasted from 'vue-toasted'

// import { BREAKPOINTS } from '../constants/breakpoints'

// Vue.prototype.$breakpoints = BREAKPOINTS

// Define default locale
Vue.use(VeeValidate, {
  locale: 'vi',
  events: 'blur',
  fieldsBagName: 'veeFields'
})

Vue.use(Toasted, { position: 'bottom-left', duration: 5000, className: 'font-weight-bold' })

// ***** Global window variables *****
window._ = lodash
