<template>
  <b-container fluid>
    <!-- API Stats Page Banner -->
    <page-banner>
      <b-col class="mb-sm-4" sm>
        <h1>{{ $t('api_stats.page_title') }}</h1>
      </b-col>

      <b-col class="mb-4" sm>
        <v-select
          :dir="isLangRTL(locale) ? 'rtl' : 'ltr'"
          v-model="tableView"
          :clearable="false"
          class="w-100 v-select-custom ml-auto"
          :options="tableViewOptions"
          label="name">
          <template slot="option" slot-scope="option">
            <div class="ml-2 rtl-mr-2 dropdown-option">
              {{ option.name }}
            </div>
          </template>
          <template slot="selected-option" slot-scope="option">
            <div class="ml-2 rtl-mr-2 dropdown-option">
              {{ option.name }}
            </div>
          </template>
        </v-select>
      </b-col>

      <b-col cols="12">
        <div class="bg-white rounded-lg py-5 px-5">
          <b-row class="bg-grey pt-3 pb-3 mb-3 api-stats-filter-content">
              <b-col>
                <selectSociety v-model="selectedSoc" :societyList="filteredSocieties"></selectSociety>
                <selectRegion v-model="selectedRegion" :socCode="selectedSoc?.countryCode ?? null"></selectRegion>
                <b-form-datepicker :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }" v-model="selectedDate" :max="new Date()" class="mb-2"></b-form-datepicker>
                <v-select v-model="selectedLanguage" class="w-100 v-select-custom p-0" :options="filteredLanguages" label="name" :disabled="filteredLanguages.length === 0" :placeholder="$t('content.whatnow.select_language')">
                  <template slot="option" slot-scope="option">
                    {{ option.text }}
                  </template>
                  <template slot="selected-option" slot-scope="option">
                    {{ option.text }}
                  </template>
                </v-select>
                <SelectHazard v-model="selectedHazard" :hazardTypeList="filteredHazardsList"></SelectHazard>
              </b-col>
          </b-row>
          <b-row>
            <b-col class="mb-3 mb-lg-0" cols="12" lg="4">
              <stats-card class="bg-grey" :title="$t('api_stats.cumulative_cards.hits')" icon="profile-duo" :value="cumulativeData?.hits" />
            </b-col>
            <b-col class="mb-3 mb-lg-0" cols="12" lg="4">
              <stats-card class="bg-grey" :title="$t('api_stats.cumulative_cards.accounts')" icon="accounts" :value="cumulativeData?.applications" />
            </b-col>
            <b-col class="mb-3 mb-lg-0" cols="12" lg="4">
              <stats-card class="bg-grey" :title="$t('api_stats.cumulative_cards.est_impacted_users')" icon="speedometer" :value="cumulativeData?.estimatedUsers" />
            </b-col>
          </b-row>
        </div>
      </b-col>
    </page-banner>

    <!-- Table Filters -->
    <b-row class=" pt-3 pb-3 mb-3 api-stats-filter-content" align-v="center">
      <b-col cols="12" sm="5" md="4" lg="3">
        <label for="datepicker-min">{{ $t('api_stats.start_date') }}</label>
        <b-form-datepicker id="datepicker-min" v-model="fromDate" :max="toDate" class="mb-2"></b-form-datepicker>
      </b-col>
      <b-col cols="12" sm="5" md="4" lg="3">
        <label for="datepicker-max">{{ $t('api_stats.end_date') }}</label>
        <b-form-datepicker id="datepicker-max" v-model="toDate" :disabled="!fromDate" :min="fromDate" :max="today" class="mb-2"></b-form-datepicker>
      </b-col>
      <b-col class="ml-auto" cols="12" sm="2">
        <div class="d-flex justify-content-end">
          <b-button size="sm" class="btn-outline-primary mr-2 text-nowrap" @click="downloadStatsCSV">
            {{ $t('api_stats.download_data') }}
          </b-button>
          <b-button size="sm" class="btn-outline-primary text-nowrap" @click="clearAllFilters">
            {{ $t('api_stats.clear_filters') }}
          </b-button>
        </div>
      </b-col>
    </b-row>

    <!-- The API Stats Table -->
    <b-row class="pb-4 px-4 pt-3 bg-white">
      <b-col>
        <APIApplicationsTable v-if="tableView.value === 'applications'" :fromDate="fromDate" :toDate="toDate" />
        <APIEndpointsTable v-if="tableView.value === 'endpoints'" :fromDate="fromDate" :toDate="toDate" />
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
import axios from 'axios'
import querystring from 'querystring'
import { download } from '~/utils/export'
import { mapGetters } from 'vuex'
import PageBanner from '~/components/PageBanner'
import StatsCard from '~/pages/api_stats/components/StatsCard'
import APIApplicationsTable from '~/pages/api_stats/components/APIApplicationsTable'
import APIEndpointsTable from '~/pages/api_stats/components/APIEndpointsTable'
import { formatDate } from '~/utils/time'
import SelectSociety from '~/pages/content/simpleSocietyPicker'
import SelectRegion from '~/pages/content/regionPicker'
import { languages } from 'countries-list'
import SelectHazard from '~/pages/content/simpleHazardTypePicker'

export default {
  components: {
    PageBanner,
    StatsCard,
    APIApplicationsTable,
    APIEndpointsTable,
    SelectSociety,
    SelectRegion,
    SelectHazard
  },
  mounted () {
    this.fetchData()
  },
  watch: {
    selectedSoc: "fetchCumulativeUsageStats",
    selectedHazard: "fetchCumulativeUsageStats",
    selectedDate: "fetchCumulativeUsageStats",
    selectedRegion: "fetchCumulativeUsageStats",
    selectedLanguage: "fetchCumulativeUsageStats"
  },
  methods: {
    clearAllFilters() {
      this.selectedSoc = null;
      this.selectedRegion = null;
      this.selectedDate = null;
      this.selectedLanguage = null;
      this.selectedHazard = null;
      this.fromDate = formatDate(new Date((new Date()).getFullYear(), (new Date()).getMonth(), 1));
      this.toDate = formatDate(new Date());
      this.fetchCumulativeUsageStats();
    },
    fetchData () {
      this.fetchOrganisations()
      this.fetchAllHazardTypes()
      this.fetchCumulativeUsageStats()
    },
    async fetchOrganisations () {
      this.isFetchingLangs = true
      try {
        await this.$store.dispatch('content/fetchOrganisations')
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
      }
    },
    async fetchAllHazardTypes () {
      try {
        await this.$store.dispatch('content/fetchHazardTypes')
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
      }
    },
    async fetchCumulativeUsageStats () {
      this.isLoading = true

      try {
        const parameters = { society: this.selectedSoc ? this.selectedSoc.countryCode : null,
          hazard: this.selectedHazard ? this.selectedHazard.code : null,
          region: this.selectedRegion ? this.selectedRegion.id : null,
          date: this.selectedDate ? this.selectedDate.toString() : null,
          language: this.selectedLanguage ? this.selectedLanguage.value : null }
        await this.$store.dispatch('usage/fetchCumulativeUsage', parameters)
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
      } finally {
        this.isLoading = false
      }
    },
    async downloadStatsCSV () {
      const queryOptions = {
        fromDate: this.fromDate,
        toDate: this.toDate
      }

      try {
        // Download request must be authenticated
        const { data: responseArrayBuffer } = await axios.get(`/api/usage/export/${this.tableView.value}?${querystring.stringify(queryOptions)}`, {
          headers: {
            Accept: 'text/csv; charset=utf-8',
            'Content-Type': 'text/csv; charset=utf-8'
          },
          responseType: 'arraybuffer'
        })

        const data = new File([responseArrayBuffer], 'export', { type: 'text/csv' })

        // https://developer.mozilla.org/en-US/docs/Web/API/URL/createObjectURL
        const url = window.URL.createObjectURL(data)

        download(url)

        window.URL.revokeObjectURL(url)

        this.$fireGTEvent(this.$gtagEvents.DownloadAPIReport)
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
      }
    }
  },
  computed: {
    filteredLanguages () {
      const languages = []
      for (const key in this.languages) {
        if (this.languages.hasOwnProperty(key)) {
          languages.push({
            value: key,
            text: `${this.languages[key].name}  (${this.languages[key].native} - ${key})`
          })
        }
      }
      return languages
    },
    filteredHazardsList () {
      const alphabeticalHazardList = [...this.hazardsList].sort((a, b) => a.name.localeCompare(b.name))

      // 'other' hazard type should always be at the end of the list
      for (const key in alphabeticalHazardList) {
        alphabeticalHazardList[key].code == 'other' ? alphabeticalHazardList.push(alphabeticalHazardList.splice(key, 1)[0]) : 0
      }

      return [...alphabeticalHazardList]
    },
    filteredSocieties () {
      return this.societies
    },
    ...mapGetters({
      cumulativeData: 'usage/currentCumulativeData',
      locale: 'lang/locale',
      currentLanguages: 'content/currentLanguages',
      societies: 'content/organisations',
      hazardsList: 'content/hazardsList'
    })
  },
  data () {
    return {
      isLoading: true,
      // First day of the current month
      fromDate: formatDate(new Date((new Date()).getFullYear(), (new Date()).getMonth(), 1)),
      // Today
      toDate: formatDate(new Date()),
      today: formatDate(new Date()),
      tableView: {
        name: this.$t('api_stats.table_select.keys'),
        value: 'applications'
      },
      languages,
      selectedSoc: null,
      selectedHazard: null,
      selectedDate: null,
      selectedRegion: null,
      selectedLanguage: null,
      tableViewOptions: [
        {
          name: this.$t('api_stats.table_select.keys'),
          value: 'applications'
        },
        {
          name: this.$t('api_stats.table_select.routes'),
          value: 'endpoints'
        }]
    }
  }
}
</script>

<style scoped lang="scss">
@import '../../../sass/variables.scss';

:deep(.api-stats-filter-content){
  .styled-select.v-select .dropdown-option{
    overflow: hidden;
  }
  .styled-select.v-select .vs__selected{
    overflow: hidden;
  }
  .b-form-btn-label-control.form-control > .form-control{
    white-space: nowrap;
    line-height: 32px;
  }
  .v-select.styled-select.v-select div.vs__dropdown-toggle  {
    background: $bg-disabled;
  }
  .vs--disabled .vs__search{
    background: $bg-disabled;
    color: #ccc;
  }
}

.api-stats-filter-content{

  > div{
    display: grid;
    width: 100%;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    grid-gap: 6px;
  }

  select, .b-form-datepicker{
    background: $bg-disabled;
    border-radius: 10px;
    color: red;
    height: 45px;
  }

  .b-form-datepicker{
    font-size: 14px;
    line-height: 20px;
  }

}

</style>
