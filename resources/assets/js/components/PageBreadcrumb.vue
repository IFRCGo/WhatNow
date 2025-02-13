<template>
  <div class="bg-white">
    <div v-if="breadcrumbs" class="d-flex">
      <div v-for="crumb in breadcrumbs" class="mr-2 d-flex align-items-center page-breadcrumbs">
        <fa icon="chevron-left" class="breadcrumb-separator" />
        <b-link variant="breadcrumb-link" :class="`breadcrumb-link ${!crumb.name ? 'btn-link-no-underline' : ''}`"
          :to="crumb">{{ crumb.text }}</b-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    breadcrumbs: {
      type: Array
    }
  }
}
</script>

<style scoped lang="scss">


.page-breadcrumbs {
  .breadcrumb-separator {
    font-size: 10px;
    color: #000;
    margin-right: 4px;
  }
  .breadcrumb-link {
    font-size: 14px;
    color: #000;
    text-transform: capitalize;
    text-decoration: none;
  }
}

</style>