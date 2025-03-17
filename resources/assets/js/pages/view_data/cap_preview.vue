<template>
  <b-container fluid class="cap-preview">
    <page-banner :breadcrumbs="[{ name: 'view-alerts-code', params: {}, text: $t('alerts') }]">
      <b-col>
        <h1>{{ $t('view_data.alert_detail') }}</h1>
      </b-col>
    </page-banner>

    <b-row class="pl-3 pr-0 bg-white cap-details" v-if="alert">
      <b-col cols="5" class="pt-4">
          <b-row align-v="center">
            <b-col cols="auto">
              <b-row align-v="center" class="mx-0">
                <b-img v-bind:class="`alert-icon alert-icon-${alert.info.severity.toLowerCase()}`" :src="hazardIcon(alert.info.event, this.$store)" height="72px" class="mr-3 rtl-ml-3"></b-img>
                <h3 class="mb-0 text-capitalize">{{ alert.info.event }} <br/><span>{{ alert.info.area.area_desc }}</span></h3>
              </b-row>
            </b-col>
          </b-row>
          <b-row class="pt-4 pb-3">
            <b-col>
              <b-card class="p-2">
                <p class="mb-0">ALERT:</p>
                <small><b>{{ alert.info.headline }}</b></small><br /><br/>
                <small>{{ alert.info.description }}</small>
              </b-card>
            </b-col>
          </b-row>
          <b-row class="px-3 pt-3">
            <b-col cols="4">
              <div class="cap-data-item">
                <h6 class="mb-1">{{ $t('view_data.cap.type')}}:</h6>
                <small><b>{{ alert.msg_type }}</b></small>
              </div>
            </b-col>
            <b-col cols="4">
              <div class="cap-data-item">
                <h6 class="mb-1">{{ $t('view_data.cap.status')}}:</h6>
                <small><b>{{ alert.status }}</b></small>
              </div>
            </b-col>
          <b-col cols="12"> <hr /> </b-col>
          </b-row>
          <b-row class="px-3">
            <b-col cols="4">
              <div class="cap-data-item">
                <h6 class="mb-1">{{ $t('view_data.cap.urgency')}}:</h6>
                <small><b>{{ alert.info.urgency }}</b></small>
              </div>
            </b-col>
            <b-col cols="4">
              <div class="cap-data-item">
                <h6 class="mb-1">{{ $t('view_data.cap.severity')}}:</h6>
                <small><b>{{ alert.info.severity }}</b></small>
              </div>
            </b-col>
            <b-col cols="4">
              <div class="cap-data-item">
                <h6 class="mb-1">{{ $t('view_data.cap.certainty')}}:</h6>
                <small><b>{{ alert.info.certainty }}</b></small>
              </div>
            </b-col>
            <b-col cols="12"> <hr /> </b-col>
          </b-row>
          <b-row class="px-3">
            <b-col cols="4">
              <div class="cap-data-item">
                <h6 class="mb-1">{{ $t('view_data.cap.sent')}}:</h6>
                <small><b>{{ alert.sent | moment("MM/DD/YY HH:mm:ss Z") }}</b></small>
              </div>
            </b-col>
            <b-col cols="4">
              <div class="cap-data-item">
                <h6 class="mb-1">{{ $t('view_data.cap.effective')}}:</h6>
                <small><b>{{ alert.info.effective | moment("MM/DD/YY HH:mm:ss Z") }}</b></small>
              </div>
            </b-col>
          </b-row>
          <b-row class="pt-5 pb-0 px-3">
            <b-col cols="4">
              <div class="cap-data-item">
                <h6 class="mb-1">{{ $t('view_data.cap.onset')}}:</h6>
                <small><b>{{ alert.info.onset | moment("MM/DD/YY HH:mm:ss Z") }}</b></small>
              </div>
            </b-col>
            <b-col cols="4">
              <div class="cap-data-item">
                <h6 class="mb-1">{{ $t('view_data.cap.expires')}}:</h6>
                <small><b>{{ alert.info.expires | moment("MM/DD/YY HH:mm:ss Z") }}</b></small>
              </div>
            </b-col>
            <b-col cols="12"> <hr /> </b-col>
          </b-row>
          <b-row class="px-3">
            <b-col>
              <p class="alert-org-name">{{ $t('view_data.cap.alert_from') }} {{ alert.organisation.name }} {{ $t('view_data.cap.feed') }}</p>
              <p v-if="societyUrl">More info: {{ societyUrl }}</p>
            </b-col>
          </b-row>
      </b-col>
      <b-col cols="7" class="pr-0 pl-0">
        <div class="cap-map-overlay"></div>
        <google-map v-if="geoJson" name="gmap" :geoJson="geoJson"></google-map>
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
import axios from 'axios'
import Spooky from '~/components/global/Spooky'
import PageBanner from '~/components/PageBanner'
import { mapGetters } from 'vuex'

export default {
  props: ['identifier'],
  components: {
    Spooky,
    PageBanner
  },
  loading: false,
  data () {
    return {
      alert: null,
      loadingContent: false,
      mapCenter: { lat: 0, lng: 0 },
      xmlData: null,
      geoJson: null
    }
  },
  metaInfo () {
    return { title: `${this.$t('view_data.alert_preview')}` }
  },
  mounted () {
    this.fetchAlert()
  },
  methods: {
    async fetchAlert () {
      this.loadingContent = true
      const { data } = await axios.get(`/api/alerts/${this.identifier}/`)
      this.alert = data.data
      this.loadingContent = false
      this.geoJson = this.alert.info.area.polygon
      if (!this.societyUrl) this.$store.dispatch('content/fetchOrganisationSingle', this.alert.organisation.country)
    },
  },
  computed: {
    societyUrl () {
      if (this.alert) {
        if (this.societies.length) {
          let society = this.societies.find(s => s.countryCode == this.alert.organisation.country)
          if (society) {
            return society.url
          }
        }
      }
      return null;
    },
    ...mapGetters({
      societies: 'content/organisations'
    })
  }
}
</script>
