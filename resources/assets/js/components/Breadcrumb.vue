<template>
  <li class="list-inline-item">
    <router-link class="underlined-link" v-if="route.meta.dynamicBc" :to="{ name: linkName, params: getParams() }">
      <template v-if="value">
        {{ formattedValue }}
      </template>
      <template v-if="!value">
        {{ loadingText }}
      </template>
    </router-link>
    <router-link class="underlined-link" v-else-if="route.meta.breadcrumb" :to="{ name: route.name }">{{ route.meta.breadcrumb }}</router-link>
  </li>
</template>
<script>
export default {
  props: ['route', 'isLast'],
  template: 'breadcrumb',
  beforeCreate () {
    this.$options.computed.value = () => {
      if (this.route.meta.dynamicBcGetter) {
        return this.$store.getters[this.route.meta.dynamicBcGetter]
      } else {
        return null
      }
    }
  },
  methods: {
    getParams () {
      let props = {}
      if (this.route.meta.dynamicBcProps) {
        this.route.meta.dynamicBcProps.forEach(prop => {
          props[prop] = this.$route.params[prop]
        })
      }
      return props
    }
  },
  computed: {
    formattedValue () {
      return this.route.meta.linkBcText(this.value())
    },
    loadingText () {
      const loadingText = this.route.meta.loadingBcText
      return loadingText || this.route.meta.breadcrumb || 'Loading'
    },
    linkName () {
      if (this.route.meta.linkNameBc) {
        return this.route.meta.linkNameBc
      }
      return this.route.meta
    }
  }
}
</script>
