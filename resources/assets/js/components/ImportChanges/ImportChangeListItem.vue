<template>
  <div class="mb-1">
    <b-badge :variant="mapBadgeStyle(importAction)">{{ $t(`content.bulk_upload.import_banner.${importAction}`) }}</b-badge>
    <span class="font-weight-bold">
      <slot />
    </span>
  </div>
</template>

<script>
export default {
  props: ['importAction'],
  methods: {
    mapBadgeStyle (importAction) {
      switch (importAction) {
        case 'added':
          return 'success'
        case 'updated':
          return 'secondary'
        case 'skipped':
          return 'warning'
        case 'failed':
        case 'invalid':
        default:
          return 'danger'
      }
    }
  }
}
</script>
