<template>
  <!-- WN MESSAGE EDITOR -->
  <b-row class="whatnow-row whatnow-row--message_editor mt-2" v-if="!isPromo">
    <b-col lg="2" class="whatnow-col-item--message_editor">
      {{ content.eventType }}
    </b-col>
    <b-col lg="3" class="whatnow-col-item--message_editor">
      <span v-if="contentExists('title', content)">{{ truncate(content.currentTranslation.title, 90) }}</span>
      <span class="border border-warning p-1 rounded bg-warning" v-else>{{ $t('content.whatnow.no_translation') }}</span>
    </b-col>
    <b-col lg="3" class="whatnow-col-item--message_editor">
      <span v-if="contentExists('description', content)">{{ truncate(content.currentTranslation.description, 90)
        }}</span>
      <span class="border border-warning p-1 rounded bg-warning" v-else>{{ $t('content.whatnow.no_translation') }}</span>
    </b-col>
    <b-col lg="2" class="whatnow-col-item--message_editor">
      <span v-if="contentExists('published', content)">{{ content.currentTranslation.published ? $t('no') : $t('yes')
        }}</span>
      <span v-else>-</span>
    </b-col>
    <b-col lg="2" class="whatnow-col-item--message_editor">
      <b-button variant="outline-primary" size="sm" class="mb-1 mr-1" :to="editLink"
        v-if="(can(user, permissions.CONTENT_EDIT) || can(user, permissions.CONTENT_VIEW))"
        :disabled="deletingContentTranslation === content.id">
        <font-awesome-icon :icon="['fas', 'pen']" />
        {{ can(user, permissions.CONTENT_EDIT) ? $t('common.edit') : $t('common.view_content') }}
      </b-button>
      <b-button variant="outline-primary" size="sm" class="mb-1" v-if="can(user, permissions.CONTENT_DELETE) && !forceCreate"
        :disabled="deletingContentTranslation === content.id" @click="deleteContentTranslation(content.id)">
        <font-awesome-icon :icon="['fas', 'spinner']" spin v-show="deletingContentTranslation === content.id" />
        <font-awesome-icon :icon="['fas', 'trash']" />
        {{ $t('common.delete') }}
      </b-button>
    </b-col>
  </b-row>
  <!--END WN MESSAGE EDITOR -->

  <!-- WN MESSAGE -->
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
                    <whatnow-message-card v-if="content.currentTranslation.stages[stageKey]"
                      :stage="content.currentTranslation.stages[stageKey]"
                      :stageName="stageKey" :eventType="content.eventType"
                      :title="content.currentTranslation.title"
                      :description="content.currentTranslation.description"
                      :selectedSoc="selectedSoc"
                    />
                  </b-col>
                </b-row>
              </b-card-body>
          </b-collapse>
        </b-card-header>
      </b-card>
    </b-col>
  </b-row>
</template>
<script>
import { mapGetters } from 'vuex'
import * as permissionsList from '../../store/permissions'
import swal from 'sweetalert2'
import WhatnowDownloadImage from './whatnowDownloadImage'
import whatnowPrevisualizer from './whatnowPrevisualizer'
import WhatnowMessageCard from './whatnowMessageCard'

export default {
  props: ['selectedLanguage', 'content', 'isPromo', 'regionSlug', 'forceCreate', 'selectedSoc'],
  components: {
    WhatnowDownloadImage,
    whatnowPrevisualizer,
    WhatnowMessageCard,
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
  padding: 2rem;
  margin-top: -1.5rem;
}

.hazard-cards-container {
  background: #E1E1E1;
  border-radius: 10px;
  padding: 1rem;
  margin: 2rem;
}

.button-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* MESSAGE EDITOR */
.whatnow-row--message_editor {
  border-radius: 10px;
  background-color: #f7f7f7;
  font-size: 12.5px;
  color: #000;
  height: 100px;

  .whatnow-col-item--message_editor {
    border-right: 3px solid #fff;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    &:last-of-type {
      border: none;
    }

    .btn {
      padding: 0 14px;
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
  font-size: 20px;
}

.subtitle-in {
  font-weight: 500;
  font-size: 18px;
}

.subtitle-card {
  font-weight: 600;
  font-size: 18px;
  color: $red;
}

.card-title {
  font-weight: 500;
  font-size: 24px;
}

.card-text {
  font-weight: 400;
  font-size: 14px;
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