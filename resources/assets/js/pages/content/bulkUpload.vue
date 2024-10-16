  <template>
    <b-container fluid>
      <page-banner>
        <b-col>
          <h1>{{ $t('content.bulk_upload.bulk_upload') }}</h1>
        </b-col>
        <b-col>
        <selectSociety class="ml-auto rtl-mr-auto" v-model="selectedSoc" v-on:languageChange="languageChange" :societyList="filteredSocieties"></selectSociety>
        </b-col>
      </page-banner>
      <b-row class="pl-4 pr-4 pb-4 pt-4 bg-white">
          <b-col cols="7">
          <h1>{{ $t('content.bulk_upload.export_template') }}</h1>
          <b-button class="mr-2 mb-2" prop='link' @click="downloadBlankTemplate">
              {{ $t('content.bulk_upload.template') }}
          </b-button>
          <span v-if="isFetchingLangs"><fa class="ml-2" spin :icon="['fas', 'spinner']"/></span>
          <span v-else>
              <b-button class="mr-2 mb-2" v-for="lang in currentLanguages" prop='link' @click="downloadTemplate(lang)" :key="lang">
                  {{ languages[lang].name }} ({{languages[lang].native}} - {{lang}})
              </b-button>
          </span>
          <p>
            {{ $t('content.bulk_upload.export_template_instructions') }}
            <a :href="pdf" target="_blank" rel="noopener noreferrer" class="underlined-link" @click="$fireGTEvent($gtagEvents.DownloadExportTemplateGuide)">{{ $t('content.bulk_upload.export_template_guide') }}</a>
          </p>
          </b-col>
      </b-row>

      <b-form @submit="uploadCsv" v-if="can(user, permissionsList.CONTENT_EDIT)">
          <b-row class="pl-4 pr-4 mb-1 pt-4">
            <b-col cols="6" v-if="!uploadResults">
              <h1>{{ $t('content.bulk_upload.import_data') }}</h1>
            </b-col>

            <b-col cols="6" v-else>
              <h1>{{ $t('content.bulk_upload.import_complete') }}</h1>

              <p>{{ $t('content.bulk_upload.import_summary', { society: selectedSoc.name, lang: languages[selectedLanguage].name }) }}</p>
            </b-col>
          </b-row>


          <b-card class="pb-4 pt-4 pl-4 pr-4" v-if="!uploadResults">
             <b-alert show variant="warning" v-if="uploadWarning">{{ uploadWarning }}</b-alert>
             <b-alert show variant="danger" v-if="uploadError">{{ uploadError }}</b-alert>
             <b-row>
                <b-col sm="12" xl="6">
                    <div class="mt-4">
                        <p>{{ $t('content.bulk_upload.file_instructions') }}</p>
                        <b-form-file ref="fileinput" accept=".csv" :choose-label="$t('content.bulk_upload.choose')" id="file_input1" v-model="file"></b-form-file>
                    </div>
                    <!--<div v-if="file !== null" class="mt-4 bg-grey pt-4 pb-4 pl-4 pr-4">-->
                        <!--Selected file: {{file && file.name}}-->
                        <!--<b-button @click="clearFiles">Reset</b-button>-->
                    <!--</div>-->
                    <div class="mt-4">
                        <p>{{ $t('content.bulk_upload.language_instructions') }}</p>
                        <b-form-select v-model="selectedLanguage" class="w-100" :options="filteredLanguages" label="name"></b-form-select>
                    </div>
                    <div class="mt-4" v-if="file">
                        <!-- Submit Button -->
                        <v-button :loading="isUploading" class="btn-dark w-100 mt-4">
                            {{ $t('content.bulk_upload.submit') }}
                        </v-button>
                    </div>
                </b-col>
              <b-col sm="12" xl="6">
                    <div class="mt-4">
                      <p>{{ $t('content.bulk_upload.warnings.title') }}</p>
                      <b-form-select v-model="warnings" :options="warningOptions"></b-form-select>
                    </div>
                    <div class="mt-4" v-if="!warnings">
                      <p>{{ $t('content.bulk_upload.overwriting.title') }}</p>
                      <b-form-select v-model="overwrite" :options="overwriteOptions"></b-form-select>
                    </div>
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
import swal from 'sweetalert2'
import SelectSociety from '~/pages/content/simpleSocietyPicker'
import { mapGetters } from 'vuex'
import { languages } from 'countries-list'
import axios from 'axios'
import PageBanner from '~/components/PageBanner'
import ImportChangesList from '~/components/ImportChanges/ImportChangesList'
import * as permissionsList from '../../store/permissions'
import pdfFile from '../../../pdf/what-now-csv-guide.pdf'

export default {
  components: {
    SelectSociety,
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
      file: null,
      selectedSoc: null,
      selectedLanguage: false,
      warnings: true,
      overwrite: false,
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
    async fetchOrganisations () {
      this.isFetchingLangs = true
      try {
        await this.$store.dispatch('content/fetchOrganisations')
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
      }
    },
    async fetchContent () {
      try {
        await this.$store.dispatch('content/fetchContent', this.selectedSoc.countryCode)
        this.languageChange()
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
      }
    },
    async uploadCsv (evt) {
      this.uploadWarning = null
      this.uploadError = null
      this.isUploading = true
      evt.preventDefault()
      const formData = new FormData()
      formData.append('warnings', this.warnings)
      formData.append('overwrite', this.overwrite)
      formData.append('csv', this.file)
      await axios.post(`/api/import/${this.selectedSoc.countryCode}/${this.selectedLanguage}`, formData)
        .then((reponse) => {
          this.uploadResults = reponse.data
          this.isUploading = false
          this.$noty.success('Success')
        })
        .catch((error) => {
          this.isUploading = false
          switch (error.response.status) {
            case 409:
              this.uploadWarning = this.$t('content.bulk_upload.errors.warning_update_will_overwrite')
              break
            case 400:
              if (error.response.data.errorCode !== undefined) {
                this.uploadError = this.$t('content.bulk_upload.errors.bad_request_error_codes.' + error.response.data.errorCode)
              } else {
                this.uploadError = this.$t('content.bulk_upload.errors.bad_request')
              }

              break
            default:
              this.uploadError = this.$t('error_alert_text')
              console.error(e)
              break
          }
        })

      this.$fireGTEvent(this.$gtagEvents.BulkDataImport)
    },
    resetForm () {
      this.uploadResults = null
      this.file = null
      this.warnings = true
      this.overwrite = false
    },
    downloadBlankTemplate () {
      axios.get(`/api/template/${this.selectedSoc.countryCode}`, {
        responseType: 'blob'
      })
        .then((response) => {
          var headers = response.headers
          var blob = new Blob([response.data], { type: headers['content-type'] })

          if (window.navigator.msSaveOrOpenBlob) {  // IE hack; see http://msdn.microsoft.com/en-us/library/ie/hh779016.aspx
            window.navigator.msSaveBlob(blob, this.selectedSoc.countryCode + '.csv')
          } else {
            var link = document.createElement('a')
            link.href = window.URL.createObjectURL(blob)
            link.download = this.selectedSoc.countryCode + '.csv'
            link.click()
          }
        })
        .catch((error) => {
          this.$noty.error(this.$t('error_alert_text'))
          console.error(error)
        })

      this.$fireGTEvent(this.$gtagEvents.DownloadBulkTemplate)
    },
    downloadTemplate (lang) {
      axios.get(`/api/template/${this.selectedSoc.countryCode}/${lang}`)
        .then((response) => {
          var headers = response.headers
          var blob = new Blob([response.data], { type: headers['content-type'] })

          if (window.navigator.msSaveOrOpenBlob) {  // IE hack; see http://msdn.microsoft.com/en-us/library/ie/hh779016.aspx
            window.navigator.msSaveBlob(blob, this.selectedSoc.countryCode + '.csv')
          } else {
            var link = document.createElement('a')
            link.href = window.URL.createObjectURL(blob)
            link.download = this.selectedSoc.countryCode + '.csv'
            link.click()
          }
        })
        .catch((error) => {
          this.$noty.error(this.$t('error_alert_text'))
          console.error(error)
        })

      this.$fireGTEvent(this.$gtagEvents.ExportLanguageData(lang))
    },
    clearFiles () {
      this.$refs.fileinput.reset()
    },
    getLocalStorage () {
      let soc = localStorage.getItem('soc')
      let lang = localStorage.getItem('lang')

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
      const languages = [{ value: null, text: this.$t('content.whatnow.select_language') }]

      for (const key in this.languages) {
        if (this.languages.hasOwnProperty(key)) {
          languages.push({
            value: key,
            text: `${this.languages[key].name}  (${this.languages[key].native} - ${key})`
          })
        }
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
