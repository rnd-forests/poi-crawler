import axios from 'axios'

const prefix = '/areas/'

export default {
  getProvinces: () => axios.get(`${prefix}provinces`),

  getDistricts: provinceId => axios.get(`${prefix}provinces/${provinceId}/districts`),

  getWards: districtId => axios.get(`${prefix}districts/${districtId}/wards`),
}
