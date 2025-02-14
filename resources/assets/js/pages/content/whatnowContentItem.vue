<template>
  <b-row class="whatnow-row mt-2 border border-secondary" v-if="!isPromo">
    <b-col class="border border-top-0 border-bottom-0 border-left-0 pt-5 pb-5">
      {{ content.eventType }}
    </b-col>
    <b-col class="border border-top-0 border-bottom-0 border-left-0 pt-5 pb-5">
      <span v-if="contentExists('title', content)">{{ truncate(content.currentTranslation.title, 90) }}</span>
      <span class="border border-warning p-1 rounded bg-warning" v-else>{{ $t('content.whatnow.no_translation')}}</span>
    </b-col>
    <b-col class="border border-top-0 border-bottom-0 border-left-0 pt-5 pb-5">
      <span v-if="contentExists('description', content)">{{ truncate(content.currentTranslation.description, 90) }}</span>
      <span class="border border-warning p-1 rounded bg-warning" v-else>{{ $t('content.whatnow.no_translation')}}</span>
    </b-col>
    <b-col class="border border-top-0 border-bottom-0 border-left-0 pt-5 pb-5">
      <span v-if="contentExists('published', content)">{{ content.currentTranslation.published ? $t('no') : $t('yes') }}</span>
      <span v-else>-</span>
    </b-col>
    <b-col class="pt-5 pb-5">
      <b-button
        variant="primary"
        size="sm"
        class="mb-1"
        :to="editLink"
        v-if="(can(user, permissions.CONTENT_EDIT) || can(user, permissions.CONTENT_VIEW))"
        :disabled="deletingContentTranslation === content.id">
          {{ can(user, permissions.CONTENT_EDIT) ?  $t('common.edit') : $t('common.view_content') }}
      </b-button>
      <b-button
        variant="danger"
        size="sm"
        class="mb-1"
        v-if="can(user, permissions.CONTENT_DELETE) && !forceCreate"
        :disabled="deletingContentTranslation === content.id"
        @click="deleteContentTranslation(content.id)">
          <fa :icon="['fas', 'spinner']" spin v-show="deletingContentTranslation === content.id"/>
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
                {{ showCollapse ? content.currentTranslation.description : truncate(content.currentTranslation.description, 90) }}
              </p>
            </div>
            <div class="ml-auto rtl-mr-auto">
              <b-button @click="showCollapse = !showCollapse" variant="link" class="rounded-circle">
                <fa icon="chevron-down" size="2x" class="animate-font-awesome" :transform="{ rotate: showCollapse ? 180 : 0 }"/>
              </b-button>
            </div>
          </div>
          <b-collapse class="collapse-content" :id="`accordion-${content.id}`" accordion="content-accordion" role="tabpanel" v-model="showCollapse">
            <div class="pl-3">
              <h4 class="card-tit mb-3 subtitle-in">
                {{ $t('content.whatnow.date_added') }}:
              </h4>
              <h4 class="card-tit mb-3 subtitle-in">
                {{ $t('content.whatnow.url') }}:
              </h4>
              <h4 class="card-tit mb-3 subtitle-in">
                {{ $t('content.whatnow.hazard_type') }}:
              </h4>
            </div>
            <b-card-body class="whatnow-collapse-card-body">
              <b-row class="hazard-cards-container">
                <b-col cols="12">
                  <h4 class="subtitle-card">{{ $t('content.whatnow.early') }}</h4>
                </b-col>
                <b-col class="hazard-card" cols="4">
                  <b-card class="h-100 home-card users-card">
                    <div class="mb-2">
                      <h4 class="card-title mb-3">
                        {{ $t('sidebar.content_users') }}
                      </h4>
                      <p class="card-text">
                        {{ $t('home_pods.manage_users') }}
                      </p>
                    </div>
                    <div class="button-container mt-4 mb-4">
                      <div></div>
                      <b-button :to="{ name: 'users.list', params: {} }" variant="danger" class="button-go">{{
                          $t('content.whatnow.show_more')
                        }}
                      </b-button>
                    </div>
                  </b-card>
                </b-col>
                <b-col class="hazard-card" cols="4">
                  <b-card class="h-100 home-card users-card">
                    <div>
                      <h4 class="card-title mb-3">
                        {{ $t('sidebar.content_users') }}
                      </h4>
                      <p class="card-text">
                        {{ $t('home_pods.manage_users') }}
                      </p>
                    </div>
                    <div class="button-container mt-4 mb-4">
                      <div></div>
                      <b-button :to="{ name: 'users.list', params: {} }" variant="danger" class="button-go">{{
                          $t('content.whatnow.show_more')
                        }}
                      </b-button>
                    </div>
                  </b-card>
                </b-col>
                <b-col class="hazard-card" cols="4">
                  <b-card class="h-100 home-card users-card">
                    <div>
                      <h4 class="card-title mb-3">
                        {{ $t('sidebar.content_users') }}
                      </h4>
                      <p class="card-text">
                        {{ $t('home_pods.manage_users') }}
                      </p>
                    </div>
                    <div class="button-container mt-4 mb-4">
                      <div></div>
                      <b-button :to="{ name: 'users.list', params: {} }" variant="danger" class="button-go">{{
                          $t('content.whatnow.show_more')
                        }}
                      </b-button>
                    </div>
                  </b-card>
                </b-col>
              </b-row>
               <b-row class="hazard-cards-container">
                <b-col cols="12">
                  <h4 class="subtitle-card">{{ $t('content.whatnow.disaster') }}</h4>
                </b-col>
                <b-col class="hazard-card" cols="4">
                  <b-card class="h-100 home-card users-card">
                    <div class="mb-2">
                      <h4 class="card-title mb-3">
                        {{ $t('sidebar.content_users') }}
                      </h4>
                      <p class="card-text">
                        {{ $t('home_pods.manage_users') }}
                      </p>
                    </div>
                    <div class="button-container mt-4 mb-4">
                      <div></div>
                      <b-button :to="{ name: 'users.list', params: {} }" variant="danger" class="button-go">{{
                          $t('content.whatnow.show_more')
                        }}
                      </b-button>
                    </div>
                  </b-card>
                </b-col>
                <b-col class="hazard-card" cols="4">
                  <b-card class="h-100 home-card users-card">
                    <div>
                      <h4 class="card-title mb-3">
                        {{ $t('sidebar.content_users') }}
                      </h4>
                      <p class="card-text">
                        {{ $t('home_pods.manage_users') }}
                      </p>
                    </div>
                    <div class="button-container mt-4 mb-4">
                      <div></div>
                      <b-button :to="{ name: 'users.list', params: {} }" variant="danger" class="button-go">{{
                          $t('content.whatnow.show_more')
                        }}
                      </b-button>
                    </div>
                  </b-card>
                </b-col>
                <b-col class="hazard-card" cols="4">
                  <b-card class="h-100 home-card users-card">
                    <div>
                      <h4 class="card-title mb-3">
                        {{ $t('sidebar.content_users') }}
                      </h4>
                      <p class="card-text">
                        {{ $t('home_pods.manage_users') }}
                      </p>
                    </div>
                    <div class="button-container mt-4 mb-4">
                      <div></div>
                      <b-button :to="{ name: 'users.list', params: {} }" variant="danger" class="button-go">{{
                          $t('content.whatnow.show_more')
                        }}
                      </b-button>
                    </div>
                  </b-card>
                </b-col>
              </b-row>
              <b-row class="hazard-cards-container">
                <b-col cols="12">
                  <h4 class="subtitle-card">{{ $t('content.whatnow.recovery') }}</h4>
                </b-col>
                <b-col class="hazard-card" cols="4">
                  <b-card class="h-100 home-card users-card">
                    <div class="mb-2">
                      <h4 class="card-title mb-3">
                        {{ $t('sidebar.content_users') }}
                      </h4>
                      <p class="card-text">
                        {{ $t('home_pods.manage_users') }}
                      </p>
                    </div>
                    <div class="button-container mt-4 mb-4">
                      <div></div>
                      <b-button :to="{ name: 'users.list', params: {} }" variant="danger" class="button-go">{{
                          $t('content.whatnow.show_more')
                        }}
                      </b-button>
                    </div>
                  </b-card>
                </b-col>
              </b-row>
              <!-- <b-card :class="`hazard-instruction-card mb-2 hazard-instruction-card`" v-if="content.translations[selectedLanguage].webUrl">
                  <h5 class="text-uppercase text-secondary">{{ $t(`view_data.more`) }}</h5>
                  <a :role="$t(`view_data.more`)" :href="content.translations[selectedLanguage].webUrl" rel="noreferrer" target="_blank"> {{ truncate(content.translations[selectedLanguage].webUrl, 90) }}</a>
                </b-card>
                <b-card :class="`hazard-instruction-card mb-2 pr-2 pl-2 hazard-instruction-card-${stageName}`"
                        v-if="instructions && instructions.length > 0"
                        v-for="(instructions, stageName) in content.translations[selectedLanguage].stages"
                        :key="stageName">
                  <h5 class="text-uppercase text-secondary">{{ $t(`content.edit_whatnow.${stageName}`) }}</h5>
                  <ol>
                    <li v-for="instruction in instructions" class="pt-1 pb-1">
                      {{ instruction }}
                    </li>
                  </ol>
                  <WhatnowDownloadImage
                    v-if="instructions.length > 0"
                    class="mt-2"
                    :instructionId="content.id"
                    :langCode="selectedLanguage"
                    :instructionName="stageName"
                  />
                </b-card> -->
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

  export default {
    props: ['selectedLanguage', 'content', 'isPromo', 'regionSlug', 'forceCreate'],
    components: {
      WhatnowDownloadImage
    },
    data () {
      return {
        deletingContentTranslation: null,
        permissions: permissionsList,
        showCollapse: false
      }
    },
    methods: {
      deleteContentTranslation (id) {
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
      contentExists (key, content) {
        if (content.currentTranslation) {
          if (content.currentTranslation[key] !== null && content.currentTranslation[key] !== undefined) {
            return true
          }
        }
        return false
      }
    },
    computed: {
      editLink () {
        if (!this.forceCreate) {
          return { name: 'content.editWhatnow', params: { whatnowId: this.content.id, langCode: this.selectedLanguage, regionSlug: this.regionSlug } }
        } else {
          return  {
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
  <style scoped>
    .collapse-content {
      padding: 3rem;
      margin-top: -2rem;
    }

    .card-tit {
    }

    .hazard-instruction-card {
      border: none
    }

    .home-card {
      background: #E9E9E9;
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

    .subtitle {
      font-weight: 500;
      font-size: 28px
    }

    .subtitle-in {
      font-weight: 500;
      font-size: 26px
    }

    .subtitle-card {
      font-weight: 600;
      font-size: 26px;
      color: red;
    }

    .card-title {
      font-weight: 500;
      font-size: 35px;
    }

    .card-text {
      font-weight: 400;
      font-size: 20px;
    }
  </style>
