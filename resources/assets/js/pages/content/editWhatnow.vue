  <template>
    <b-container fluid class="edit-whatnow bg-white">
      <transition name="fade">
        <div v-if="content && !loadingContent">
          <b-row class="pb-0 pl-4 pr-4 pt-4 pb-0" v-if="isCreateHazardMode">
            <b-col>
              <div>
                <h4 class="text-uppercase text-secondary">
                  <b>{{ $t('hazard_type.create.title') }}</b>
                </h4>
                <b-card class="bg-grey mb-2 pl-3 pr-3 pt-2 pb-2">
                  <div class="u-flex mb-4 pt-1">
                    <label for="name"
                      class="styled-label mb-0 mr-3 rtl-ml-3 mt-2">{{ $t('hazard_type.create.hazard_name') }}</label>
                    <div class="u-flex-fluid">
                      <b-form-input autocomplete="off"
                        :class="`u-flex-fluid ${newHazardValidations.validated && (newHazardValidations.name === false) ? 'is-invalid' : ''}`"
                        :placeholder="$t('hazard_type.create.hazard_name_placeholder')" type="text" id="name"
                        name="name" v-model.trim="newHazard.name"
                        :state="newHazardValidations.validated ? newHazardValidations.title : null" />
                      <b-form-invalid-feedback id="nameFeedback">
                        <!-- This will only be shown if the preceeding input has an invalid state -->
                        {{ $t('common.validations.empty') }}
                      </b-form-invalid-feedback>
                    </div>
                  </div>
                  <hr>
                  <div
                    :class="`c-file-upload ${newHazardValidations.validated && (newHazardValidations.icon === false) ? 'is-invalid' : ''}`">
                    <b-img :src="hazardIcon('create')" class="c-file-upload__image rounded-circle mr-1" width="60"
                      height="60" alt="" role="presentation"></b-img>
                    <b-form-file id="newHazardIcon" accept=".png" v-model="newHazard.icon"
                      :placeholder="$t('hazard_type.create.icon_requirements')"
                      :choose-label="$t('hazard_type.create.upload_icon')"></b-form-file>
                  </div>
                  <b-form-invalid-feedback id="newHazardIconFeedback">
                    <!-- This will only be shown if the preceeding input has an invalid state -->
                    {{ $t('common.validations.hazardIcon') }}
                  </b-form-invalid-feedback>
                </b-card>
              </div>
            </b-col>
          </b-row>


          <div :class="{ 'has-semi-opacity': isCreateHazardMode }">
            <b-row class="content-editor-header bg-white">
              <b-col class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h4 class="content-editor-title">
                      {{ this.isNew ? $t('content.edit_whatnow.content_add_hazard_title') : $t('content.edit_whatnow.content_edit_title') }}
                    </h4>
                  </div>
                </div>
                <page-breadcrumb :breadcrumbs="[
                    { name: 'content.whatnow', params: { countryCode: this.content.countryCode, regionSlug: this.regionSlug }, text: (content && content.attribution) ? content.attribution.name : $t('content.whatnow.create') },
                    { text: getLangName(langCode) }
                  ]">
                </page-breadcrumb>
              </b-col>
            </b-row>
            <b-row class="hazard-form bg-white">
              <b-col sm="auto" md="4" class="mb-4">
                <SelectHazardType v-if="isNew && !forceEventType" class="bg-white" v-model="selectedHazardType" v-on:hazardTypeChange="hazardTypeChange" :hazardTypeList="filteredHazardsList"></SelectHazardType>
                <h1 v-else-if="isNew && !forceEventType">{{ content.eventType }} {{ regionSlug && `- ${regionSlug}`}}</h1>
                <h1 v-else-if="forceEventType">{{ selectedHazardType.name }} {{ regionSlug && `- ${regionSlug}`}}</h1>

                <div v-if="isOtherHazardType">
                  <label for="other_hazard" class="mt-3 mb-1 font-weight-bold text-uppercase text-secondary">{{$t('content.edit_whatnow.other_hazard_type_label')}}</label>
                  <b-form-input id="other_hazard" autocomplete="off" v-model.trim="content.eventType" :placeholder="$t('content.edit_whatnow.enter_other_hazard_type')" size="lg"/>
                </div>

                <div v-if="cannot(user, permissions.HAZARD_TYPE_CREATE)">
                  <a class="u-link--black mt-2 u-d-block" href="mailto:gdpc@redcross.org?subject=Request for new hazard type">{{$t('hazard_type.create.requestLink')}}</a>
                </div>
              </b-col>
              <b-col class="col-12 hazard-form-input-container">
                <label for="title" class="hazard-form-label required">{{ $t('common.title') }}</label>
                <b-form-input class="form-control hazard-form-input" :disabled="isCreateHazardMode || !canEdit"
                  type="text" id="title" name="title" v-model.trim="content.translations[langCode].title"
                  :state="validations.showErrors ? validations.title : null" />
                <b-form-invalid-feedback id="titleFeedback">
                  {{ $t('common.validations.empty') }}
                </b-form-invalid-feedback>
              </b-col>
              <b-col class="col-12 hazard-form-input-container">
                <label for="description" class="hazard-form-label required">{{ $t('common.description') }}</label>
                <textarea id="description" name="description" :disabled="isCreateHazardMode || !canEdit"
                  :class="`form-control hazard-form-input ${validations.showErrors && (validations.description === false) ? 'is-invalid' : ''}`"
                  v-model="content.translations[langCode].description" :rows="3"></textarea>
                <b-form-invalid-feedback id="descriptionFeedback">
                  {{ $t('common.validations.empty') }}
                </b-form-invalid-feedback>
              </b-col>
              <b-col class="col-12 hazard-form-input-container">
                <label for="url" class="hazard-form-label">{{ $t('content.edit_whatnow.content_url') }}</label>
                <b-form-input
                  :class="`form-control hazard-form-input ${validations.showErrors && (validations.urlFeedback === false) ? 'is-invalid' : ''}`"
                  :disabled="isCreateHazardMode || !canEdit" type="url" id="url" name="url"
                  v-model.trim="content.translations[langCode].webUrl" placeholder="https://" />
                <b-form-invalid-feedback id="urlFeedback">
                  {{ $t('common.validations.empty') }}
                </b-form-invalid-feedback>
              </b-col>

              <b-col class="col-12 hazard-form-input-container" v-if="!isNew">
                <label for="url" class="hazard-form-label required">{{ $t('content.edit_whatnow.hazard_type') }}</label>
                <b-form-input
                  class="form-control hazard-form-input"
                  :disabled="true" type="text" id="eventType" name="eventType"
                  v-model.trim="content.eventType" placeholder=""/>
              </b-col>
            </b-row>
            <b-row class="bg-white">
              <b-col>

                <div class="urgency-card mb-5" v-for="(urgency, i) in urgencyLevels" :key="'urgency-' + i">
                  <div class="urgency-card-header">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <h4 class="urgency-title">
                          {{ urgency.text }}
                        </h4>
                        <p class="urgency-description">
                          {{ urgency.description }}
                        </p>
                      </div>
                      <b-button class="btn-collapse" variant="light" :key="urgency.value + 'collapse'"
                        @click="toggleUrgencyCollapse(urgency.value)">
                        <div v-if="urgencyCollapses[urgency.value]" :key="1">
                          <i class="fas fa-chevron-up"></i>
                        </div>
                        <div v-else :key="2">
                          <i class="fas fa-chevron-down"></i>
                        </div>

                      </b-button>
                    </div>

                  </div>
                  <b-collapse :visible="urgencyCollapses[urgency.value]" :id="'urgency-collapse-' + i">
                    <div v-for="name in instructionNames" :key="name">
                      <whatnow-instructions v-if="urgency.stages.includes(name)" :disabled="isCreateHazardMode"
                        :instructions="content.translations[langCode].stages[name] || []" :instructionName="name"
                        :instructionId="whatnowId" :langCode="langCode" class="mb-4"
                        v-on:instructionUpdate="instructionUpdate" />
                    </div>
                  </b-collapse>

                </div>

              </b-col>
            </b-row>
            <!-- save controls -->
            <div v-if="canEdit" class="save-controls-container bg-white d-flex justify-content-end align-items-center mt-4">
              <b-button size="sm" variant="outline-primary" class="mr-3" :disabled="savingContent"
                :to="{ name: 'content.whatnow', params: { countryCode: this.content.countryCode } }">
                {{ $t('common.cancel') }}
              </b-button>

              <b-button size="sm"
                :variant="isAutoSavingContent ? 'light' : 'outline-primary'" v-if="can(user, permissions.CONTENT_EDIT)"
                @click="saveContent" :disabled="savingContent">
                {{ $t(isAutoSavingContent ? 'common.auto_saving' : 'common.submit_changes') }}
                <fa :icon="['fas', 'spinner']" spin v-show="savingContent" />
              </b-button>
            </div>
          </div>
        </div>
      </transition>
      <!-- Skeleton loading -->
      <transition name="fade">
        <div v-if="loadingContent">
          <page-banner>
            <b-col>
              <spooky width="40%" height="48px"></spooky>
            </b-col>
          </page-banner>
          <b-row class="pb-0 pl-4 pr-4 pt-4 pb-4 bg-white">
            <b-col>
              <spooky width="10%" height="33px" class="mb-3"></spooky>
              <spooky width="50%" height="24px" class="mb-3"></spooky>
              <spooky width="100%" height="38px"></spooky>
            </b-col>
          </b-row>
          <b-row class="pb-0 pl-4 pr-4 pt-4 pb-4 bg-white">
            <b-col>
              <spooky width="20%" height="33px" class="mb-3"></spooky>
              <spooky width="50%" height="24px" class="mb-3"></spooky>
              <spooky width="100%" height="86px"></spooky>
            </b-col>
          </b-row>
          <b-row class="pb-0 pl-4 pr-4 pt-4 pb-4 bg-white">
            <b-col>
              <spooky width="10%" height="33px" class="mb-3"></spooky>
              <spooky width="50%" height="24px" class="mb-3"></spooky>
              <spooky width="100%" height="38px"></spooky>
            </b-col>
          </b-row>
          <b-row class="mb-4 pb-0 pl-4 pr-4 pt-4 pb-4 bg-white">
            <b-col>
              <spooky width="20%" height="33px" class="mb-3"></spooky>
              <spooky width="30%" height="24px" class="mb-3"></spooky>

              <b-card>
                <ul class="spooky-list whatnow-instruction-list">
                  <li v-for="n in 5" class="mb-4" :key="n">
                    <spooky width="100%" height="38px"></spooky>
                  </li>
                </ul>
              </b-card>
            </b-col>
          </b-row>
        </div>
      </transition>
    </b-container>
  </template>

<script>
import swal from 'sweetalert2'
import { mapGetters } from 'vuex'
import * as permissionsList from '../../store/permissions'
import WhatnowInstructions from '~/pages/content/whatnowInstruction'
import Spooky from '~/components/global/Spooky'
import SelectHazardType from '~/pages/content/simpleHazardTypePicker'
import { ContentTranslation } from '../../store/models/contentTranslation'
import PageBreadcrumb from '../../components/PageBreadcrumb.vue'

const newContent = (langCode, countryCode) => {
  const translations = {}
  translations[langCode] = new ContentTranslation({ lang: langCode })
  return {
    eventType: null,
    countryCode: countryCode,
    translations
  }
}

export default {
  props: ['whatnowId', 'organisation', 'langCode', 'createLang', 'regionSlug', 'eventTypeToCreate'],
  components: {
    WhatnowInstructions,
    SelectHazardType,
    Spooky,
    PageBreadcrumb
  },
  data() {
    return {
      permissions: permissionsList,
      items: [
        { message: 'Stockpile essential foods' },
        { message: 'Plan daily basic food rations of good nutritional value' }
      ],
      content: null,
      resetAutoSaveTimeoutID: null,
      isCreateHazardMode: false,
      isFetchingInitialContent: true,
      loadingContent: false,
      forceEventType: false,
      newHazard: {
        name: '',
        icon: null
      },
      newHazardValidations: {
        validated: false,
        name: false,
        icon: false
      },
      selectedHazardType: null,
      savingContent: false,
      isAutoSavingContent: false,
      validations: {
        showErrors: false,
        validated: false,
        title: false,
        url: false,
        description: false
      },
      instructionNames: [
        'immediate',
        'warning',
        'anticipated',
        'assess_and_plan',
        'mitigate_risks',
        'prepare_to_respond',
        'recover'
      ],
      changedItems: [],
      urgencyLevels: [
        {
          value: 'early_warning',
          text: this.$t('content.edit_whatnow.early_warning'),
          stages: ['immediate', 'warning', 'anticipated'],
          description: this.$t('content.edit_whatnow.early_warning_description')
        },
        {
          value: 'disaster_risk_reduction',
          text: this.$t('content.edit_whatnow.disaster_risk_reduction'),
          stages: ['assess_and_plan', 'mitigate_risks', 'prepare_to_respond'],
          description: this.$t('content.edit_whatnow.disaster_risk_reduction_description')
        },
        {
          value: 'recovery',
          text: this.$t('content.edit_whatnow.recovery'),
          stages: ['recover'],
          description: this.$t('content.edit_whatnow.recovery_description')
        },
      ],
      urgencyCollapses: {
        early_warning: true,
        disaster_risk_reduction: true,
        recovery: true
      },
    }
  },
  mounted() {
    if (this.createLang) {
      swal({
        type: 'info',
        title: this.$t('content.whatnow.add_language'),
        text: this.$t('content.whatnow.add_language_message')
      })
    }
    this.fetchContent()
  },
  watch: {
    filteredHazardsList() {
      if (this.eventTypeToCreate) {
        this.selectedHazardType = this.filteredHazardsList.find(hazard => hazard.name === this.eventTypeToCreate) || null
        if (this.selectedHazardType) {
          this.forceEventType = true
        }
      }
    },
    content: {
      handler(val, oldVal) {
        if (this.validations.validated) {
          this.isValid()
        }

        if (!this.isFetchingInitialContent) {
          this.triggerAutoSave()
        } else {
          this.isFetchingInitialContent = false
        }
      },
      deep: true
    },
    selectedHazardType: {
      handler(val, oldVal) {
        if (val !== oldVal) {
          this.hazardTypeChange()
        }
      },
      deep: true
    },
    newHazard: {
      handler(val, oldVal) {
        if (this.newHazardValidations.validated) {
          this.isNewHazardValid()
        }
      },
      deep: true
    }
  },
  methods: {
    async fetchContent() {
      this.loadingContent = true
      this.fetchAllHazardTypes()
      if (this.isNew) {
        this.content = newContent(this.langCode, this.organisation)
      } else {
        try {
          await this.$store.dispatch('content/fetchInstructions', this.whatnowId)
          this.content = this.currentInstructions
          if (this.content === null) {
            // We don't have any correct ids
            this.$router.push({ name: 'content.whatnow', params: { countryCode: this.content.countryCode } })
          } else if (!this.content.translations[this.langCode]) {
            this.content.translations[this.langCode] = new ContentTranslation({ lang: this.langCode })
          }
        } catch (e) {
          this.$noty.error(this.$t('error_alert_text'))
        }
      }
      this.loadingContent = false
    },
    async fetchAllHazardTypes() {
      try {
        await this.$store.dispatch('content/fetchHazardTypes')
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
      }
    },
    instructionUpdate(val) {
      this.content.translations[this.langCode].stages[val.instructionName] = val.instructions
      if (this.changedItems.indexOf(val.instructionName) === -1) {
        this.changedItems.push(val.instructionName)
      }
      this.triggerAutoSave()
    },
    hazardTypeChange() {
      if (this.selectedHazardType === null) {
        this.isCreateHazardMode = false
        this.resetHazardCreation()
      } else if (this.selectedHazardType.code === 'create') {
        this.isCreateHazardMode = true
      } else {
        this.isCreateHazardMode = false
      }
    },
    isValid() {
      this.validations.validated = false
      let isValid = true
      if (this.content.translations[this.langCode].title.trim().length === 0) {
        isValid = false
        this.validations.title = false
      } else {
        this.validations.title = true
      }
      if (this.content.translations[this.langCode].description.trim().length === 0) {
        isValid = false
        this.validations.description = false
      } else {
        this.validations.description = true
      }
      this.validations.validated = true
      return isValid
    },
    isNewHazardValid() {
      this.newHazardValidations.validated = false
      let isValid = true

      if (this.newHazard.name.trim().length === 0) {
        isValid = false
        this.newHazardValidations.name = false
      } else {
        this.newHazardValidations.name = true
      }

      if (this.newHazard.icon && this.newHazard.icon.size <= 30000) {
        this.newHazardValidations.icon = true
      } else {
        isValid = false
        this.newHazardValidations.icon = false
      }
      this.newHazardValidations.validated = true
      return isValid
    },
    resetHazardCreation() {
      this.selectedHazardType = null
      this.newHazard.name = ''
      this.newHazard.icon = null

      this.newHazardValidations.validated = false
      this.newHazardValidations.name = false
      this.newHazardValidations.icon = false

      this.isCreateHazardMode = false
    },
    triggerAutoSave() {
      if (this.savingContent) return

      if (this.resetAutoSaveTimeoutID) {
        clearTimeout(this.resetAutoSaveTimeoutID)
      }
      this.resetAutoSaveTimeoutID = setTimeout(() => {
        this.saveContent({ isAutoSaving: true })
      }, 5000)
    },
    async saveContent({ isAutoSaving = false } = {}) {
      this.savingContent = true
      this.isAutoSavingContent = isAutoSaving
      this.validations.showErrors = !isAutoSaving

      if (this.isCreateHazardMode && this.isNewHazardValid()) {
        try {
          const formData = new FormData()
          formData.append('name', this.newHazard.name)
          formData.append('icon', this.newHazard.icon)
          await this.$store.dispatch('content/createHazardType', formData)
          this.$noty.success(`${this.newHazard.name} ${this.$t('content.edit_whatnow.created')}`)
          await this.$store.dispatch('content/fetchHazardTypes')
          this.resetHazardCreation()
        } catch (e) {
          this.$noty.error(this.$t('error_alert_text'))
        }
      } else if (!this.isCreateHazardMode && this.isValid()) {
        try {
          if (this.isNew) {
            const hazardTypeFromDropdown = this.selectedHazardType && this.selectedHazardType.name ? this.selectedHazardType.name : null

            this.content.eventType = this.selectedHazardType && this.selectedHazardType.code === 'other' ? this.content.eventType : hazardTypeFromDropdown
            this.content.regionName = this.regionSlug || ''
            // Are we adding this to an event type already created? If so write the translation to that one
            await this.$store.dispatch('content/fetchContent', this.organisation)
            let newContent = this.currentContent.find(content => content.eventType === this.content.eventType && content.regionName === (this.regionSlug || 'National'))
            if (newContent) {
              newContent.translations[this.langCode] = this.content.translations[this.langCode]
              this.content = newContent
              await this.$store.dispatch('content/saveContent', { data: this.content })
            } else {
              await this.$store.dispatch('content/createContent', { data: this.content })
            }

            if (this.isAutoSavingContent) {
              // Fetch the Hazards
              await this.$store.dispatch('content/fetchContent', this.organisation)
              newContent = this.currentContent.find(content => content.eventType === this.content.eventType && content.regionName === (this.regionSlug || 'National'))
              this.content = newContent
              // Redirect user to Edit Mode
              this.$router.replace({
                name: 'content.editWhatnow',
                params: {
                  whatnowId: newContent.id,
                  langCode: this.langCode,
                  regionSlug: this.regionSlug,
                  // Ensure the browser doesn't scroll to the top.
                  scrollPosition: {
                    x: 0,
                    y: document.querySelector("html").scrollTop
                  }
                }
              })
            }
          } else {
            await this.$store.dispatch('content/saveContent', { data: this.content })
          }

          if (!this.isAutoSavingContent) {
            if (this.isNew) {
              this.$fireGTEvent(this.$gtagEvents.CreateHazard)
            } else {
              this.$fireGTEvent(this.$gtagEvents.EditHazard)
            }

            this.$noty.success(`${this.content.eventType} ${this.$t('content.edit_whatnow.updated')} (${this.langCode.toUpperCase()})`)
            this.$router.push({ name: 'content.whatnow', params: { countryCode: this.content.countryCode, regionSlug: this.regionSlug } })
          }
        } catch (e) {
          if (e.response.status === 422 && e.response.data && e.response.data.errors) {
            for (const [key, value] of Object.entries(e.response.data.errors)) {
              this.$noty.error(value.join(', '))
            }
          } else {
            this.$noty.error(this.$t('error_alert_text'))
          }
        }
      }

      this.savingContent = false
      this.isAutoSavingContent = false
    },
    toggleUrgencyCollapse(urgency) {
      this.urgencyCollapses[urgency] = !this.urgencyCollapses[urgency]
    },
  },
  metaInfo() {
    return { title: this.$t('content.whatnow.whatnow') }
  },
  computed: {
    isNew() {
      return !!(this.organisation && this.langCode)
    },
    isOtherHazardType() {
      return !!(this.selectedHazardType && this.selectedHazardType.code === 'other')
    },
    filteredHazardsList() {
      const createOther = { 'name': 'Create Hazard Type', 'icon': 'add@3x.png', 'code': 'create', 'url': null }
      const alphabeticalHazardList = [...this.hazardsList].sort((a, b) => a.name.localeCompare(b.name))

      // 'other' hazard type should always be at the end of the list
      for (const key in alphabeticalHazardList) {
        alphabeticalHazardList[key].code == 'other' ? alphabeticalHazardList.push(alphabeticalHazardList.splice(key, 1)[0]) : 0
      }

      if (this.user) {
        if (this.can(this.user, permissionsList.HAZARD_TYPE_CREATE)) {
          return [...alphabeticalHazardList, createOther]
        } else {
          return [...alphabeticalHazardList]
        }
      }
    },
    canEdit() {
      return this.can(this.user, permissionsList.CONTENT_EDIT)
    },
    ...mapGetters({
      user: 'auth/user',
      hazardsList: 'content/hazardsList',
      currentInstructions: 'content/currentInstructions',
      currentContent: 'content/currentContent'
    })
  }
}
</script>

<style scoped lang="scss">
@import '../../../sass/variables.scss';

.edit-whatnow {
  position: relative;
  .save-controls-container {
    padding: 16px 24px;
    position: fixed;
    bottom: 0;
    right: 0;
    height: 70px;
    width: 100%;
    background-color: $white;
    z-index: 100;
  }
}

.content-editor-header {
  position: relative;
  padding: 26px 0;
  &::after {
    content: '';
    display: block;
    width: 98%;
    height: 1px;
    background-color: $cad-solid-bg-3;
    margin: 24px auto 0 auto;
  }

  .content-editor-title {
    font-size: 55px;
    font-weight: 600;
    color: $text-dark;
  }

  .content-editor-description {
    font-size: 11px;
    color: $text-black;
  }
}

.hazard-form {
  .hazard-form-input-container {
    margin-bottom: 16px;

    .hazard-form-label {
      font-size: 20px;
      font-weight: 500;
      color: $text-dark;
      width: max-content;
      position: relative;
    }

    .hazard-form-label.required {
      &::after {
        content: '*';
        color: $red;
        font-size: 20px;
        font-weight: 600;
        position: absolute;
        right: -12px;
        top: 0;
      }
    }

    .hazard-form-input {
      border-radius: 6px;
      background-color: $card-solid-bg;
      height: 48px;
      font-size: 11px;
      border: none;
      outline: none;
      color: #000;
    }

    .hazard-form-input.is-invalid {
      border: solid 1.2px $red;
    }

    textarea.hazard-form-input {
      height: 80px;
    }
  }
}

.urgency-card {
  padding: 16px 24px;
  border-radius: 8px;
  border: solid 0.8px $urgency-card-border;
  position: relative;
  box-shadow: 0px 1.6px 6.4px rgba(0, 0, 0, 0.1);

  .urgency-card-header {
    width: 100%;
    margin-bottom: 24px;

    h4.urgency-title {
      font-size: 24px;
      font-weight: 600;
      letter-spacing: -0.32px;
      color: $text-dark;
    }

    p.urgency-description {
      font-size: 14px;
      font-weight: normal;
      color: $text-black;
    }

    &::after {
      content: '';
      display: block;
      width: 100%;
      height: 1px;
      background-color: $cad-solid-bg-3;
      margin-top: 16px;
    }
  }

  .btn-collapse {
    font-size: 24px;
    border: none;
    background-color: transparent;
    color: $grey;
  }
}
</style>