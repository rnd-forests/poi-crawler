<template>
  <div id="app">
    <component :is="layout">
      <transition
        name="fade"
        mode="out-in"
      >
        <router-view :layout.sync="layout"/>
      </transition>
    </component>
  </div>
</template>

<script>
export default {
  name: 'App',

  data: () => ({
    layout: 'div'
  }),
}
</script>

<style lang="scss">
@import './assets/style/index.scss';

body {
  font-size: 17px;
}
</style>
