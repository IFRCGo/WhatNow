<template>
  <transition name="fade">
    <div v-if="loading" class="table-load-overlay d-flex justify-content-center align-items-center">
      <fa :icon="['fas', 'circle-notch']" spin size="6x"/>
    </div>
  </transition>
</template>
<script>
export default {
  props: ['loading']
}
</script>
