import { mergeData } from 'vue-functional-data-merge'
import { _defineProperty } from 'utils/common'

export default {
  functional: true,
  props: {
    loading: {
      type: Boolean,
      default: false
    }
  },

  render (h, { props, children, data }) {
    return h(
      'button',
      mergeData(data, {
        class: _defineProperty({
          'disabled': props.loading,
        }),
      }),
      [
        props.loading ? h('fa-icon', {
          props: {
            icon: 'circle-notch',
            spin: true,
          }
        }) : null,
        ' ',
        children,
      ]
    )
  }
}
