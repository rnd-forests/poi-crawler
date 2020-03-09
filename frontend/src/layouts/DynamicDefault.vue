<script>
import LayoutDefault from './Default.vue'

export default {
  created () {
    this.$parent.$emit('update:layout', LayoutDefault)
  },

  render () {
    return this.$slots.default[0]
  },
}
</script>
