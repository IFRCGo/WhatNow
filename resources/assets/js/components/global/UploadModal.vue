<template>
  <div>
    <!-- Modal -->
    <b-modal v-model="showModal" title="Upload File" @hide="resetModal" hide-footer>
      <div class="upload-area" @click="triggerFileInput" v-if="!file">
        <input type="file" ref="fileInput" @change="handleFileUpload" class="file-input" />
        <p>{{ $t('upload_modal.drag_button_text') }}</p>
      </div>

      <div v-if="file" class="file-preview">
        <p>{{ file.name }} ({{ (file.size / 1024 / 1024).toFixed(2) }} MB)</p>
        <b-button variant="danger" size="sm" class="thin-x" @click="removeFile">X</b-button>
      </div>

      <b-button v-if="file && !uploading" @click="uploadFile" class="global-button-style">{{$t('upload_modal.button_text')}}</b-button>

      <b-progress v-if="uploading" :max="100" :value="progress" class="mb-3" :variant="uploadFailed ? 'danger' : 'success'"></b-progress>
      <p v-if="uploading">{{$t('common.upploading')}}... {{ progress }}%</p>
    </b-modal>
  </div>
</template>

<script>
import axios from 'axios'
import { BToast } from 'bootstrap-vue'

export default {
  props: ['disabled', 'showModal', 'fileName'],
  data() {
    return {
      file: null,
      uploading: false,
      progress: 0,
      uploadFailed: false,
      uploadSuccess: false,
      errorMessage: ""
    }
  },
  methods: {
    triggerFileInput() {
      this.$refs.fileInput.click()
    },
    handleFileUpload(event) {
      const file = event.target.files[0];
      const fileExtension = file.name.split('.').pop()

      const renamedFile = new File([file], this.fileName + '.' + fileExtension, { type: file.type });
      
      this.file = renamedFile;

      if (!this.file) return

      // Validate file type
      const allowedTypes = ["image/png", "image/jpeg"]
      if (!allowedTypes.includes(this.file.type)) {
        this.showNotification("Only PNG and JPG files are allowed.", "danger")
        this.file = null
        return
      }

      // Validate file size (10MB limit)
      if (this.file.size > 10 * 1024 * 1024) {
        this.showNotification("File size must not exceed 10MB.", "danger")
        this.file = null
        return
      }
    },
    removeFile() {
      this.file = null
    },
    async uploadFile() {
      if (!this.file) return

      const formData = new FormData()
      formData.append('file', this.file)

      this.uploading = true
      this.progress = 0
      this.uploadFailed = false
      this.uploadSuccess = false

      try {
        const response = await axios.post('/api/upload-file', formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
          onUploadProgress: progressEvent => {
            this.progress = Math.round((progressEvent.loaded * 100) / progressEvent.total)
          }
        })

        if (response.status === 200) {
          this.uploadSuccess = true
          this.showNotification("Upload successful!", "success")
          setTimeout(() => {
            this.showModal = false
          }, 1500) // Delay to show success message
        } else {
          this.uploadFailed = true
          this.showNotification("Upload failed. Please try again.", "danger")
        }

        this.$emit('fileUploaded', response.data)
      } catch (error) {
        this.uploadFailed = true
        this.showNotification("An error occurred during upload.", "danger")
        console.error(error)
      } finally {
        setTimeout(() => {
          this.uploading = false
          this.file = null
        }, 1500)
      }
    },
    resetModal() {
      this.file = null
      this.uploading = false
      this.progress = 0
      this.uploadFailed = false
      this.uploadSuccess = false
      this.errorMessage = ""
      this.$emit('modalReset')
    },
    showNotification(message, variant) {
      this.$bvToast.toast(message, {
        title: 'Notification',
        variant: variant,
        solid: true
      })
    }
  }
}
</script>

<style scoped>
.upload-area {
  border: 2px dashed #d3d3d3;
  border-radius: 5px;
  padding: 50px;
  text-align: center;
  cursor: pointer;
  transition: background-color 0.3s ease;
}
.upload-area:hover {
  background-color: #f8f9fa;
}
.file-input {
  display: none;
}
.file-preview {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f8f9fa;
  padding: 10px;
  border-radius: 5px;
  margin-bottom: 10px;
}
.thin-x {
  font-size: 12px;
  padding: 2px 5px;
}
.b-progress {
  transition: width 0.5s ease;
}
</style>
