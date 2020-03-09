<template>
  <div id="wrapper">
    <nav class="navbar header-top fixed-top navbar-expand-lg  navbar-dark bg-dark">
      <router-link class="navbar-brand" to="/">Location CMS</router-link>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText"
              aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav side-nav pl-2">
          <template v-if="isAdmin">
            <li class="nav-item">
              <router-link class="nav-link" to="/users"><fa-icon icon="users"/>&nbsp;Users</router-link>
            </li>

            <li class="nav-item">
              <router-link class="nav-link" to="/location-types"><fa-icon :icon="['far', 'map']"/>&nbsp;Location Types</router-link>
            </li>
          </template>

          <li class="nav-item">
            <router-link class="nav-link" to="/locations"><fa-icon icon="map-marker-alt"/>&nbsp;Locations</router-link>
          </li>

          <li class="nav-item">
            <router-link class="nav-link" to="/locations/new"><fa-icon icon="plus-circle"/> New Location</router-link>
          </li>

          <li class="nav-item">
            <router-link class="nav-link" to="/location-jobs"><fa-icon icon="download"/>&nbsp;Crawler</router-link>
          </li>
        </ul>

        <ul class="navbar-nav ml-md-auto d-md-flex">
          <li class="nav-item">
            <span class="nav-link">{{ user.name }}</span>
          </li>
          <li class="nav-item">
            <span @click="signOut" class="nav-link pointer">Đăng xuất</span>
          </li>
        </ul>
      </div>
    </nav>

    <slot></slot>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import role from '@/mixins/role'

export default {
  mixins: [role],

  computed: {
    ...mapState([
      'user',
    ])
  },

  methods: {
    signOut () {
      this.$store.dispatch('SIGN_OUT')
    }
  },
}
</script>

<style lang="scss" scoped>
  body {
    background: #f9f9f9;
  }

  #wrapper {
    padding: 90px 15px;
  }

  .navbar-expand-lg .navbar-nav.side-nav {
    flex-direction: column;
  }

  .header-top {
    box-shadow: 0 3px 5px rgba(0, 0, 0, .1)
  }

  @media(min-width: 992px) {
    #wrapper {
      margin-left: 200px;
      padding: 90px 15px 15px;
    }
    .navbar-nav.side-nav {
      background: #585f66;
      box-shadow: 2px 1px 2px rgba(0, 0, 0, .1);
      position: fixed;
      top: 56px;
      flex-direction: column !important;
      left: 0;
      width: 200px;
      overflow-y: auto;
      bottom: 0;
      overflow-x: hidden;
      padding-bottom: 40px
    }
  }
</style>
