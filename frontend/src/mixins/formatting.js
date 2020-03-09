import moment from 'moment'

export default {
  methods: {
    /**
     *
     * @param {Array} histories
     * @param {Boolean} getLast
     * @returns {string|Array}
     */
    formatLastEdit(histories, getLast = true) {
      if (_.isEmpty(histories)) return ''

      if (getLast) {
        const last = _.last(histories)

        const time = moment(Number(last['edited_at']['$date']['$numberLong'])).format('HH:mm DD/MM/YYYY')

        return time + ' ' + last['name']
      }

      return histories.map(e => {
        return moment(Number(e['edited_at']['$date']['$numberLong'])).format('HH:mm DD/MM/YYYY') + ' ' + e['name']
      })
    }
  }
}
