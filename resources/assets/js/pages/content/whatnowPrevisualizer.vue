<template>
   <div class="modal-content">
      <!-- Header -->
      <div class="modal-header">
        <div class="logo-section">
          <img src="https://whatnowimages.blob.core.windows.net/images/American-Red-Cross_Logo_1200x630.jpg" alt="American Red Cross" class="logo" />
          <span>American Red Cross</span>
        </div>
      </div>

      <!-- Main Content -->
      <div class="modal-body">
        <div class="title-section">
          <div class="previsualizer-key-message-icon small-icon icon-spacing">
            <b-img :src="hazardIcon('Animal')" class="rounded-circle" width="40" height="40"></b-img>
          </div>
          <h4>Coastal Flood Safety Messages</h4>
        </div>

        <h3>WATCH</h3>

        <div v-for="(message, index) in safetyMessages" :key="index" class="safety-message">
          <p><strong>{{ message.title }}</strong></p>
          <ul v-if="message.support && message.support.length">
            <li v-for="(item, i) in message.support" :key="i">{{ item }}</li>
          </ul>
        </div>
      </div>
    </div>
</template>

<script>
export default {
  data() {
    return {
      safetyMessages: [
        { title: "Safety message 1", support: ["Support message 1", "Support message 2", "Support message 3"] },
        { title: "Safety message 2", support: ["Support message 1"] },
        { title: "Safety message 3", support: [] },
        { title: "Safety message 4", support: ["Support message 1", "Support message 2"] }
      ]
    }
  }
}
</script>

<style scoped>

.modal-content {
  background: #fff;
  padding: 30px;
  width: auto;
  border-radius: 8px;
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}


.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo-section {
  display: flex;
  align-items: center;
}

.logo {
  width: 70px;
  margin-right: 10px;
}

.modal-body {
  padding: 15px 0;
}

.title-section {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}

.icon-spacing {
  margin-right: 10px;
}

.safety-message {
  margin-bottom: 10px;
  padding-bottom: 10px;
  border-bottom: 1px solid #ddd;
}

.safety-message:last-child {
  border-bottom: none;
}


.modal-footer {
  margin-top: 20px;
}

</style>
