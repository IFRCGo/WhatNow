<template>
  <transition name="fade" mode="out-in">
    <router-view></router-view>
  </transition>
</template>

<script>
export default {
  loading: false
}
</script>
