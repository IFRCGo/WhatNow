<template>
  <b-row class="page-banner pl-4 pr-4">
    <b-col>
      <b-row class="page-banner-breadcrumbs">
        <b-col v-if="breadcrumbs" class="d-flex">
          <div v-for="crumb in breadcrumbs" class="mr-2 d-flex align-items-center">
            <fa icon="chevron-left" class="breadcrumb-separator"/>
            <b-button variant="breadcrumb-link" :class="`text-uppercase ${!crumb.name ? 'btn-link-no-underline' : ''}`" :to="crumb">{{ crumb.text }}</b-button>
          </div>
        </b-col>
      </b-row>
      <b-row align-h="between" align-v="center" >
        <slot>
        </slot>
      </b-row>
    </b-col>
  </b-row>
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
