<template>
  <b-container fluid class="api-dash">
    <b-col class="mt-4 mb-2 pl-4 pr-4">
      <h1>{{ $t('applications.api_keys') }}</h1>
    </b-col>
    <b-row class="pl-4 pr-4 pb-4 pt-4">
      <b-col>
        <!-- TERMS CONDITION NOTI -->
         <div class="terms-notification" visible id="terms" v-if="latestTerms && user.data.user_profile.terms_version !== latestTerms.version">
            <h4 class="alert-heading text-uppercase">{{ $t('applications.new_tc') }}</h4>
            <p>{{ $t('applications.terms_conditions_message') }} <span class="terms-notification-version">{{ latestTerms.version }}</span></p>
            <div>
              <b-button variant="outline-primary" :to="{ name: 'legal_terms', params: {} }" class="mr-2">{{ $t('View') }}</b-button>
              <b-button variant="primary"  @click="acceptTerms" v-b-toggle.terms>
                {{ $t('common.accept') }}
              </b-button>
            </div>
         </div>
        <!-- END -->
        <b-row class="align-items-baseline">
          <b-col>
          </b-col>
          <b-col>
            <b-button class="text-uppercase float-right rtl-float-left pl-4 pr-4 btn-outline-primary"v-b-modal.create-app :disabled="creatingApp">
              <span v-if="!creatingApp">{{ $t('applications.build') }} <fa class="ml-2 rtl-mr-2" :icon="['fas', 'plus']"/></span>
              <span v-else>{{ $t('applications.building') }}</span>
            </b-button>
          </b-col>
        </b-row>
          <transition-group name="fade-slide" tag="b-row">
            <b-col md="4" sm="6" xs="12" class="mb-3" v-for="app in apps" :key="app.id">
              <b-card class="api-dash-app-card p-2 h-100" body-class="d-flex flex-column justify-content-between">
                <div>
                  <avatar class="api-avatar mb-2" :size="72" :username="app.name" :initials="app.name.charAt(0).toUpperCase()" :rounded="false"
                    :customStyle="{ 'border-radius': '13px', 'font-size': '36px', 'display': 'inline-block' }"></avatar>
                    <b-dropdown class="float-right rtl-float-left app-settings-dropdown" :no-caret="true">
                      <template slot="button-content" class="rounded">
                        <fa :icon="['fas', 'ellipsis-v']"/>
                      </template>
                      <b-dropdown-item @click="deleteApp(app.id)">{{ $t('common.delete') }}</b-dropdown-item>
                      <b-dropdown-item @click="editApp(app)">{{ $t('common.edit') }}</b-dropdown-item>
                    </b-dropdown>
                  <h4>{{ app.name }}</h4>
                  <p class="mb-2">
                    {{ app.description }}
                  </p>
                  <p v-if="app.estimatedUsers !== 'undefined'" class="mb-0">{{ `${$t('common.estimated_users')}: ${app.estimatedUsers}` }}</p>
                </div>
                <div>
                  <hr />
                  <div class="d-flex justify-content-between api-key-panel align-items-center p-3">
                    <div :ref="app.id">
                      <small>{{ app.key }}</small>
                    </div>
                    <div class="text-uppercase">
                      <b-button variant="link"
                        @click="copyToClip(app.id)"
                        class="text-dark underlined-link"
                        :id="`tooltip-${app.id}`">{{ $t('common.copy') }}</b-button>
                    </div>
                  </div>
                </div>
                <b-tooltip ref="tooltip" :target="`tooltip-${app.id}`" :show.sync="app.id === copied" v-if="app.id === copied">
                  {{ $t('common.copied') }}
                </b-tooltip>
              </b-card>
            </b-col>
          </transition-group>
          <h3 v-if="apps.length === 0" class="text-center"> {{ $t('applications.no_apps') }} </h3>
      </b-col>
    </b-row>

    <!-- Create app modal -->
    <b-modal  id="create-app"
              centered
              :title="isEdit ? $t('applications.edit') : $t('applications.build_key')"
              ref="createModal"
              @ok="createApp"
              @shown="clearApp"
              ok-title="OK"
              cancel-title="Cancel"
              ok-variant="outline-primary"
              cancel-variant="outline-primary">
      <b-form-group
          id="fieldset-name"
          :label="$t('common.name')"
          label-for="new-name"
          :invalid-feedback="newAppValidations.feedback"
          :state="newAppValidations.validated ? newAppValidations.name : null">
        <b-form-input :disabled="isEdit" id="new-name" :state="newAppValidations.validated ? newAppValidations.name : null" v-model.trim="newApp.name"></b-form-input>
      </b-form-group>
      <b-form-group
          id="fieldset-description"
          :label="$t('common.description')"
          label-for="new-description"
          :invalid-feedback="newAppValidations.feedback"
          :state="newAppValidations.validated ? newAppValidations.description : null">
        <b-form-input :disabled="isEdit" id="new-description" :state="newAppValidations.validated ? newAppValidations.description : null" v-model.trim="newApp.description"></b-form-input>
      </b-form-group>
      <b-form-group
          id="fieldset-users-count"
          :label="$t('common.estimated_users')"
          label-for="new-users-count"
          :invalid-feedback="newAppValidations.feedback"
          :state="newAppValidations.validated ? newAppValidations.estimatedUsers : null">
        <b-form-input type="number" min="0" id="new-users-count" :state="newAppValidations.validated ? newAppValidations.estimatedUsers : null" v-model.trim="newApp.estimatedUsers"></b-form-input>
      </b-form-group>
    </b-modal>
  </b-container>
</template>

<script>
import { mapGetters } from 'vuex'
import Avatar from 'vue-avatar'
import swal from 'sweetalert2'
import axios from 'axios'
import PageBanner from '~/components/PageBanner'
import LatestTerms from '~/components/LatestTerms'

export default {
  components: {
    Avatar,
    PageBanner,
    LatestTerms
  },

  metaInfo () {
    return { title: this.$t('home.home') }
  },

  mounted () {
    this.fetchApps()
    this.fetchLatestTerms()
  },

  methods: {
    async fetchApps () {
      try {
        await this.$store.dispatch('apps/fetchUserApps')
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
        console.error(e)
      }
    },
    async fetchLatestTerms () {
      try {
        await this.$store.dispatch('terms/fetchLatestTerms')
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
        console.error(e)
      }
    },
    copyToClip (ref) {
      window.getSelection().selectAllChildren(this.$refs[ref][0])
      document.execCommand('copy')
      this.copied = ref
    },
    editApp (app) {
      this.newApp = app
      this.isEdit = true
      this.$refs.createModal.show()
    },
    deleteApp (id) {
      swal({
        title: this.$t('common.are_you_sure'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: this.$t('common.delete')
      }).then(async () => {
        try {
          await this.$store.dispatch('apps/deleteApp', id)
        } catch (e) {
          this.$noty.error(this.$t('error_alert_text'))
          console.error(e)
        }
        this.fetchApps()
      }).catch(swal.noop)
    },
    async createApp (evt) {
      // Prevent modal from closing
      evt.preventDefault()
      this.newAppValidations.validated = true

      this.newAppValidations.name = !!this.newApp.name
      this.newAppValidations.description = !!this.newApp.description
      this.newAppValidations.estimatedUsers = !!this.newApp.estimatedUsers

      this.$fireGTEvent(this.$gtagEvents.NewApp)

      if (this.newApp.description && this.newApp.name) {
        this.creatingApp = true
        try {
          if (this.isEdit) {
            await this.$store.dispatch('apps/editApp', this.newApp)
          } else {
            await this.$store.dispatch('apps/createApp', this.newApp)
          }
          this.creatingApp = false
          this.$refs['createModal'].hide()
        } catch (e) {
          this.$noty.error(this.$t('error_alert_text'))
          this.creatingApp = false
          console.error(e)
        }
        this.fetchApps()
        this.creatingApp = false
        this.isEdit = false
      }
    },
    clearApp () {
      if (!this.isEdit) {
        this.newApp = {
          name: '',
          description: ''
        }
      }

      this.newAppValidations.validated = false
    },
    async acceptTerms () {
      const changes = {
        accepted_agreement: true
      }

      await this.$store.dispatch('auth/patchUser', { id: this.user.data.id, changes })
    }
  },
  data () {
    return {
      title: window.config.appName,
      copied: null,
      creatingApp: false,
      openTermsModal: false,
      isEdit: false,
      newApp: {
        name: '',
        description: '',
        estimatedUsers: ''
      },
      newAppValidations: {
        name: true,
        feedback: this.$t('common.not_empty'),
        description: true,
        estimatedUsers: true,
        validated: false
      }
    }
  },
  computed: mapGetters({
    user: 'auth/user',
    apps: 'apps/userApps',
    currentApp: 'apps/currentApp',
    latestTerms: 'terms/latest'
  })
}
</script>

<style lang="scss" scoped>
@import '../../../sass/variables.scss';
.terms-notification {
  flex-grow: 0;
  padding: 15.2px 18px 13px;
  border-radius: 10px;
  background-color: #e6e6e6;
  margin-bottom: 16px;
  color: $text-black;
  font-size: 14px;
  font-weight: normal;


  .terms-notification-version {
    color: $text-primary;
  }

  .btn-outline-primary {
    padding: 2px 16px;
  }

  .btn-primary  {
    padding: 4px 16px;
  }
}
</style>