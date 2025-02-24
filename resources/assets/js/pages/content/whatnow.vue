  <template>
    <b-container fluid>
      <page-banner>
          <b-col sm="auto" md="4">
            <h1>{{ $t('content.whatnow.whatnow') }} {{ currentRegionName && `- ${currentRegionName}` }}</h1>
          </b-col>
          <b-col sm="auto" md="8">
            <div class="d-flex justify-content-end u-gap-24 flex-wrap">
              <selectSociety v-model="selectedSoc" :societyList="filteredSocieties" :countryCode="countryCode"></selectSociety>
              <selectRegion v-model="selectedRegion" :socCode="selectedSoc.countryCode" :translationCode="selectedLanguage"></selectRegion>
            </div>
          </b-col>
      </page-banner>

<b-row :class="`pb-0 pl-4 pr-4 whatnow-language-picker text-light ${selectingLanguage ? 'pt-2 pb-2':'pb-0'}`" v-if="selectedSoc">
  <b-col>
  <b-input-group v-if="selectingLanguage">
  <b-form-select v-model="languageToAdd" :options="filteredLanguages" />
  <div class="input-group-append">
  <b-button class="h-100 btn-small-radius" variant="dark" @click="addNewLanguage"><fa :icon="['fas', 'plus']" /></b-button>
<b-button class="h-100 btn-small-radius" variant="outline-danger" @click="selectingLanguage = false"><fa :icon="['fas', 'times']" /></b-button>
</div>
</b-input-group>
<b-nav tabs v-else>
  <b-nav-item
    v-for="lang in currentLanguages"
  :key="lang"
  @click="selectedLanguage = lang"
  :active="selectedLanguage === lang">
  <div class="nav-link-wrapper text-center h-100">
    {{ lang | uppercase }} <br />
    <small v-if="languages[lang]">{{ truncate(languages[lang].name, 8) }}</small>
  </div>
</b-nav-item>
<b-nav-item v-b-tooltip.hover :title="selectingLanguage ? null : $t('content.whatnow.add_language')" @click="selectingLanguage = true" v-if="can(user, permissions.CONTENT_CREATE)">
  <fa class="mt-2" :icon="['fas', 'plus']"/>
  </b-nav-item>
</b-nav>
</b-col>
</b-row>

<b-row class="pl-4 pr-4 pb-3 pt-3 whatnow-publish-banner text-light" v-if="selectedSoc">
  <b-col>
    <p>{{ selectedSoc.label }}</p>
  </b-col>
  <b-col cols="auto">
    <b-button size="lg" variant="primary" class="float-right pl-5 pr-5" v-if="can(user, permissions.CONTENT_PUBLISH)"
              v-b-modal.publish-modal
    :disabled="toPublish.length === 0 || !attributionSet">
    <span v-if="toPublish.length === 0">{{ $t('content.whatnow.no_publish') }}</span>
    <span v-else-if="!attributionSet">{{ $t('content.whatnow.set_attribution') }}</span>
    <span v-else-if="toPublish.length > 0 && !publishing">{{ $t('content.whatnow.publish') }}</span>
    <span v-else-if="publishing">{{ $t('content.whatnow.publishing') }} <fa class="ml-2" spin :icon="['fas', 'spinner']"/></span>
  </b-button>
</b-col>
</b-row>
<b-row class="pl-4 pr-4 pb-4 pt-3 bg-white" v-if="selectedLanguage && selectedSoc">
  <b-col>
    {{ $t('content.whatnow.attribution') }}
    <b-button size="sm" variant="primary" class="float-right rtl-float-left" @click="showEditAttributionModal" v-if="canEditAttribution">{{ $t('common.edit') }}</b-button>
  <b-modal
    v-model="showEditAttribution" centered
  :title="addingNewLanguage ? $t('content.whatnow.add_language') : $t('content.whatnow.edit_attribtion')"
  v-if="attributionTranslation !== null && canEditAttribution"
  no-close-on-backdrop
  no-close-on-esc
  hide-header-close
  @cancel="cancelEditAttribution()">
  <p v-if="addingNewLanguage">
    {{ $t('content.whatnow.add_language_attribution') }}
  </p>
  <b-form-group :label="$t('content.whatnow.attribution_url')" label-for="url">
  <b-form-input
    type="url" id="url"
    name="url" maxlength="255"
    v-model="attributionToEdit.url"
  :state="updateErrors.errors.url ? false : null"
  placeholder="https://" />
  <b-form-invalid-feedback id="urlFeedback">
    <!-- This will only be shown if the preceeding input has an invalid state -->
    <p v-for="error in updateErrors.errors.url">
      {{ error }}
    </p>
  </b-form-invalid-feedback>
</b-form-group>
<b-form-group :label="$t('content.whatnow.society_name')" label-for="socName">
  <b-form-input
  type="text"
id="socName"
name="socName"
maxlength="255"
v-model="attributionEditTranslation.name"
:state="updateErrors.errors[`translations.${updateErrors.indexError}.name`] ? false : null"
v-if="attributionEditTranslation"
  />
  <b-form-invalid-feedback id="socNameFeedback">
  <p>
  {{ $t('common.not_empty')}}
</p>
</b-form-invalid-feedback>
</b-form-group>
<b-form-group :label="$t('content.whatnow.attribution_message')" label-for="message">
  <textarea
  :class="`form-control ${updateErrors.errors[`translations.${updateErrors.indexError}.attributionMessage`] ? 'is-invalid': ''}`"
id="message"
name="message"
maxlength="2048"
rows="5"
v-model="attributionEditTranslation.attributionMessage"
v-if="attributionEditTranslation"></textarea>
  </b-form-group>
<div slot="modal-footer" class="w-100">
              <span v-if="attributionEditTranslation && attributionEditTranslation.published">
                {{ $t('content.whatnow.attribution_publish') }}
              </span>
  <b-btn size="sm" class="float-right ml-1" variant="secondary" @click="cancelEditAttribution">
  {{ $t('cancel') }}
</b-btn>
<b-btn size="sm" class="float-right" variant="primary" @click="publishAttribution(true)">
  <span v-if="attributionPublishing">
  <fa spin :icon="['fas', 'spinner']"/>
  </span>
<span v-if="attributionEditTranslation && attributionEditTranslation.published">{{ $t('content.whatnow.publish') }}</span>
<span v-else>{{ $t('common.save_changes') }}</span>

</b-btn>
</div>
</b-modal>
<hr />
<b-row>
  <b-col>
    <b>{{ $t('content.whatnow.attribution_url') }}</b>
  </b-col>
  <b-col>
    <b>{{ $t('content.whatnow.society_name') }}</b>
  </b-col>
  <b-col>
    <b>{{ $t('content.whatnow.attribution_message') }}</b>
  </b-col>
</b-row>
<transition name="fade">
  <b-row class="whatnow-row mt-2 border border-secondary" v-if="attribution !== null && !loadingContent">
    <b-col class="border border-top-0 border-bottom-0 border-left-0 pt-5 pb-5">
                <span v-if="attribution.url">
                  <a :href="attribution.url" rel="noreferrer" target="_blank">{{ truncate(attribution.url, 20) }}</a>
    </span>
    <span class="border border-warning p-1 rounded bg-warning" v-else>{{ $t('content.whatnow.no_translation')}}</span>
  </b-col>
  <b-col class="border border-top-0 border-bottom-0 border-left-0 pt-5 pb-5">
                <span v-if="attributionTranslation && attributionTranslation.name">
                  {{ attribution.name }} <br />
                  <small><i>{{ truncate(attributionTranslation.name, 90) }}</i></small>
                </span>
    <span class="border border-warning p-1 rounded bg-warning" v-else>{{ $t('content.whatnow.no_translation')}}</span>
  </b-col>
  <b-col class="border border-top-0 border-bottom-0 border-left-0 pt-5 pb-5">
                <span v-if="attributionTranslation && attributionTranslation.attributionMessage">
                  {{ truncate(attributionTranslation.attributionMessage, 90) }}
                </span>
    <span class="border border-warning p-1 rounded bg-warning" v-else>{{ $t('content.whatnow.no_translation')}}</span>
  </b-col>
</b-row>
</transition>
<transition name="fade">
  <b-row class="whatnow-row mt-2 border border-secondary" v-if="loadingContent">
    <b-col class="border border-top-0 border-bottom-0 border-left-0 pt-5 pb-5">
      <spooky width="75%" height="20px"></spooky>
    </b-col>
    <b-col class="border border-top-0 border-bottom-0 border-left-0 pt-5 pb-5">
      <spooky width="75%" height="20px"></spooky>
    </b-col>
    <b-col class="border border-top-0 border-bottom-0 border-left-0 pt-5 pb-5">
      <spooky width="75%" height="20px"></spooky>
      <spooky width="100%" height="10px" class="mt-2"></spooky>
      <spooky width="100%" height="10px" class="mt-1"></spooky>
    </b-col>
  </b-row>
</transition>
</b-col>
</b-row>
<b-row class="pl-4 pr-4 pb-4 pt-3 bg-white" v-else-if="!selectedLanguage && selectedSoc">
  <h3>{{ $t('content.whatnow.no_languages') }}</h3>
</b-row>
<b-row class="pl-4 pr-4 pb-4 pt-3 bg-white" v-else-if="!selectedSoc">
  <h3>{{ $t('content.whatnow.no_soc') }}</h3>
</b-row>
<whatnow-list :selectedSoc="selectedSoc" :selectedRegion="selectedRegion" :selectedLanguage="selectedLanguage" v-if="selectedLanguage && selectedSoc"></whatnow-list>
  <!-- Publish Modal -->
  <b-modal
    id="publish-modal" size="lg"
    centered
        :ok-title="$t('content.whatnow.publish')"
ok-variant="dark"
cancel-variant="outline-danger"
hide-header
@ok="publish"
v-if="attribution !== null && selectedLanguage && selectedSoc">
  <div class="px-3">
  <h3>{{ $t('content.whatnow.content_to_publish')}}</h3>
<p v-if="attributionTranslation">
  {{ attributionTranslation.name }} - {{ selectedLanguage | uppercase }}
</p>
<b-card class="border whatnow-publish-modal">
  <div v-if="attributionTranslation && !attributionTranslation.published">
    <b-row>
      <b-col>
        <h4>Attribution</h4>
        <hr />
      </b-col>
    </b-row>
    <b-row>
      <b-col md="3">
        <b>{{ $t('content.whatnow.attribution_url') }}</b>
      </b-col>
      <b-col md="9">
        {{ attribution.url }}
      </b-col>
    </b-row>
    <b-row>
      <b-col md="3">
        <b>{{ $t('content.whatnow.society_name') }}</b>
      </b-col>
      <b-col md="9" v-if="attributionTranslation">
        {{ attributionTranslation.name }}
      </b-col>
    </b-row>
    <b-row>
      <b-col md="3">
        <b>{{ $t('content.whatnow.attribution_message') }}</b>
      </b-col>
      <b-col md="9" v-if="attributionTranslation">
        {{ attributionTranslation.attributionMessage }}
      </b-col>
    </b-row>
    <hr />
  </div>
  <h4>{{ $t('content.whatnow.whatnow_content') }}</h4>
  <hr />
  <div class="whatnow-publish-content-wrapper">
    <div v-for="content in toPublish" :key="content.eventType" class="mb-3" v-if="content.translations[selectedLanguage]">
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
