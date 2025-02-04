<template>
  <b-container fluid class="home-page">
    <b-row class="p-4" v-if="user">
      <b-col cols="12">
        <h2 class="mb-3">
          {{ $t('home_pods.wellcome') }}
          {{ user.data.user_profile.first_name }}!
        </h2>
        <p>{{ $t('home_pods.wellcome_description') }}</p>
        <div v-if="can(user, permissions.USERS_LIST)">
          <b-row>
            <b-col sm class="mb-2">
              <b-card class="h-100 p-3 home-card users-card pt-5">
                <div class="mt-5">
                  <h4 class="card-title mb-3">
                    {{ $t('sidebar.content_users') }}
                  </h4>
                  <p class="card-text">
                    {{ $t('home_pods.manage_users') }}
                  </p>
                </div>
                <div class="pt-5 button-container">
                  <img class="img-bottom" src="../..//img/home_page/people.png" alt="people">
                  <b-button :to="{ name: 'users.list', params: {} }" variant="danger" class="pl-4 pr-4 button-go">{{
                      $t('go')
                    }}
                  </b-button>
                </div>
              </b-card>
            </b-col>

            <b-col sm class="mb-2" v-if="firstSocietyCode">
              <b-card class="h-100 p-3 home-card content-card pt-5">
                <div class="mt-5">
                  <h4 class="card-title">{{ $t('sidebar.content') }}</h4>
                  <p class="card-text">
                    {{ $t('home_pods.whatnow_publish') }}
                  </p>
                </div>
                <div class="pt-5 button-container">
                  <img class="img-bottom" src="../..//img/home_page/speech.png" alt="speech">
                  <b-button :to="{ name: 'content.whatnow', params: { countryCode: firstSocietyCode } }"
                            variant="danger" class="pl-4 pr-4 button-go">{{ $t('go') }}
                  </b-button>
                </div>
              </b-card>
            </b-col>

            <b-col sm class="mb-2">
              <b-card class="h-100 p-3 home-card api-card pt-5">
                <div class="mt-5">
                  <h4 class="card-title">{{ $t('sidebar.api') }}</h4>
                  <p class="card-text">
                    {{ $t('home_pods.api_usage') }}
                  </p>
                </div>
                <div class="pt-5 button-container">
                  <img class="img-bottom" src="../..//img/home_page/computer.png" alt="computer">
                  <b-button :to="{ name: 'api-usage.api-users', params: {} }" variant="danger" class="pl-4 pr-4 button-go">
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
            <b-col sm class="mb-2" v-if="firstSocietyCode">
              <b-card class="h-100 p-3 home-card content-card pt-5">
                <div class="mt-5">
                  <h4 class="card-title">{{ $t('sidebar.content') }}</h4>
                  <p class="card-text">
                    {{ $t('home_pods.whatnow_publish') }}
                  </p>
                </div>
                <div class="pt-5 button-container">
                  <img class="img-bottom" src="../..//img/home_page/computer.png" alt="computer">
                  <b-button :to="{ name: 'content.whatnow', params: { countryCode: firstSocietyCode } }" variant="danger"
                            class="pl-4 pr-4">{{ $t('go') }}
                  </b-button>
                </div>
              </b-card>
            </b-col>

            <b-col sm class="mb-2">
              <b-card class="h-100 p-3 home-card api-card pt-5">
                <div class="mt-5">
                  <h4 class="card-title">{{ $t('sidebar.audit_log') }}</h4>
                  <p class="card-text">
                    {{ $t('home_pods.audit_log') }}
                  </p>
                </div>
                <div class="pt-5 button-container">
                  <img class="img-bottom" src="../..//img/home_page/computer.png" alt="computer">
                  <b-button :to="{ name: 'content.audit_log', params: {} }" variant="danger" class="pl-4 pr-4">{{
                      $t('go')
                    }}
                  </b-button>
                </div>
              </b-card>
            </b-col>

            <b-col sm class="mb-2">
              <b-card class="h-100 p-3 home-card content-card pt-5">
                <div class="mt-5">
                  <h4 class="card-title">{{ $t('sidebar.bulk') }}</h4>
                  <p class="card-text">
                    {{ $t('home_pods.bulk_upload') }}
                  </p>
                </div>
                <div class="pt-5 button-container">
                  <img class="img-bottom" src="../..//img/home_page/computer.png" alt="computer">
                  <b-button :to="{ name: 'content.bulk_upload', params: {} }" variant="danger" class="pl-4 pr-4">
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
            <b-col sm class="mb-2" v-if="firstSocietyCode">
              <b-card class="h-100 p-3 home-card content-card pt-5" v-if="firstSocietyCode">
                <div class="mt-5">
                  <h4 class="card-title">{{ $t('sidebar.content') }}</h4>
                  <p class="card-text">
                    {{ $t('home_pods.whatnow_edit') }}
                  </p>
                </div>
                <div class="pt-5 button-container">
                  <img class="img-bottom" src="../..//img/home_page/computer.png" alt="computer">
                  <b-button :to="{ name: 'content.whatnow', params: { countryCode: firstSocietyCode } }" variant="danger"
                            class="pl-4 pr-4">{{ $t('go') }}
                  </b-button>
                </div>
              </b-card>
            </b-col>

            <b-col sm class="mb-2">
              <b-card class="h-100 p-3 home-card api-card pt-5">
                <div class="mt-5">
                  <h4 class="card-title">{{ $t('sidebar.audit_log') }}</h4>
                  <p class="card-text">
                    {{ $t('home_pods.audit_log') }}
                  </p>
                </div>
                <div class="pt-5 button-container">
                  <img class="img-bottom" src="../..//img/home_page/computer.png" alt="computer">
                  <b-button :to="{ name: 'content.audit_log', params: {} }" variant="danger" class="pl-4 pr-4">{{
                      $t('go')
                    }}
                  </b-button>
                </div>
              </b-card>
            </b-col>

            <b-col sm class="mb-2">
              <b-card class="h-100 p-3 home-card content-card pt-5">
                <div class="mt-5">
                  <h4 class="card-title">{{ $t('sidebar.bulk') }}</h4>
                  <p class="card-text">
                    {{ $t('home_pods.bulk_upload') }}
                  </p>
                </div>
                <div class="pt-5 button-container">
                  <img class="img-bottom" src="../..//img/home_page/computer.png" alt="computer">
                  <b-button :to="{ name: 'content.bulk_upload', params: {} }" variant="danger" class="pl-4 pr-4">
                    {{ $t('go') }}
                  </b-button>
                </div>
              </b-card>
            </b-col>
          </b-row>
        </div>

        <div v-else-if="can(user, permissions.CONTENT_VIEW) && cannot(user, permissions.CONTENT_CREATE)">
          <b-row>
            <b-col sm class="mb-2" v-if="firstSocietyCode">
              <b-card class="h-100 p-3 home-card content-card pt-5" v-if="firstSocietyCode">
                <div class="mt-5">
                  <h4 class="card-title">{{ $t('sidebar.content') }}</h4>
                  <p class="card-text">
                    {{ $t('home_pods.whatnow_review') }}
                  </p>
                </div>
                <div class="pt-5 button-container">
                  <img class="img-bottom" src="../..//img/home_page/computer.png" alt="computer">
                  <b-button :to="{ name: 'content.whatnow', params: { countryCode: firstSocietyCode } }" variant="danger"
                            class="pl-4 pr-4">{{ $t('go') }}
                  </b-button>
                </div>
              </b-card>
            </b-col>
          </b-row>
        </div>
      </b-col>
    </b-row>
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
  metaInfo() {
    return {title: this.$t('home.home')}
  },
  data: () => ({
    title: window.config.appName,
    permissions: permissionsList
  }),
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
}

.img-bottom {
  width: 26%;
}
</style>
