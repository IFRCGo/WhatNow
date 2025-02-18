<template>
  <div class="previsualizer-container">
    <div class="previsualizer-key-message">
      <div class="previsualizer-key-message-head">
        <div class="previsualizer-key-message-icon">
          <b-img :src="hazardIcon('Animal')" class="rounded-circle" width="60" height="60"></b-img>
        </div>
        <h4>
          <span>Coastal Flood Safety Messages</span>
        </h4>
      </div>
    </div>
  </div>
</template>


<script>

export default {
  props: ['entityId', 'published'],
  data () {
    return {
    }
  },
  methods: {
  },
  mounted () {
  },
  computed: {
  }
}


</script>

<style lang="scss" scoped>
.previsualizer-container {
  background: #ffffff;
  padding: 56px 68px;
}

.modal-lg {
  max-width: auto !important;
}

.previsualizer-key-message {
  min-width: 1100px;

  .previsualizer-key-message-head {
    display: flex;
    align-items: center;
    padding: 33px 50px 70px 50px;
    gap: 40px;
    position: relative;

    h4 {
      font-size: 30px;
      font-weight: 500;
      color: #1e1e1e;
    }

    &::after {
      content: '';
      display: block;
      position: absolute;
      bottom: 0;
      width: 90%;
      height: 1px;
      background: #cecece;
      margin-top: 39px;
    }
  }
}
</style>