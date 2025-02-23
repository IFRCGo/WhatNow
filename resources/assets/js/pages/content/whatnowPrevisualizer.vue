<template>
  <div>
    <div class="modal-content" ref="modalContent"> <!-- Agregar ref aquí -->
      <!-- Header -->
      <div class="modal-header">
        <div class="logo-section">
          <img v-if="selectedSoc.imageUrl" :src="selectedSoc.imageUrl" alt="American Red Cross" class="logo" />
          <span>{{selectedSoc.name}}</span>
        </div>
      </div>

      <!-- Main Content -->
      <div class="modal-body">
        <div class="title-section">
          <div class="previsualizer-key-message-icon small-icon icon-spacing">
            <b-img :src="hazardIcon(eventType)" class="rounded-circle" width="40" height="40"></b-img>
          </div>
          <h4>{{title}}</h4>
        </div>
        <hr>
        <h3>{{ $t('stages.'+stageName) }}</h3>
        <hr>
        <div v-for="(message, index) in keyMessage" :key="index" class="safety-message">
          <p><strong>{{ message.title }}</strong></p>
          <ul v-if="message.content && message.content?.length">
            <li v-for="(item, i) in message.content" :key="i">{{ item }}</li>
          </ul>
        </div>
      </div>
    </div>
    <div class="button-container">
      <b-dropdown id="download-dropdown" text="Download" class="primary">
        <b-dropdown-item @click="downloadAsPNG">Download as PNG</b-dropdown-item>
        <b-dropdown-item @click="downloadAsPDF">Download as PDF</b-dropdown-item>
      </b-dropdown>
  </div>
  </div>
</template>

<script>
import html2canvas from 'html2canvas'
import jsPDF from 'jspdf'

export default {
  props: ['eventType', 'keyMessage', 'stageName', 'title', 'description', 'selectedSoc'],
  methods: {
    async downloadAsPNG() {
      const element = this.$refs.modalContent
      const canvas = await html2canvas(element)
      const link = document.createElement('a')
      link.href = canvas.toDataURL('image/png')
      link.download = 'modal-content.png'
      link.click()
    },
    async downloadAsPDF() {
      const element = this.$refs.modalContent
      const canvas = await html2canvas(element)
      const imgData = canvas.toDataURL('image/png')
      const pdf = new jsPDF({
        orientation: 'portrait',
        unit: 'mm',
        format: 'a4'
      })

      const pageWidth = 265
      const pageHeight = 245
      pdf.setFontSize(12)

      pdf.addImage(imgData, 'PNG', 0, 0, pageWidth, pageHeight)
      pdf.save('modal-content.pdf')
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
  padding: 30px;
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
.button-container {
  display: flex;
  justify-content: flex-end;
  margin-top: 30px;
  margin-bottom: 10px;
}

</style>
