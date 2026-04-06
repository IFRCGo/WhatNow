<template>
  <b-container fluid class="home-page">
    <b-row class="p-4" v-if="user">
      <b-col cols="12">
        <h2 class="mb-3">
          {{ $t('home_pods.wellcome') }}
          {{ user.data.user_profile.first_name }}!
        </h2>
        <div v-if="can(user, permissions.USERS_LIST)">
          <b-row>
            <b-col sm class="mb-5">
              <b-card class="h-100 home-card users-card">
                <div>
                  <h4 class="card-title mb-3">
                    {{ $t('sidebar.content_users') }}
                  </h4>
                  <p class="card-text">
                    {{ $t('home_pods.manage_users') }}
                  </p>
                </div>
                <div class="button-container">
                  <i class="fas fa-users font-icon"></i>
                  <b-button :to="{ name: 'users.list', params: {} }" variant="danger" class="button-go">{{
                      $t('go')
                    }}
                  </b-button>
                </div>
              </b-card>
            </b-col>

            <b-col sm class="mb-5" v-if="firstSocietyCode">
              <b-card class="h-100 home-card content-card">
                <div>
                  <h4 class="card-title mb-3">{{ $t('sidebar.content') }}</h4>
                  <p class="card-text">
                    {{ $t('home_pods.whatnow_publish') }}
                  </p>
                </div>
                <div class="pt-5 button-container">
                  <i class="fas fa-comment-alt font-icon"></i>
                  <b-button :to="{ name: 'content.whatnow', params: { countryCode: firstSocietyCode } }"
                            variant="danger" class="button-go">{{ $t('go') }}
                  </b-button>
                </div>
              </b-card>
            </b-col>

            <b-col sm class="mb-5">
              <b-card class="h-100 home-card api-card">
                <div>
                  <h4 class="card-title">{{ $t('sidebar.api') }}</h4>
                  <p class="card-text">
                    {{ $t('home_pods.api_usage') }}
                  </p>
                </div>
                <div class="pt-5 button-container">
                  <i class="fas fa-laptop font-icon"></i>
                  <b-button :to="{ name: 'api-usage.api-users', params: {} }" variant="danger"
                            class="button-go">
                    {{ $t('go') }}
                  </b-button>
                </div>
              </b-card>
            </b-col>
          </b-row>
        </div>

        <div v-if="can(user, permissions.CONTENT_PUBLISH) && cannot(user, permissions.USERS_LIST)">
          <p>
            {{ $t('home.ns_admin_intro') }}
          </p>

          <b-row>
            <b-col sm class="mb-5" v-if="firstSocietyCode">
              <b-card class="h-100 home-card content-card">
                <div>
                  <h4 class="card-title">{{ $t('home.go_publish_safety_messages') }}</h4>
                  <p class="card-text">
                    {{ $t('home_pods.whatnow_publish') }}
                  </p>
                </div>
                <div class="pt-5 button-container">
                  <i class="fas fa-comment-alt font-icon"></i>
                  <b-button :to="{ name: 'content.whatnow', params: { countryCode: firstSocietyCode } }"
                            variant="danger"
                            class="button-go">{{ $t('go') }}
                  </b-button>
                </div>
              </b-card>
            </b-col>

            <b-col sm class="mb-5">
              <b-card class="h-100 home-card api-card">
                <div>
                  <h4 class="card-title">{{ $t('home.go_audit_log') }}</h4>
                  <p class="card-text">
                    {{ $t('home_pods.audit_log') }}
                  </p>
                </div>
                <div class="pt-5 button-container">
                  <i class="fas fa-history font-icon"></i>
                  <b-button :to="{ name: 'content.audit_log', params: {} }" variant="danger" class="button-go">{{
                      $t('go')
                    }}
                  </b-button>
                </div>
              </b-card>
            </b-col>

            <b-col sm class="mb-5">
              <b-card class="h-100 home-card content-card">
                <div>
                  <h4 class="card-title">{{ $t('home.go_bulk_upload') }}</h4>
                  <p class="card-text">
                    {{ $t('home_pods.bulk_upload') }}
                  </p>
                </div>
                <div class="pt-5 button-container">
                  <i class="fa fa-upload font-icon"></i>
                  <b-button :to="{ name: 'content.bulk_upload', params: {} }" variant="danger" class="button-go">
                    {{ $t('go') }}
                  </b-button>
                </div>
              </b-card>
            </b-col>
          </b-row>
        </div>

        <div v-if="can(user, permissions.CONTENT_CREATE) && cannot(user, permissions.CONTENT_PUBLISH)">
          <p>
            {{ $t('home.ns_editor_intro') }}
          </p>

          <b-row>
            <b-col sm class="mb-5" v-if="firstSocietyCode">
              <b-card class="h-100 home-card content-card" v-if="firstSocietyCode">
                <div>
                  <h4 class="card-title">{{ $t('sidebar.content') }}</h4>
                  <p class="card-text">
                    {{ $t('home_pods.whatnow_edit') }}
                  </p>
                </div>
                <div class="pt-5 button-container">
                  <i class="fas fa-comment-alt font-icon"></i>
                  <b-button :to="{ name: 'content.whatnow', params: { countryCode: firstSocietyCode } }"
                            variant="danger"
                            class="button-go">{{ $t('go') }}
                  </b-button>
                </div>
              </b-card>
            </b-col>

            <b-col sm class="mb-5">
              <b-card class="h-100 home-card api-card">
                <div>
                  <h4 class="card-title">{{ $t('sidebar.audit_log') }}</h4>
                  <p class="card-text">
                    {{ $t('home_pods.audit_log') }}
                  </p>
                </div>
                <div class="pt-5 button-container">
                  <i class="fas fa-history font-icon"></i>
                  <b-button :to="{ name: 'content.audit_log', params: {} }" variant="danger" class="button-go">{{
                      $t('go')
                    }}
                  </b-button>
                </div>
              </b-card>
            </b-col>

            <b-col sm class="mb-5">
              <b-card class="h-100 home-card content-card">
                <div>
                  <h4 class="card-title">{{ $t('sidebar.bulk') }}</h4>
                  <p class="card-text">
                    {{ $t('home_pods.bulk_upload') }}
                  </p>
                </div>
                <div class="pt-5 button-container">
                  <i class="fa fa-upload font-icon"></i>
                  <b-button :to="{ name: 'content.bulk_upload', params: {} }" variant="danger" class="button-go">
                    {{ $t('go') }}
                  </b-button>
                </div>
              </b-card>
            </b-col>
          </b-row>
        </div>

        <div v-else-if="can(user, permissions.CONTENT_VIEW) && cannot(user, permissions.CONTENT_CREATE)">
          <b-row>
            <b-col sm class="mb-5" v-if="firstSocietyCode">
              <b-card class="h-100 home-card content-card" v-if="firstSocietyCode">
                <div>
                  <h4 class="card-title">{{ $t('sidebar.content') }}</h4>
                  <p class="card-text">
                    {{ $t('home_pods.whatnow_review') }}
                  </p>
                </div>
                <div class="pt-5 button-container">
                  <i class="fas fa-comment-alt font-icon"></i>
                  <b-button :to="{ name: 'content.whatnow', params: { countryCode: firstSocietyCode } }"
                            variant="danger"
                            class="button-go">{{ $t('go') }}
                  </b-button>
                </div>
              </b-card>
            </b-col>
          </b-row>
        </div>
      </b-col>
    </b-row>

    <b-modal id="role-changed" centered :title="$t('home.role_changed_title')" ref="roleChangedModal" ok-variant="primary" cancel-variant="outline-primary" @ok="confirmRole" :ok-title="$t('home.role_changed_confirm')" :cancel-title="$t('home.role_changed_dismiss')">
      <p>
        {{ $t('home.role_changed_p') }} "<span v-if="user.data.role.name">{{ user.data.role.name }}</span>".
      </p>
      <p>
        {{ $t('home.role_changed_p2') }}
      </p>
      <ul class="row" v-if="user.data.role.permissions">
        <li class="col col-6" v-for="item in user.data.role.permissions">
          {{ item.display_name }}
        </li>
      </ul>

    </b-modal>
  </b-container>
</template>

<script>
import {mapGetters} from 'vuex'
import * as permissionsList from '../store/permissions'
import methods from '../methods'

export default {
  beforeCreate() {
    // Redirect user if they are an API user to the application dash
    const user = this.$store.getters['auth/user']
    if (methods.cannot(user, permissionsList.VIEW_BACKEND)) {
      this.$router.push({name: 'applications.dash'})
    }
  },
  mounted () {
    this.showRoleChangedModal()

  },
  metaInfo() {
    return {title: this.$t('home.home')}
  },
  data: () => ({
    title: window.config.appName,
    permissions: permissionsList
  }),
  methods: {
    showRoleChangedModal() {
      const isConfirmed = this.user.data.confirmed_role;
      if (!isConfirmed) {
        this.$refs.roleChangedModal.show()
      }
    },
    async confirmRole() {
      const changes = {
        confirmed_role: true
      }
      await this.$store.dispatch('auth/patchUser', { id: this.user.data.id, changes })
    }
  },
  computed: {
    firstSocietyCode() {
      if (this.user) {
        if (this.can(this.user, permissionsList.ALL_ORGANISATIONS)) {
          return 'USA'
        }
        if (this.user.data.organisations && this.user.data.organisations.length === 0) {
          return null
        } else if (this.user.data.organisations) {
          return this.user.data.organisations[0]
        }
      }
      return null
    },
    ...mapGetters({
      user: 'auth/user'
    })
  }
}
</script>
<style>
.button-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: -2rem;
}

.font-icon {
  font-size: 3rem;
  margin-left: 1rem;
  color: #B6B6B6;
}

</style>
