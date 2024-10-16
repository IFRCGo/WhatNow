  <template>
    <b-container fluid>
      <b-row align-h="between" align-v="center" class="pl-4 pr-4 mb-1">
        <b-col md="12" xl="6">
          <h1>Create app</h1>
        </b-col>
      </b-row>

      <b-row class="mt-4 pl-4 pr-4 pb-4 pt-4 bg-white">
        <b-col>

          <b-alert show>
            <h2>{{ $t('common.greeting') }} {{ user.data.user_profile.first_name }}{{$t('banners.get_started_create')}}</h2>
            <p>
              {{ $t('banners.get_started_info') }}
            </p>

            <b-button size="sm" variant="primary">
              Create an app
            </b-button>
          </b-alert>

          <b-card title="0 Apps">
            <b-row>
              <b-col md="4" class="mb-3">
                  <b-card>
                    App
                  </b-card>
                </b-col>

                <b-col md="4" class="mb-3">
                   <b-card>
                     App
                   </b-card>
                 </b-col>

                 <b-col md="4" class="mb-3">
                    <b-card>
                      App
                    </b-card>
                  </b-col>
              </b-row>
          </b-card>
        </b-col>
      </b-row>
    </b-container>
  </template>

<script>
import { mapGetters } from 'vuex'

export default {
  metaInfo () {
    return { title: this.$t('home.home') }
  },
  data: () => ({
    title: window.config.appName
  }),
  computed: mapGetters({
    user: 'auth/user'
  })
}
</script>
