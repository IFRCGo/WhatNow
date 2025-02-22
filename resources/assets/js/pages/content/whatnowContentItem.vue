<template>
  <b-row class="whatnow-row mt-2 border border-secondary" v-if="!isPromo">
    <b-col class="border border-top-0 border-bottom-0 border-left-0 pt-5 pb-5">
      {{ content.eventType }}
    </b-col>
    <b-col class="border border-top-0 border-bottom-0 border-left-0 pt-5 pb-5">
      <span v-if="contentExists('title', content)">{{ truncate(content.currentTranslation.title, 90) }}</span>
      <span class="border border-warning p-1 rounded bg-warning" v-else>{{ $t('content.whatnow.no_translation') }}</span>
    </b-col>
    <b-col class="border border-top-0 border-bottom-0 border-left-0 pt-5 pb-5">
      <span v-if="contentExists('description', content)">{{ truncate(content.currentTranslation.description, 90)
        }}</span>
      <span class="border border-warning p-1 rounded bg-warning" v-else>{{ $t('content.whatnow.no_translation') }}</span>
    </b-col>
    <b-col class="border border-top-0 border-bottom-0 border-left-0 pt-5 pb-5">
      <span v-if="contentExists('published', content)">{{ content.currentTranslation.published ? $t('no') : $t('yes')
        }}</span>
      <span v-else>-</span>
    </b-col>
    <b-col class="pt-5 pb-5">
      <b-button variant="primary" size="sm" class="mb-1" :to="editLink"
        v-if="(can(user, permissions.CONTENT_EDIT) || can(user, permissions.CONTENT_VIEW))"
        :disabled="deletingContentTranslation === content.id">
        {{ can(user, permissions.CONTENT_EDIT) ? $t('common.edit') : $t('common.view_content') }}
      </b-button>
      <b-button variant="danger" size="sm" class="mb-1" v-if="can(user, permissions.CONTENT_DELETE) && !forceCreate"
        :disabled="deletingContentTranslation === content.id" @click="deleteContentTranslation(content.id)">
        <fa :icon="['fas', 'spinner']" spin v-show="deletingContentTranslation === content.id" />
        {{ $t('common.delete') }}
      </b-button>
    </b-col>
  </b-row>
  <!-- Whatnow promo -->
  <b-row v-else>
    <b-col v-if="content.translations[selectedLanguage]">
      <b-card no-body class="whatnow-collapse-card">
        <b-card-header header-tag="header" class="pl-2 pt-3 pb-3 pr-2 rounded-bottom" role="tab">
          <div class="d-flex align-items-center">
            <div>
              <b-img :src="hazardIcon(content.eventType)" class="rounded-circle" width="60" height="60"></b-img>
            </div>
            <div class="ml-2 rtl-mr-2">
              <h4 class="subtitle">{{ content.currentTranslation.title }}</h4>
              <p v-if="contentExists('description', content)">
                {{ showCollapse ? content.currentTranslation.description :
                  truncate(content.currentTranslation.description, 90) }}
              </p>
            </div>
            <div class="ml-auto rtl-mr-auto">
              <b-button @click="showCollapse = !showCollapse" variant="link" class="rounded-circle">
                <fa icon="chevron-down" size="2x" class="animate-font-awesome"
                  :transform="{ rotate: showCollapse ? 180 : 0 }" />
              </b-button>
            </div>
          </div>
          <b-collapse class="collapse-content" :id="`accordion-${content.id}`" accordion="content-accordion"
            role="tabpanel" v-model="showCollapse">

            <div class="pl-3 content-info">
              <div class="d-flex align-items-center mb-3">
                <h4 class="card-title subtitle-in mr-3 mb-0">
                  {{ $t('content.whatnow.date_added') }}:
                </h4>
                <span class="content-info-value">{{ content.currentTranslation.created_at | moment("MM/DD/YY HH:mm") }}</span>
              </div>
              <div class="d-flex align-items-center mb-3">
                <h4 class="card-title subtitle-in mr-3 mb-0">
                  {{ $t('content.whatnow.url') }}:
                </h4>
                <span class="content-info-value">{{ content.currentTranslation.webUrl }}</span>
              </div>
              <div class="d-flex align-items-center mb-3">
                <h4 class="card-title subtitle-in mr-3 mb-0">
                  {{ $t('content.whatnow.hazard_type') }}:
                </h4>
                <span class="content-info-value">{{ content.eventType }}</span>
              </div>
            </div>

            <b-card-body class="whatnow-collapse-card-body" v-for="(urgency, index) in urgencyLevels" :key="'urgency'+index">
                <b-row class="hazard-cards-container">
                  <b-col cols="12">
                    <h4 class="subtitle-card">{{ urgency.text }}</h4>
                  </b-col>
                  <b-col 
                    class="hazard-card" 
                    cols="4" 
                    v-for="(stageKey, index) in Object.keys(content.currentTranslation.stages)" 
                    :key="'stage'+index" 
                    v-if="urgency.stages.includes(stageKey)"
                  >
                    <b-card class="h-100 key-message-card" v-if="content.currentTranslation.stages[stageKey]">
                      <div class="mb-2">
                        <h4 class="card-title mb-3">
                          {{ $t('content.edit_whatnow.'+stageKey) }}
                        </h4>
                        <div class="key-message-item" v-for="(keyMessage, index) in content.currentTranslation.stages[stageKey]" :key="'instruction'+index">
                          <h5 class="card-text key-message-item-title">
                            {{ keyMessage.title  }}
                          </h5>
                          <ul class="key-message-item-list">
                            <li class="text-card" v-for="(supportingMessage, index) in keyMessage.content" :key="'supportingMessage'+index">
                              {{ supportingMessage }}
                            </li>
                          </ul>
                        </div>
                      </div>
                      <div class="button-container mt-4 mb-4 key-message-item-btn">
                        <b-button variant="danger" class="button-go" @click="openKeyMessageVisualizer(content.currentTranslation.stages[stageKey], content.eventType)">
                          {{ $t('content.whatnow.show_more') }}
                        </b-button>
                      </div>
                    </b-card>
                  </b-col>
                </b-row>
              </b-card-body>
          </b-collapse>
        </b-card-header>
      </b-card>
      <b-modal size="xl"  ref="keyMessageVisualizer" :hide-header="true" id="pre-image" centered ok-variant="outline-primary" :ok-title="'Descargar'">
        <whatnowPrevisualizer :keyMessage="openedKeyMessage"></whatnowPrevisualizer>
      </b-modal>
    </b-col>
  </b-row>
</template>
<script>
import { mapGetters } from 'vuex'
import * as permissionsList from '../../store/permissions'
import swal from 'sweetalert2'
import WhatnowDownloadImage from './whatnowDownloadImage'
import whatnowPrevisualizer from './whatnowPrevisualizer.vue'

export default {
  props: ['selectedLanguage', 'content', 'isPromo', 'regionSlug', 'forceCreate'],
  components: {
    WhatnowDownloadImage,
    whatnowPrevisualizer
  },
  data() {
    return {
      deletingContentTranslation: null,
      permissions: permissionsList,
      showCollapse: false,
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
      openedKeyMessage: null
    }
  },
  methods: {
    deleteContentTranslation(id) {
      swal({
        title: this.$t('common.are_you_sure'),
        text: `${this.$t('content.whatnow.delete_content_translation')} (${this.selectedLanguage.toUpperCase()})`,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: this.$t('common.delete')
      }).then(async () => {
        this.deletingContentTranslation = id
        try {
          await this.$store.dispatch('content/deleteContentTranslation', id)
          this.$emit('languageChange')
          this.$noty.success(`${this.$t('common.removed')} ${this.content.eventType} (${this.selectedLanguage.toUpperCase()})`)
        } catch (e) {
          this.$noty.error(this.$t('error_alert_text'))
        }
        this.deletingContentTranslation = null
      }).catch(swal.noop)
    },
    contentExists(key, content) {
      if (content.currentTranslation) {
        if (content.currentTranslation[key] !== null && content.currentTranslation[key] !== undefined) {
          return true
        }
      }
      return false
    },
    openKeyMessageVisualizer(keyMessage) {
      this.openedKeyMessage = keyMessage
      this.$refs.keyMessageVisualizer.show()
    },
  },
  computed: {
    editLink() {
      if (!this.forceCreate) {
        return { name: 'content.editWhatnow', params: { whatnowId: this.content.id, langCode: this.selectedLanguage, regionSlug: this.regionSlug } }
      } else {
        return {
          name: 'content.create',
          params: {
            organisation: this.content.countryCode,
            langCode: this.selectedLanguage,
            regionSlug: this.regionSlug,
            eventTypeToCreate: this.content.eventType
          }
        }
      }
    },
    ...mapGetters({
      user: 'auth/user'
    })
  }
}
</script>
<style scoped lang="scss">
@import '../../../sass/variables.scss';

.collapse-content {
  padding: 2.1rem;
  margin-top: -1.4rem;
}

.key-message-item {
  margin-bottom: 11px;
  &::after {
    content: '';
    display: block;
    width: 90%;
    height: 0.7px;
    background-color: $cad-solid-bg-3;
    margin: 24px auto;
  }

  .key-message-item-title {
    font-size: 16.8px;
    font-weight: 500;
    color: #000;
  }

  ul.key-message-item-list {
    li {
      font-size: 14px;
      color: #1e1e1e;
    }
  }
}

.hazard-card {
  .key-message-item-btn {
    position: absolute;
    bottom: -14px;
    right: 7px;

    .btn {
      font-size: 14px;
    }
  }
}

.hazard-cards-container {
  background: #E1E1E1;
  border-radius: 7px;
  padding: 0.7rem;
  margin: 1.4rem;
}

.button-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.subtitle {
  font-weight: 500;
  font-size: 19.6px;
}

.subtitle-in {
  font-weight: 500;
  font-size: 18.2px;
}

.subtitle-card {
  font-weight: 600;
  font-size: 18.2px;
  color: red;
}

.card-title {
  font-weight: 500;
  font-size: 24.5px;
}

.card-text {
  font-weight: 400;
  font-size: 14px;
}

.key-message-card {
  background: #E9E9E9;
  padding-bottom: 40px;
}

.content-info {
  color: #1e1e1e;
  .card-title {
    font-weight: 500;
    font-size: 18.2px;
  }
  .content-info-value {
    font-size: 14px;
    font-weight: normal;
  }
}
</style>
