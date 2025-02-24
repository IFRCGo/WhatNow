<template>
  <b-container fluid class="h-100 whatnow-message-editor-container">
    <page-banner >
      <b-col class="whatnow-message-editor-header">
        <h1>{{ $t('content.message_editor.title') }} {{ currentRegionName && `- ${currentRegionName}` }}</h1>
      </b-col>
    </page-banner>
    <b-row class="pl-4 pr-4 pb-3 pt-3 selects-container d-flex align-items-center justify-content-start m-auto"
      v-show="selectedSoc">
      <b-col class="d-flex align-items-center">
        <selectSociety v-model="selectedSoc" :societyList="filteredSocieties" :countryCode="countryCode">
        </selectSociety>
        <selectRegion v-model="selectedRegion" :socCode="selectedSoc.countryCode" :translationCode="selectedLanguage">
        </selectRegion>
      </b-col>
    </b-row>
    <b-row class="m-3">
      <b-col>
        <b-nav tabs class="whatnow-message-editor-tabs">
          <b-nav-item v-for="lang in currentLanguages" :key="lang" class="wn-item" @click="selectedLanguage = lang"
            :active="selectedLanguage === lang">
            <div class="nav-link-wrapper text-center h-100" v-if="lang">
              {{ lang | uppercase }} -
              <small v-if="languages[lang]">{{ truncate(languages[lang].name, 8) }}</small>
            </div>
          </b-nav-item>
          <b-nav-item class="add-lang-btn ml-2" v-b-tooltip.hover
            :title="selectingLanguage ? null : $t('content.whatnow.add_language')" @click="selectingLanguage = true"
            v-if="can(user, permissions.CONTENT_CREATE)">
            <fa :icon="['fas', 'plus']" />
          </b-nav-item>
        </b-nav>
      </b-col>
    </b-row>
    <b-row class="whatnow-message-editor-ns-title" v-if="selectedSoc">
      <b-col>
        <div class="d-flex justify-content-between align-items-center">
          <h4>
            {{ selectedSoc.name }}
          </h4>

          <div class="d-flex align-items-center">
            <b-button variant="outline-primary" size="sm" class="mr-2"
              v-if="canEditAttribution && !languageToAdd && !editing" @click="editing = true"
              :disabled="!canEditAttribution" :key="'edit'">
              <font-awesome-icon :icon="['fas', 'pen']" />
              {{ $t('common.edit') }}
            </b-button>
            <b-button variant="primary" size="sm" class="mr-2" v-if="canEditAttribution && (languageToAdd || editing)"
              :disabled="!canEditAttribution" @click="publishAttribution(true)" :key="'save'">
              <font-awesome-icon :icon="['fas', 'check']" />
              {{ $t('common.save') }}
            </b-button>
            <b-button variant="outline-primary" size="sm" class="mr-2" v-if="canEditAttribution && editing"
              :disabled="!canEditAttribution" :key="'add'" @click="addContributor">
              {{ $t('common.add') + ' ' + $t('content.message_editor.contributor_single') }}
            </b-button>
            <b-button variant="outline-primary" size="sm" v-if="canEditAttribution && (languageToAdd || editing)"
              @click="discard" :disabled="!canEditAttribution" :key="'cancel'">
              <font-awesome-icon :icon="['fas', 'xmark']" />
              {{ $t('common.cancel') }}
            </b-button>
          </div>
        </div>
      </b-col>
    </b-row>
    <!-- FORM ORG -->
    <b-row align-v="stretch"
      class="pl-4 pr-4 pb-3 pt-3 whatnow-message-editor-form-card d-flex align-items-center justify-content-start mb-3 mr-3 ml-auto mr-auto">

      <b-col lg="5">
        <div class="d-flex justify-content-start align-items-start">
          <!-- NS IMG -->
          <div slot="button-content" class="text-dark py-0 mr-3">
            <div class="upload-img-button">
              <b-button :variant="'link'" @click="openUploadModal(logoType.NS)" :disabled="isFormDisabled"
                class="p-0 d-flex flex-column align-items-center justify-content-center">
                <img  :src="attributionToEdit.imageUrl" v-if="attributionToEdit.imageUrl" />
                <div class="upload-img-button-controls">
                  <fa :icon="['fas', 'plus']" />
                  <span>Add a logo</span>
                </div>
              </b-button>
            </div>
          </div>
          <b-row>
            <b-col lg="12">
              <b-form-group :label="$t('content.message_editor.society_name_label')" label-for="socName">
                <b-form-input type="text" id="socName" name="socName" maxlength="255"
                  v-model="attributionEditTranslation.name"
                  :state="updateErrors.errors[`translations.${updateErrors.indexError}.name`] ? false : null"
                  v-if="attributionEditTranslation" :disabled="isFormDisabled" />
                <b-form-invalid-feedback id="socNameFeedback">
                  <p>
                    {{ $t('common.not_empty') }}
                  </p>
                </b-form-invalid-feedback>
              </b-form-group>
            </b-col>
            <b-col lg="12">
              <b-form-group :label="$t('content.message_editor.attribution_url_label')" label-for="url">
                <b-form-input type="url" id="url" name="url" maxlength="255" v-model="attributionToEdit.url"
                  :state="updateErrors.errors.url ? false : null" placeholder="https://" :disabled="isFormDisabled" />
                <b-form-invalid-feedback id="urlFeedback">
                  <!-- This will only be shown if the preceeding input has an invalid state -->
                  <p v-for="error in updateErrors.errors.url">
                    {{ error }}
                  </p>
                </b-form-invalid-feedback>
              </b-form-group>
            </b-col>
          </b-row>
        </div>

      </b-col>

      <b-col lg="7">
        <b-form-group :label="$t('content.message_editor.attribution_message_label')" label-for="message">
          <textarea
            :class="`form-control ${updateErrors.errors[`translations.${updateErrors.indexError}.attributionMessage`] ? 'is-invalid' : ''}`"
            id="message" name="message" maxlength="2048" rows="5"
            v-model="attributionEditTranslation.attributionMessage" v-if="attributionEditTranslation"
            :disabled="isFormDisabled"></textarea>
        </b-form-group>
      </b-col>
    </b-row>

    <b-row v-if="contributors.length > 0" align-v="stretch"
      class="pl-4 pr-4 pb-3 pt-3 whatnow-message-editor-form-card d-flex align-items-center justify-content-start m-auto mb-3 mr-3 ml-auto mr-auto">
      <b-col lg="12">
        <div v-for="(contributor, index) in attributionEditTranslation.contributors" :key="index">
          <div class="d-flex justify-content-start align-items-start">
            <!-- CONTRIBUTOR IMG -->
            <div slot="button-content" class="text-dark py-0 mr-3">
              <div class="upload-img-button">
                <b-button :variant="'link'" @click="openUploadModal(logoType.CONTRIBUTOR, index)" :disabled="isFormDisabled"
                  class="p-0 d-flex flex-column align-items-center justify-content-center">
                  <img  :src="contributor.logo" v-if="contributor.logo" />
                  <div class="upload-img-button-controls">
                    <fa :icon="['fas', 'plus']" />
                    <span>Add a logo</span>
                  </div>
                </b-button>
              </div>
            </div>
            <b-form-group :label="$t('content.message_editor.contributor_name_label')" class="w-50"
              :label-for="'contributorName' + index">
              <b-form-input type="text" :id="'contributorName' + index" :name="'contributorName' + index"
                v-model="contributor.name" maxlength="100"
                :state="updateErrors.errors[`contributors.${index}.name`] ? false : null" />
              <b-form-invalid-feedback v-if="updateErrors.errors[`contributors.${index}.name`]">
                {{ $t('common.not_empty') }}
              </b-form-invalid-feedback>
            </b-form-group>
            <b-button variant="link" class="align-self-center ml-1 contributor-delete-btn" size="sm" v-if="canEditAttribution && (languageToAdd || editing)"
              @click="deleteContributor(index)" :disabled="!canEditAttribution" :key="'cancel'">
              <font-awesome-icon :icon="['fas', 'trash']" />
            </b-button>
          </div>
        </div>
      </b-col>
    </b-row>

    <!-- END FORM ORG -->
    <b-row>
      <b-col>
        <whatnow-list :selectedSoc="selectedSoc" :selectedRegion="selectedRegion" :selectedLanguage="selectedLanguage"
          v-if="selectedLanguage && selectedSoc"></whatnow-list>
      </b-col>
    </b-row>

    <!-- PUBLISH BUTTON -->
    <div class="publish-bottom-container d-flex justify-content-end" v-if="selectedSoc">
      <b-button size="md" variant="outline-primary" v-if="can(user, permissions.CONTENT_PUBLISH)"
        v-b-modal.publish-modal :disabled="toPublish.length === 0 || !attributionSet" class="align-self-center">
        <span v-if="toPublish.length === 0">{{ $t('content.whatnow.no_publish') }}</span>
        <span v-else-if="!attributionSet">{{ $t('content.whatnow.set_attribution') }}</span>
        <span v-else-if="toPublish.length > 0 && !publishing">{{ $t('content.whatnow.publish') }}</span>
        <span v-else-if="publishing">{{ $t('content.whatnow.publishing') }}
          <fa class="ml-2" spin :icon="['fas', 'spinner']" />
        </span>
      </b-button>
    </div>

    <!-- Publish Modal -->
    <b-modal id="publish-modal" size="lg" centered :ok-title="$t('content.whatnow.publish')" ok-variant="primary" cancel-variant="outline-primary" hide-header @ok="publish"
      v-if="attribution !== null && selectedLanguage && selectedSoc">
      <div class="px-3">
        <h3>{{ $t('content.whatnow.content_to_publish') }}</h3>
        <p v-if="attributionTranslation">
          {{ attributionTranslation.name }} - {{ selectedLanguage | uppercase }}
        </p>
        <b-card class="whatnow-org-publish-modal">
          <div class="whatnow-publish-header">
            <h4>{{ attributionTranslation.publish_summary_title }}</h4>
          </div>

          <div class="d-flex justify-content-start align-items-start">
            <div class="mr-3">
              <img v-if="attributionToEdit.imageUrl" :src="attributionToEdit.imageUrl" class="whatnow-org-publish-modal-ns-logo" alt="NS Profile Photo">
              <avatar v-else class="rounded-circle profile-photo mr-1 rtl-ml-1" :size="50" :username="'NS'" :initials="'NS'" ></avatar>
            </div>
            <div class="whatnow-org-publish-modal-content">
              <div class="d-flex justify-content-start align-items-center whatnow-org-publish-modal-content-item">
                <h5>{{ $t('content.whatnow.publish_summary_ns') }}</h5>
                <p>{{ attributionTranslation.name }}</p>
              </div>

              <div class="d-flex justify-content-start align-items-center whatnow-org-publish-modal-content-item">
                <h5>{{ $t('content.whatnow.publish_summary_url') }}</h5>
                <p>{{ attributionTranslation.url }}</p>
              </div>

              <div class="d-flex justify-content-start align-items-center whatnow-org-publish-modal-content-item">
                <h5>{{ $t('content.whatnow.publish_summary_message') }}</h5>
                <p>{{ attributionTranslation.attributionMessage }}</p>
              </div>
            </div>
          </div>
        </b-card>

        <div class="whatnow-publish-content-wrapper">
          <div v-for="content in toPublish" :key="content.eventType" class="mb-3"
            v-if="content.translations[selectedLanguage]">
            <h5>{{ content.eventType }} {{ content.regionName ? `- ${content.regionName}` : "" }}</h5>

    <b-card class="bg-grey mb-1 hazard-instruction-card" v-for="key in ['title', 'description', 'webUrl']" :key="key" v-if="attributionExists(key)">
    <b-row>
      <b-col md="3">
        {{ $t(`common.${key}`) }}
      </b-col>
      <b-col md="9">
        {{ truncate(content.translations[selectedLanguage][key], 60) }}
      </b-col>
    </b-row>
</b-card>

<b-card :class="`bg-grey mb-1 hazard-instruction-card hazard-instruction-card-${stageName}`"
v-for="(instruction, stageName) in content.translations[selectedLanguage].stages"
v-if="instruction" :key="stageName">
<b-row>
  <b-col md="3">
    {{ $t(`content.edit_whatnow.${stageName}`) }}
  </b-col>
  <b-col md="9">
    {{ instruction.length }} {{ $t('content.whatnow.steps') }}
  </b-col>
</b-row>
</b-card>
</div>
</div>
</b-card>
</div>
</b-modal>
</b-container>
</template>

<script>
import swal from 'sweetalert2'
import SelectSociety from '~/pages/content/simpleSocietyPicker'
import SelectRegion from '~/pages/content/regionPicker'
import WhatnowList from '~/pages/content/whatnowList'
import PageBanner from '~/components/PageBanner'
import { mapGetters } from 'vuex'
import * as permissionsList from '../../store/permissions'
import Spooky from '~/components/global/Spooky'
import axios from 'axios'
import { languages } from 'countries-list'

export default {
  components: {
    SelectSociety,
    SelectRegion,
    Spooky,
    WhatnowList,
    PageBanner
  },
  props: ['countryCode', 'regionSlug'],
  data () {
    return {
      selectedSoc: null,
      selectedRegion: null,
      permissions: permissionsList,
      selectedLanguage: null,
      loadingContent: false,
      currentTranslations: null,
      publishing: false,
      languageToAdd: null,
      selectingLanguage: false,
      languages,
      showEditAttribution: false,
      attributionPublishing: false,
      addingNewLanguage: false,
      previousLanguage: null,
      attributionToEdit: {
        countryCode: '',
        url: '',
        name: '',
        translations: []
      },
      attributionEditTranslation: null,
      updateErrors: {
        errors: {}
      }
    }
  },
  watch: {
    regions () {
      if (this.regions.length && this.regionSlug) {
        this.selectedRegion = this.regions.find(region => region.title === this.regionSlug) || null
      }
    },
    selectedSoc () {
      if (this.countryCode !== this.selectedSoc.countryCode) {
        this.$router.push({ name: 'content.whatnow', params: { countryCode: this.selectedSoc.countryCode, regionSlug: this.selectedRegion?.title }})
      }
    },
    selectedRegion () {
      if (this.regionSlug !== this.selectedRegion?.title) {
        this.$router.push({ name: 'content.whatnow', params: { countryCode: this.selectedSoc.countryCode, regionSlug: this.selectedRegion?.title }})
      }
    },
    selectedLanguage: {
      handler (val, oldVal) {
        if (val !== oldVal) {
          this.contentChange()
          this.setLocalStorage()
          this.setAttributionToEdit()
        }
      },
      deep: true
    },
    currentContent: {
      async handler (val, oldVal) {
        if (this.selectedSoc) {
          await this.$store.dispatch('content/fetchOrganisationSingle', this.selectedSoc.countryCode)
        }
        this.contentChange()
      }
    },
    currentLanguages () {
      if (this.currentLanguages && this.currentLanguages.find && !this.currentLanguages.find(lang => this.selectedLanguage === lang)) {
        if (this.currentLanguages.length > 0) {
          this.selectedLanguage = this.currentLanguages[0]
        }
      }
    },
    lastError () {
      if (this.lastError.response) {
        this.updateErrors = this.lastError.response.data
      }
    },
    attribution () {
      this.setAttributionToEdit()
    }
  },
  mounted () {
    this.getLocalStorage()
    this.$store.dispatch('content/fetchOrganisations').then(() => {
      this.selectedSoc = this.filteredSocieties.find((soc) => soc.countryCode === this.countryCode)
    })
  },
  methods: {
    async publish () {
      if (this.toPublish.length > 0) {
        this.loadingContent = true
        this.publishing = true
        this.$fireGTEvent(this.$gtagEvents.PublishContent)

        try {
          await axios.post(`/api/instructions/${this.selectedSoc.countryCode}/publish`, { translations: this.getPublishPayload() })
          if (!this.attributionEditTranslation.published) {
            this.attributionEditTranslation.published = true
            await this.publishAttribution()
          }
          this.$noty.success(`${this.toPublish.length} ${this.$t('content.whatnow.published')}`)
        } catch (e) {
          this.publishing = false
          this.loadingContent = false
          this.$noty.error(this.$t('error_alert_text'))
          console.error(e)
        }
        try {
          await this.$store.dispatch('content/fetchContent', this.selectedSoc.countryCode)
        } catch (e) {
          this.$noty.error(this.$t('error_alert_text'))
        }

        this.publishing = false
        this.loadingContent = false
      }
    },
    setLocalStorage () {
      localStorage.setItem('lang', this.selectedLanguage)
    },
    attributionExists (key) {
      const attributionTranslation = this.attribution.translations.find(translation => translation.languageCode === this.selectedLanguage)
      return attributionTranslation && attributionTranslation[key]
    },
    getLocalStorage () {
      let lang = localStorage.getItem('lang')

      if (!lang) {
        lang = 'en'
      }

      this.selectedLanguage = lang
    },
    contentChange () {
      if (this.selectedSoc) {
        this.currentTranslations = this.currentContent.map((content) => {
          const flattened = JSON.parse(JSON.stringify(content))
          if (content.translations) {
            flattened.currentTranslation = content.translations[this.selectedLanguage]
          }
          return flattened
        })
      }
    },
    getPublishPayload () {
      if (this.currentTranslations) {
        return this.currentTranslations.reduce((filtered, translation) => {
          if (translation.currentTranslation && translation.currentTranslation.published === false) {
            filtered.push({
              id: translation.currentTranslation.id,
              eventType: translation.eventType,
              lang: translation.currentTranslation.lang
            })
          }
          return filtered
        }, [])
      }
      return []
    },
    addNewLanguage () {
      // Does it exist already?
      let found = false
      for (const key in this.currentLanguages) {
        if (this.currentLanguages.hasOwnProperty(key) && this.currentLanguages[key] === this.languageToAdd) {
          found = true
          swal({
            title: this.$t('common.duplicate_lang_error'),
            text: this.$t('common.duplicate_lang_description'),
            type: 'warning',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Close'
          })
        }
      }
      if (!found && this.languageToAdd !== null) {
        // Add new attribution for this language
        this.previousLanguage = this.selectedLanguage
        this.selectedLanguage = this.languageToAdd
        this.addingNewLanguage = true
        this.showEditAttribution = true
        this.selectingLanguage = false
      }
    },
    async publishAttribution (fireEvent = false) {
      if (fireEvent) {
        this.$fireGTEvent(this.$gtagEvents.EditAttribution)
      }

      this.attributionPublishing = true
      this.updateErrors = { errors: {}}
      try {
        await this.$store.dispatch('content/updateAttribution', { countryCode: this.selectedSoc.countryCode, data: this.attributionToEdit })
        await this.$store.dispatch('content/fetchOrganisationSingle', this.selectedSoc.countryCode)
        this.showEditAttribution = false
        this.attributionPublishing = false
        this.addingNewLanguage = false
      } catch (e) {
        // Find index of translation we've just edited so we can find it in the response from the server
        this.updateErrors.indexError = this.attributionToEdit.translations.findIndex(translation => translation.languageCode === this.selectedLanguage)
        this.attributionPublishing = false
      }
    },
    setAttributionToEdit () {
      if (this.attribution) {
        this.attributionToEdit = JSON.parse(JSON.stringify(this.attribution))
        const attributionTranslation = this.attributionToEdit.translations.find(translation => translation.languageCode === this.selectedLanguage)
        if (!attributionTranslation) {
          const newTranslation = {
            attributionMessage: '',
            languageCode: this.selectedLanguage,
            name: '',
            published: false
          }
          this.attributionToEdit.translations.push(newTranslation)
          this.attributionEditTranslation = newTranslation
        } else {
          this.attributionEditTranslation = attributionTranslation
        }
      }
    },
    showEditAttributionModal () {
      this.showEditAttribution = true
      this.addingNewLanguage = false
    },
    cancelEditAttribution () {
      if (this.addingNewLanguage) {
        this.selectedLanguage = this.previousLanguage
        this.addingNewLanguage = false
      }
      this.showEditAttribution = false
    }
  },
  metaInfo () {
    return { title: this.$t('content.whatnow.whatnow') }
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
    toPublish () {
      if (this.currentTranslations) {
        const toPublish = this.currentTranslations.filter(translation => translation.currentTranslation && translation.currentTranslation.published === false)
        if (toPublish.length) {
          return toPublish
        }
      }
      return []
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
    attribution () {
      if (this.selectedSoc) {
        return this.societies.find(soc => soc.countryCode === this.selectedSoc.countryCode)
      }
      return null
    },
    attributionTranslation () {
      if (this.attribution) {
        return this.attribution.translations.find(translation => translation.languageCode === this.selectedLanguage)
      }
      return null
    },
    canEditAttribution () {
      if ((this.attributionTranslation && !this.attributionTranslation.published) || !this.attributionTranslation) {
        return this.can(this.user, permissionsList.CONTENT_CREATE) && !this.selectedRegion
      }
      return this.can(this.user, permissionsList.CONTENT_PUBLISH) && !this.selectedRegion
    },
    attributionSet () {
      return this.attributionTranslation && this.attributionTranslation.name && this.attributionTranslation.attributionMessage
    },
    currentRegionName () {
      if (!this.selectedRegion) {
        return ""
      }

      if (this.selectedLanguage && this.selectedRegion.translations[this.selectedLanguage]?.title) {
        return this.selectedRegion.translations[this.selectedLanguage].title
      }

      return this.selectedRegion.translations[0]?.title || ""
    },
    ...mapGetters({
      user: 'auth/user',
      currentContent: 'content/currentContent',
      currentLanguages: 'content/currentLanguages',
      societies: 'content/organisations',
      lastError: 'generic/lastError',
      regions: 'content/regionsArray'
    })
  }
}
</script>
