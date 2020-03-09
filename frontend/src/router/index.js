import Vue from 'vue'
import { authGuard } from './guards'
import Router from 'vue-router'
import HelloWorld from '@/components/HelloWorld'
import SignIn from 'pages/auth/SignIn'
import UserIndex from 'pages/users/Index'
import UserCreate from 'pages/users/Create'
import LocationTypeIndex from 'pages/location-types/Index'
import LocationTypeEdit from 'pages/location-types/Edit'
import LocationJobIndex from 'pages/location-jobs/Index'
import LocationIndex from 'pages/locations/Index'
import LocationEdit from 'pages/locations/Edit'
import LocationNew from 'pages/locations/New'
import NotFound from 'pages/errors/404'

Vue.use(Router)

export default new Router({
  mode: 'history',

  routes: [
    ...authGuard([
      {
        path: '/',
        component: HelloWorld
      },
      {
        path: '/users',
        component: UserIndex
      },
      {
        path: '/users/create',
        component: UserCreate
      },
      {
        path: '/location-types',
        component: LocationTypeIndex
      },
      {
        path: '/location-types/:id',
        component: LocationTypeEdit
      },
      {
        path: '/locations',
        component: LocationIndex
      },
      {
        path: '/locations/edit/:id',
        component: LocationEdit
      },
      {
        path: '/locations/new',
        component: LocationNew
      },
      {
        path: '/location-jobs',
        component: LocationJobIndex
      },
    ]),
    {
      name: 'auth.sign-in',
      path: '/auth/sign-in',
      component: SignIn
    },
    {
      path: '*',
      component: NotFound
    },
  ]
})
