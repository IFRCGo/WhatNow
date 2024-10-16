<template>
  <b-row class="c-action-banner px-4 align-items-center justify-content-between">
    <b-col cols="auto">
      <h2 class="h1 m-0 font-weight-bold text-light">{{ title }}</h2>
    </b-col>

    <b-col cols="auto">
      <slot />
    </b-col>
  </b-row>
</template>

<script>
export default {
  props: {
    title: {
      type: String
    }
  }
}
</script>
