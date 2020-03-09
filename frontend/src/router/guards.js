import store from '@/store/store'

export const authGuard = (routes) => {
  return guard(routes, (to, from, next) => {
    if (store.getters.token) {
      next()
    } else {
      next({ name: 'auth.sign-in', params: { redirect_url: to.fullPath } })
    }
  })
}

/**
 * @param  {Array} routes
 * @param  {Function} guard
 * @return {Array}
 */
function guard (routes, guard) {
  routes.forEach(route => {
    route.beforeEnter = guard
  })

  return routes
}
