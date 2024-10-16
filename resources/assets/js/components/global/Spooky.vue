<template>
  <div class="skeleton-wrapper">
    <div class="skeleton-item" :style="{ width, height, 'border-radius': radius }"></div>
  </div>
</template>
<script>

export default {
  props: {
    radius: {
      type: String,
      default: '3px'
    },
    height: {
      type: String,
      default: '25px'
    },
    width: {
      type: String,
      default: '200px'
    }
  }
}

</script>
<style lang="scss" scoped>
  .skeleton-wrapper {
    .skeleton-item {
      background: linear-gradient(-45deg, rgb(219, 219, 219), rgb(197, 197, 197));
      background-size: 400% 400%;
      animation: moveGradient 1.2s ease-in-out infinite;
      display: block;
      &.circle {
        border-radius: 50%;
      }
      &.skeleton-break {
        margin-left: 60px;
      }
    }
  }

  @keyframes moveGradient {
    0% {
      background-position: 0% 50%
    }
    60% {
      background-position: 100% 50%
    }
    100% {
      background-position: 0% 50%
    }
  }

</style>
