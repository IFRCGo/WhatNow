<template>
  <div class="c-api-stats-card">
    <div class="c-api-stats-card__upper">
      <div class="c-api-stats-card__icon">
        <b-img :src="hazardIcon(icon)" width="21" fluid role="presentation" />
      </div>

      <h3 class="c-api-stats-card__title">{{ title }}</h3>
    </div>

    <p class="c-api-stats-card__value">
      {{ value.toLocaleString() }}
    </p>
  </div>
</template>

<script>
export default {
  props: {
    title: String,
    icon: String,
    value: Number
  }
}
</script>
