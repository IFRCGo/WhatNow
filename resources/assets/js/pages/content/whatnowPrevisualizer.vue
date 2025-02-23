<template>
  <div>
    <div class="modal-content" ref="modalContent">
      <div class="modal-header">
        <div class="logo-section">
          <img v-if="selectedSoc.imageUrl" :src="selectedSoc.imageUrl" class="logo" />
          <span>{{selectedSoc.name}}</span>
        </div>
        <div v-if="contributors" class="contributors-container">
          <div v-for="(contributor, index) in contributors" :key="'contributor-logo-'+index" class="contributor">
            <img :src="contributor.logo" v-if="contributor.logo" />
          </div>
        </div>
      </div>

      <div class="main-container">
        <div class="card-content">
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
    </div>
    <div class="button-container">
      <b-dropdown id="download-dropdown" :text="$t('common.download')" class="primary" variant="outline-primary">
        <b-dropdown-item @click="downloadAsPNG"> {{ $t('common.download_as_image') }} </b-dropdown-item>
        <b-dropdown-item @click="downloadAsPDF">{{ $t('common.download_as_pdf') }}</b-dropdown-item>
      </b-dropdown>
    </div>
  </div>
</template>

<script>
import html2canvas from 'html2canvas'
import jsPDF from 'jspdf'

export default {
  props: ['eventType', 'keyMessage', 'stageName', 'title', 'description', 'contributors', 'selectedSoc', 'selectedLanguage'],
  data() {
    return {
      contributors: [],
    }
  },
  mounted() {
    if (this.selectedSoc.translations) {
      const translation = this.selectedSoc.translations.filter((translation) => translation.languageCode === this.selectedLanguage);
      if (translation.length) {
        this.contributors = translation[0].contributors;
      }
    }
  },
  methods: {
    async downloadAsPNG() {
      const element = this.$refs.modalContent
      const canvas = await html2canvas(element, { allowTaint: true, useCORS: true, logging: true })
      const link = document.createElement('a')
      link.href = canvas.toDataURL('image/png')
      const fileName = this.selectedSoc.name+'_'+this.stageName+'_'+this.eventType+'_'+this.title+".png";
      link.download = fileName
      link.click()
    },
    async downloadAsPDF() {
      const element = this.$refs.modalContent;

      const canvas = await html2canvas(element, {
        scale: window.devicePixelRatio,
        useCORS: true
      });

      const imgData = canvas.toDataURL("image/png");
      const pdf = new jsPDF({
        orientation: "portrait",
        unit: "mm",
        format: "a4"
      });

      const imgWidth = 210;
      const imgHeight = (canvas.height * imgWidth) / canvas.width;
      const fileName = this.selectedSoc.name+'_'+this.stageName+'_'+this.eventType+'_'+this.title+".pdf";
      pdf.addImage(imgData, "PNG", 0, 0, imgWidth, imgHeight);
      pdf.save(fileName);
    }
  }
}
</script>

<style scoped lang="scss">

.card-content {
  background: #fff;
  padding: 30px;
  width: auto;
  border-radius: 8px;
  border-radius: 10px;
  border: solid 1.5px #e6e6e6;
}


.modal-content {
  background: #fff;
  width: auto;
  border-radius: 8px;
  box-shadow: none;
}

.modal-header {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  gap: 50px;
  .contributors-container {
    display: flex;
    align-items: center;
    gap: 30px;

    .contributor {
      padding: 0 40px;
      border-left: 1px solid #cecece;
      img {
        width: 30px;
        height: auto;
      }
    }
  }

}

.logo-section {
  display: flex;
  align-items: center;
}

.logo {
  width: 70px;
  margin-right: 10px;
}

.main-container {
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
  padding: 20px;
  border-top: 1px solid #cecece;

}

</style>
