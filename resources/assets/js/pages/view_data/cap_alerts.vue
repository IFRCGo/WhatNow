<template>
  <b-container fluid>
    <b-row class="pb-0 px-4 py-4 bg-dark text-light">
      <b-col>
        <selectSociety :selected.sync="selectedSoc" :dontfilter="true" :countryCode="countryCode"></selectSociety>
      </b-col>
    </b-row>
    <page-banner>
      <b-col>
        <b-col>
          <h1 v-if="selectedSoc !== null && !loadingContent">{{ selectedSoc.name }}<br />
          {{ $t('view_data.cap.alert_feed') }}</h1>
          <h1 v-else>
            {{ $t('view_data.cap.fetching_feed') }}
          </h1>
        </b-col>
      </b-col>
    </page-banner>
    <b-row class="feed-body px-4 py-4">
      <b-col>
        <transition-group name="fade" v-if="alerts.length > 0">
          <b-row class="whatnow-row mt-2 mb-4" v-if="!loadingContent" v-for="alert in alerts" :key="alert.identifier">
            <b-col>
              <b-card :class="`hazard-instruction-card hazard-instruction-card-${alert.info.severity.toLowerCase()} px-2`">
                <b-row align-v="end">
                  <b-col cols="auto">
                    <b-img :src="hazardIcon(alert.info.event)" class="rounded-circle" width="60" height="60"></b-img>
                  </b-col>
                  <b-col class="pl-0">
                    <h3 class="mb-1 text-capitalize">{{ alert.info.event }}</h3>
                    <p class="mb-0">
                      {{ alert.info.effective | moment("MM/DD/YY HH:mm:ss Z") }} {{ $t('view_data.cap.through')}} {{ alert.info.expires | moment("MM/DD/YY HH:mm:ss Z") }}
                    </p>
                  </b-col>
                  <b-col cols="auto" v-bind:style="{alignSelf: 'center'}">
                    <b-button class="text-uppercase" variant="outline-secondary"
                    :to="{ name: 'preview-cap', params: { identifier: alert.identifier } }">
                      {{ $t('view_data.more') }}
                    </b-button>
                  </b-col>
                </b-row>
                <hr />
                <b-row>
                  <b-col>
                    <p class="mt-0 mb-1">
                      {{ $t('view_data.area_affected') }}: {{ alert.info.area.area_desc }}
                    </p>
                    <p class="text-secondary my-0">
                      {{ $t('view_data.details') }}: {{ truncate(alert.info.description, 50) }}
                    </p>
                  </b-col>
                </b-row>
              </b-card>
            </b-col>
          </b-row>
        </transition-group>
        <b-row v-else-if="!loadingContent">
          <b-col>
            <h3>{{ $t('view_data.cap.empty') }}</h3>
          </b-col>
        </b-row>
        <transition-group name="fade" v-if="loadingContent">
          <b-row class="mt-2 mb-4" v-for="n in 5" :key="n">
            <b-col>
              <b-card :class="`hazard-instruction-card hazard-instruction-card-extreme px-2`">
                <b-row>
                  <b-col cols="auto">
                    <spooky width="60px" height="60px" radius="50%"></spooky>
                  </b-col>
                  <b-col>
                    <spooky width="30%" height="30px" class="mb-2"></spooky>
                    <spooky width="65%" height="15px"></spooky>
                  </b-col>
                  <b-col cols="auto">
                    <spooky width="10%" height="25px"></spooky>
                  </b-col>
                </b-row>
                <hr />
                <b-row>
                  <b-col>
                    <spooky width="10%" height="10px"></spooky>
                  </b-col>
                </b-row>
              </b-card>
            </b-col>
          </b-row>
        </transition-group>
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
import axios from 'axios'
import SelectSociety from '~/pages/content/selectSociety'
import Spooky from '~/components/global/Spooky'
import PageBanner from '~/components/PageBanner'

export default {
  props: {
    countryCode: {
      type: String,
      default: 'USA'
    }
  },
  components: {
    SelectSociety,
    Spooky,
    PageBanner
  },
  metaInfo () {
    return { title: `${this.countryCode} ${this.$t('view_data.alerts')}` }
  },
  loading: false,
  data () {
    return {
      alerts: [],
      selectedSoc: null,
      loadingContent: false,
      selectedLanguage: 'en'
    }
  },
  mounted () {
    this.fetchResults()
  },
  watch: {
    selectedSoc: {
      handler (val, oldVal) {
        if (val) {
          this.$router.push({ name: 'view-alerts-code', params: { countryCode: val.countryCode } })
        }
      },
      deep: true
    },
    '$route' (to, from) {
      this.fetchResults()
    }
  },
  methods: {
    async fetchResults() {
      this.loadingContent = true
      const { data } = await axios.get(`/api/organisations/${this.countryCode}/alerts`)
      this.alerts = data.data
      this.loadingContent = false
    }
  }
}
</script>
