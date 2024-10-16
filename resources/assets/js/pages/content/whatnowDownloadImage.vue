<template>
  <div class="hazard-card-download-container d-flex align-items-center ml-2">
    <img v-if="!downloadingImage" :src="src('downloadIcon')" :srcSet="srcSet('downloadIcon')" alt="" class="hazard-card-download-container-icon"/>
    <fa  v-else class="" spin :icon="['fas', 'spinner']"/>
    <span class="ml-1 rtl-mr-1">
      <button
        @click="downloadImage"
        :disabled="downloadingImage"
        class="btn-link underlined-link hazard-card-download-container-button">{{ $t('content.edit_whatnow.download_image')}}</button> (.jpg)
    </span>
    <b-modal ref="save-hazard-card-modal" hide-footer size="lg" :title="$t('content.edit_whatnow.download_image')">
      <img class="w-100 mb-2 rounded box-shadow" v-if="imageData" :src="imageData" :alt="$t('content.edit_whatnow.downloaded_image_alt')"/>
      <div class="d-block text-center">
        <p v-if="!downloadImageError">{{ $t('content.edit_whatnow.download_instructions')}}</p>
      </div>
    </b-modal>
  </div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'

export default {
  props: ['instructionId', 'langCode', 'instructionName'],
  data () {
    return {
      downloadingImage: false,
      downloadImageError: false,
      imageData: null
    }
  },
  methods: {
    async downloadImage () {
      this.downloadingImage = true
      const url = `/api/instructions/${this.instructionId}/${this.langCode}/${this.instructionName}/download`
      try {
        const response = await axios.get(url, { responseType: 'blob' })
        if (response.status === 200) {
          // We have an image.
          this.imageData = URL.createObjectURL(response.data)
          this.$refs['save-hazard-card-modal'].show()
        }
      } catch (err) {
        if (err.response && err.response.status === 404) {
          this.$noty.error(this.$t('content.edit_whatnow.download_image_error'))
        }
        // Other errors are handled in /resources/assets/js/plugins/axios.js
      }

      this.downloadingImage = false
    }
  },
  computed: {
    ...mapGetters({
      currentOrganisation: 'content/currentOrganisation'
    })
  }
}
</script>
