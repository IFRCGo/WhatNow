<template>
  <div class="container-fluid">
    <b-row class="pl-4 pr-4">
      <b-col>
        <div class="breadcrumbs">
          <ul class="list-inline">
            <breadcrumb
              v-for="(theRoute, index) in matchedRoute"
              v-if="theRoute.meta.breadcrumb || theRoute.meta.dynamicBc"
              :route="theRoute" :key="theRoute.path"></breadcrumb>
          </ul>
        </div>
      </b-col>
    </b-row>
</div>
</template>

<style>
  /* todo: update styling */
  .breadcrumbs li:not(:last-child):after {
    content: '>';
  }
</style>

<script>
import Breadcrumb from './Breadcrumb.vue'
export default {
  components: {
    Breadcrumb
  },
  name: 'breadcrumbs',
  computed: {
    matchedRoute () {
      return this.$route.matched
    }
  }
}
</script>
