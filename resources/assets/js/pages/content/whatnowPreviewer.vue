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
              <b-img :src="hazardIcon(eventType, hazardsList)" class="rounded-circle" width="40" height="40"></b-img>
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
      <b-dropdown id="download-dropdown" :text="loading ? $t('common.loading_image') : $t('common.download')" class="primary" variant="outline-primary" :disabled="loading">
        <b-dropdown-item @click="downloadAsPNG"> {{ $t('common.download_as_image') }} </b-dropdown-item>
        <b-dropdown-item @click="downloadAsPDF">{{ $t('common.download_as_pdf') }}</b-dropdown-item>
      </b-dropdown>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import html2canvas from 'html2canvas'
import Loading from '../../components/Loading'
import jsPDF from 'jspdf'
export default {
  props: ['eventType', 'keyMessage', 'stageName', 'title', 'description', 'selectedSoc', 'selectedLanguage', 'contributors'],
  components: {
    Loading
  },
  data() {
    return {
      loading: false,
    }
  },
  mounted() {
    this.fetchAllHazardTypes()
  },
  methods: {
    async downloadAsPNG() {
      this.loading = true;
      try {
        const element = this.$refs.modalContent;
        const scaleFactor = 5;
        const fixedWidth = 1200;  
        const fixedHeight = 'auto';

        //the clone is hidden in the background layer
        const clone = element.cloneNode(true);
        clone.style.position = 'absolute';
        clone.style.left = '-9999px';
        clone.style.width = `${fixedWidth}px`;
        clone.style.height = `${fixedHeight}px`;
        document.body.appendChild(clone);

        const canvas = await html2canvas(clone, {
          scale: scaleFactor,
          useCORS: true,
          allowTaint: true,
          backgroundColor: null
        });
        //destroy clone
        document.body.removeChild(clone);

        const link = document.createElement('a');
        link.href = canvas.toDataURL('image/png', 1.0);
        const fileName = `${this.selectedSoc.name}_${this.stageName}_${this.eventType}_${this.title}.png`;
        link.download = fileName;
        link.click();
      } catch (error) {
        this.$bvToast.toast(this.$t('content.previewer.download_image_error'), {
          title: 'Notification',
          variant: 'danger',
          solid: true
        })
      } finally {
        this.loading = false;
      }
    },
    async downloadAsPDF() {
      this.loading = true;
      try {
        const element = this.$refs.modalContent;
        const scaleFactor = 5;
        const fixedWidth = 1200;  
        const fixedHeight = 'auto';

        //the clone is hidden in the background layer
        const clone = element.cloneNode(true);
        clone.style.position = 'absolute';
        clone.style.left = '-9999px';
        clone.style.width = `${fixedWidth}px`;
        clone.style.height = `${fixedHeight}px`;
        document.body.appendChild(clone);


        const canvas = await html2canvas(clone, {
          scale: scaleFactor,
          useCORS: true,
          backgroundColor: null,
        });

        //destroy clone
        document.body.removeChild(clone);

        const imgData = canvas.toDataURL("image/png", 1.0);
        const pdf = new jsPDF({
          orientation: "portrait",
          unit: "mm",
          format: "a4"
        });

        const imgWidth = 210;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;

        if (imgHeight > 297) {
          const pageHeight = 297;
          let yPos = 0;

          while (yPos < imgHeight) {
            pdf.addImage(imgData, "PNG", 0, yPos * -1, imgWidth, imgHeight);
            yPos += pageHeight;
            if (yPos < imgHeight) pdf.addPage();
          }
        } else {
          pdf.addImage(imgData, "PNG", 0, 0, imgWidth, imgHeight);
        }

        const fileName = `${this.selectedSoc.name}_${this.stageName}_${this.eventType}_${this.title}.pdf`;
        pdf.save(fileName);
      } catch (error) {
        this.$bvToast.toast(this.$t('content.previewer.download_pdf_error'), {
          title: 'Notification',
          variant: 'danger',
          solid: true
        })
      } finally {
        this.loading = false;
      }
    },
    async fetchAllHazardTypes() {
      try {
        await this.$store.dispatch('content/fetchHazardTypes')
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
      }
    },
  },
  computed: {
    ...mapGetters({
      hazardsList: 'content/hazardsList',
    })
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
  box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
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
  font-size: 20px;
  img {
    width: 100px;
    height: auto;
  }
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
