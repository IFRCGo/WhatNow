<template>
  <b-container fluid>
    <page-banner>
      <b-col sm="auto" md="4">
        <h1 class="sec-title">{{ $t('content.bulk_upload.bulk_upload') }}</h1>
        <h6>{{ $t('content.bulk_upload.bulk_subtitle') }}</h6>
      </b-col>
      <b-col sm="auto" md="6">
        <div class="d-flex justify-content-end u-gap-24 flex-wrap flex-md-nowrap w-100">
          <selectSociety class="mr-3" v-model="selectedSoc" :societyList="filteredSocieties" :countryCode="countryCode">
          </selectSociety>
          <selectRegion v-model="selectedRegion" :socCode="selectedSoc.countryCode" class="flex-grow-1"></selectRegion>
        </div>
      </b-col>
    </page-banner>

    <b-row class="pl-4 pr-4 pb-4 bg-white">
      <b-col cols="4">
        <h1 class="mb-4">{{ $t('content.bulk_upload.export_template') }}</h1>
        <span v-if="isFetchingLangs"><fa class="ml-2" spin :icon="['fas', 'spinner']"/></span>
        <span v-else>
               <v-select
                 :dir="isLangRTL(locale) ? 'rtl' : 'ltr'"
                 v-model="languageFilter"
                 class="w-100 v-select-custom bulk-select mr-4"
                 :options="filteredLanguages"
                 label="text" :disabled="filteredLanguages.length === 0"
                 :placeholder="$t('content.audit_log.select_language')"
                 @input="handleExportLangSelection">
                    <template slot="option" slot-scope="option">
                     <div class="ml-2 rtl-mr-2 dropdown-option">
                    {{ option.text }}
                     </div>
                    </template>
                    <template slot="selected-option" slot-scope="option">
                    <div class="ml-2 rtl-mr-2 dropdown-option" >
                     {{ option.text }}
                    </div>
                    </template>
               </v-select>
          </span>
        <b-button class="mr-2 mb-4 mt-4 btn-outline-primary dw-btn" prop='link' @click="downloadTemplate('xlsx')">
            <span>
              <i class="fas fa-download"></i>
            </span>
          {{ $t('content.bulk_upload.template') }}
        </b-button>
        <p class="mt-4 bulk-text">
          {{ $t('content.bulk_upload.refer') }}
          <a :href="pdf" target="_blank" rel="noopener noreferrer" class="underlined-link" @click="$fireGTEvent($gtagEvents.DownloadExportTemplateGuide)">{{ $t('content.bulk_upload.export_template_guide') }}</a>
          {{ $t('content.bulk_upload.detail') }}
        </p>
      </b-col>
    </b-row>
    <hr>
    <b-form @submit.prevent="uploadFile" v-if="can(user, permissionsList.CONTENT_EDIT)">
      <b-row class="pl-4 pr-4 pt-4">
        <b-col cols="6" v-if="!uploadResults">
          <h1>{{ $t('content.bulk_upload.import_data') }}</h1>
        </b-col>

        <b-col cols="6" v-else>
          <h1>{{ $t('content.bulk_upload.import_complete') }}</h1>

          <p>{{ $t('content.bulk_upload.import_summary', { society: selectedSoc.name, lang: languages[selectedLanguage].name }) }}</p>
        </b-col>
      </b-row>


      <b-card class="pb-4 pt-4 pl-4 pr-4" v-if="!uploadResults">
        <b-row>
          <b-col cols="8">
            <b-alert show variant="warning" v-if="uploadWarning">{{ uploadWarning }}</b-alert>
            <b-alert show variant="danger" v-if="uploadError">{{ uploadError }}</b-alert>
            <b-row>
              <b-col cols="12" xl="6">
                <div class="mt-4">
                  <p class="bulk-text">{{ $t('content.bulk_upload.file_instructions') }}</p>
                  <b-form-file  class="file-inp" ref="fileinput" accept=".csv;*.xlsx" :choose-label="$t('content.bulk_upload.choose')" id="file_input1" v-model="importForm.file"></b-form-file>
                </div>
                <hr>
                <div class="mt-4">
                  <p class="bulk-text"> {{ $t('content.bulk_upload.choose_file') }} </p>
                  <div>
                    <label class="custom-radio">
                      <input type="radio" v-model="importForm.selectedFileType" value="xlsx" name="fileType" /> XLS
                    </label>
                    <label class="ml-4 custom-radio">
                      <input type="radio" v-model="importForm.selectedFileType" value="csv" name="fileType" /> CSV
                    </label>
                  </div>
                </div>
                <hr>
                <div class="mt-4">
                  <p class="bulk-text">{{ $t('content.bulk_upload.language_instructions') }}</p>
                  <v-select
                    :dir="isLangRTL(locale) ? 'rtl' : 'ltr'"
                    v-model="importForm.selectedImportLang"
                    class="w-100 v-select-custom bulk-select-2 mr-4"
                    :options="allLanguages"
                    label="text" :disabled="allLanguages.length === 0"
                    :placeholder="$t('content.audit_log.select_language')"
                    @input="handleImportLangSelection">
                    <template slot="option" slot-scope="option">
                      <div class="ml-2 rtl-mr-2 dropdown-option">
                        {{ option.text }}
                      </div>
                    </template>
                    <template slot="selected-option" slot-scope="option">
                      <div class="ml-2 rtl-mr-2 dropdown-option">
                        {{ option.text }}
                      </div>
                    </template>
                  </v-select>
                </div>
                <hr v-if="importForm.file">
                <div class="mt-4" v-if="importForm.file && importForm.selectedImportLang && importForm.selectedFileType">
                  <!-- Submit Button -->
                  <v-button :loading="isUploading" class="btn-dark w-100 mt-4">
                    {{ $t('content.bulk_upload.submit') }}
                  </v-button>
                </div>
              </b-col>
              <b-col sm="12" xl="6">
                <div class="mt-4">
                  <p class="bulk-text">{{ $t('content.bulk_upload.warnings.title') }}</p>
                  <div class="btns-container">
                      <b-form-radio-group v-model="importForm.warnings" buttons button-variant="outline-primary">
                      <b-form-radio class="new-btn btn-yes" value="true">{{ $t('Yes') }}</b-form-radio>
                      <b-form-radio class="btn-no" value="false">{{ $t('No') }}</b-form-radio>
                    </b-form-radio-group>
                  </div>
                </div>
                <hr>
                <div class="mt-4" v-if="importForm.warnings" >
                  <p class="bulk-text">{{ $t('content.bulk_upload.overwriting.title') }}</p>
                  <v-select

                    v-model="importForm.overwrite"
                    class="w-100 v-select-custom bulk-select-3 mr-4"
                    :options="overwriteOptions"
                    label="text" :disabled="overwriteOptions[0].text === ''"
                    :placeholder="$t('content.audit_log.select_overwrite')">
                    >
                    <template slot="option" slot-scope="option">
                      <div class="ml-2 rtl-mr-2 dropdown-option">
                        {{ option.text }}
                      </div>
                    </template>
                    <template slot="selected-option" slot-scope="option">
                      <div class="ml-2 rtl-mr-2 dropdown-option">
                        {{ option.text }}
                      </div>
                    </template>
                  </v-select>
                </div>
              </b-col>
            </b-row>
          </b-col>
        </b-row>
      </b-card>
    </b-form>

    <b-card class="pb-4 pt-4 pl-4 pr-4" v-if="uploadResults">
      <import-changes-list :importResults="uploadResults.importSummary" />

      <b-button class="mr-2 mb-2" @click="resetForm">
        {{ $t('content.bulk_upload.reset') }}
      </b-button>
    </b-card>

  </b-container>
</template>

<script>
import SelectSociety from '~/pages/content/simpleSocietyPicker'
import { mapGetters } from 'vuex'
import { languages } from 'countries-list'
import SelectRegion from '~/pages/content/regionPicker'
import axios from 'axios'
import PageBanner from '~/components/PageBanner'
import ImportChangesList from '~/components/ImportChanges/ImportChangesList'
import * as permissionsList from '../../store/permissions'
import pdfFile from '../../../pdf/what-now-csv-guide.pdf'
import swal from 'sweetalert2'
export default {
  components: {
    SelectSociety,
    SelectRegion,
    PageBanner,
    ImportChangesList
  },

  data () {
    return {
      uploadWarning: null,
      uploadError: null,
      isFetchingLangs: false,
      uploadResults: null,
      isUploading: false,
      importForm: {
        file: null,
        warnings: true,
        selectedImportLang: null,
        selectedFileType: null,
        overwrite: { value: false, text: this.$t('content.bulk_upload.overwriting.off') },
        errors: {}
      },
      selectedSoc: null,
      selectedRegion: null,
      selectedLanguage: false,
      selectedExportLang: null,
      languages,
      pdf: pdfFile,
      permissionsList: permissionsList,
      warningOptions: [
        { value: true, text: this.$t('content.bulk_upload.warnings.on') },
        { value: false, text: this.$t('content.bulk_upload.warnings.off') }
      ],
      overwriteOptions: [
        { value: false, text: this.$t('content.bulk_upload.overwriting.off') },
        { value: true, text: this.$t('content.bulk_upload.overwriting.on') }
      ]
    }
  },
  created () {
    this.fetch()
  },
  watch: {
    selectedLanguage: {
      handler (val, oldVal) {
        if (val !== oldVal) {
          this.languageChange()
        }
      },
      deep: true
    },
    selectedSoc: {
      handler (val, oldVal) {
        if (oldVal && val !== null && val.countryCode !== oldVal.countryCode) {
          this.setLocalStorage()
          this.fetch()
        }
      },
      deep: true
    },
    selectedRegion: {
      handler (val, oldVal) {
        if (val !== oldVal) {
          this.fetchContent()
        }
      },
      deep: true
    }
  },
  methods: {
    async fetch (silent = false) {
      this.loadingContent = !silent
      await this.fetchOrganisations()
      this.getLocalStorage()
      await this.fetchContent()
      this.loadingContent = false
    },
    handleExportLangSelection (lang) {
      this.selectedExportLang = lang.value
    },
    handleImportLangSelection (lang) {
      this.importForm.selectedImportLang = lang.value
    },
    async fetchOrganisations () {
      this.isFetchingLangs = true
      try {
        await this.$store.dispatch('content/fetchOrganisations')
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
      }
    },
    async fetchContent () {
      if (this.selectedSoc) {
        try {
          await this.$store.dispatch('content/fetchContent', this.selectedSoc.countryCode)
          this.languageChange()
        } catch (e) {
          this.$noty.error(this.$t('error_alert_text'))
        }
      }
    },

    async uploadFile (evt) {

      this.uploadWarning = null
      this.uploadError = null
      this.isUploading = true
      const formData = new FormData()
      formData.append('warnings', this.importForm.warnings)
      formData.append('overwrite', this.importForm.overwrite.value)
      formData.append('fileType', this.importForm.selectedFileType)
      formData.append(this.importForm.selectedFileType, this.importForm.file)
      await axios.post(`/api/import/${this.selectedSoc.countryCode}/${this.importForm.selectedImportLang}`, formData)
        .then((response) => {
          this.uploadResults = response.data
          this.$noty.success('Success')
        })
        .catch((error) => {
          this.isUploading = false
          switch (error.response.status) {
            case 409:
              this.uploadWarning = this.$t('content.bulk_upload.errors.warning_update_will_overwrite')
              break
            case 400:
              swal({
                icon: 'error',
                type: 'error',
                title: 'Message File Upload error',
                text: error.response.data.message || 'An error occurred'
              })
              if (error.response.data.errorCode !== undefined) {
                this.uploadError = this.$t('content.bulk_upload.errors.bad_request_error_codes.' + error.response.data.errorCode)
              } else {
                this.uploadError = this.$t('content.bulk_upload.errors.bad_request')
              }
              break
            default:
              this.uploadError = this.$t('error_alert_text')
              console.error(error)
              break
          }
        })
        .finally(() => {
          this.isUploading = false
        })

      this.$fireGTEvent(this.$gtagEvents.BulkDataImport)
    },
    resetForm () {
      this.uploadResults = null
      this.importForm.file = null
      this.importForm.warnings = true
      this.importForm.overwrite.value = true
      this.importForm.overwrite.text = this.$t('content.bulk_upload.overwriting.on')
      this.clearFiles()
    },
    downloadTemplate (extension = 'xlsx') {
      const langEndpoint = this.selectedExportLang ? `/${this.selectedExportLang}` : ''
      const params = new URLSearchParams()
      params.append('extension', extension)
      if (this.selectedRegion) params.append('subnational', this.selectedRegion.title)
      axios.get(`/api/template/${this.selectedSoc.countryCode}${langEndpoint}?${params.toString()}`, {
        responseType: 'blob'
      })
        .then((response) => {
          var headers = response.headers
          var blob = new Blob([response.data], { type: headers['content-type'] })

          if (window.navigator.msSaveOrOpenBlob) {window.navigator.msSaveBlob(blob, this.selectedSoc.countryCode + '.' + extension)
          } else {
            var link = document.createElement('a')
            link.href = window.URL.createObjectURL(blob)
            link.download = this.selectedSoc.countryCode + '.' + extension
            link.click()
          }
        })
        .catch((error) => {
          this.$noty.error(this.$t('error_alert_text'))
          console.error(error)
        })

      this.$fireGTEvent(this.selectedExportLang ? this.$gtagEvents.ExportLanguageData(this.selectedExportLang) : this.$gtagEvents.DownloadBulkTemplate)
    },
    clearFiles () {
      this.$refs.fileinput.reset()
    },
    getLocalStorage () {
      let soc = localStorage.getItem('soc')
      const lang = localStorage.getItem('lang')

      if (!soc || !lang) {
        soc = this.filteredSocieties[0].countryCode
      }

      this.selectedSoc = this.societies.find(society => society.countryCode === soc)
      this.selectedLanguage = lang
    },
    setLocalStorage () {
      localStorage.setItem('soc', this.selectedSoc.countryCode)
      localStorage.setItem('lang', this.selectedLanguage)
    },
    languageChange () {
      if (this.selectedSoc) {
        this.setLocalStorage()
        this.currentTranslations = this.currentContent.map((content) => {
          const flattened = JSON.parse(JSON.stringify(content))
          if (content.translations) {
            flattened.currentTranslation = content.translations[this.selectedLanguage]
          }
          this.isFetchingLangs = false
          return flattened
        })
      }
      this.isFetchingLangs = false
    }
  },
  computed: {
    filteredLanguages () {
      const languages = [{ value: null, text: this.$t('content.bulk_upload.empty_template') }]
      if (this.selectedSoc) {
        this.selectedSoc.translations.forEach((translation) => {
          const { languageCode } = translation
          languages.push({
            value: languageCode,
            text: `${this.languages[languageCode].name}  (${this.languages[languageCode].native} - ${languageCode})`
          })
        })
      }

      return languages
    },
    allLanguages () {
      const languages = []
      for (const key in this.languages) {
        languages.push({
          value: key,
          text: `${this.languages[key].name}  (${this.languages[key].native} - ${key})`
        })
      }

      return languages
    },
    filteredSocieties () {
      if (this.user) {
        if (this.can(this.user, permissionsList.ALL_ORGANISATIONS)) {
          return this.societies
        }
        return this.societies.filter(soc => this.user.data.organisations.indexOf(soc.countryCode) !== -1)
      }
      return this.societies
    },
    ...mapGetters({
      user: 'auth/user',
      societies: 'content/organisations',
      currentContent: 'content/currentContent',
      currentLanguages: 'content/currentLanguages'
    })
  }
}
</script>
<style scoped>

.custom-file-label {
  background: #F7F7F7;
  border-radius: 10px;
  font-size: 1rem;
  border: none;
}
.bulk-select {
  width: 26%;
  display: inline-block;
  input {
    line-height: 0;
    margin-top: 0rem;
  }
}
.bulk-select-2 {
  width: 45%!important;
  display: inline-block;
  input {
    line-height: 0;
    margin-top: 0rem;
  }
}
.bulk-select-3 {
  display: inline-block;
  input {
    line-height: 0;
    margin-top: 0rem;
  }
}
.custom-radio input:checked + span {
  background-color: red;
  border-color: red;
}
.btn-yes {
  font-size: 1rem;
  line-height: 1rem;
  padding: 5px 20px;
}
.btn-no {
  font-size: 1rem;
  line-height: 1rem;
  padding: 8px 23px;
  margin-left: 1rem;
}
.btns-container {
  width: 100%;
  display: flex;
  justify-content: flex-end;
  align-content: center;
  & > div {
    display: flex;
    gap: 5px;
    & label {
      border-radius: 20px;
      align-content: center;
      justify-content: center;
    }
  }
}
.bulk-text {
  font-size: 0.8rem;
}
</style>
