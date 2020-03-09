<script>
export default {
  created () {
    this.$parent.$emit('update:layout', 'div')
  },

  render () {
    return this.$slots.default[0]
  },
}
</script>
