<template>
  <b-container fluid>
    <page-banner>
      <b-col>
        <h1>{{ $t('content.audit_log.my_audit_log') }}</h1>
      </b-col>
      <b-col>
        <b-button class="float-right rtl-float-left mr-2 rtl-ml-2 new-btn" prop='link' href="/api/organisations/instructions/export" v-if="can(user, permissions.USERS_CREATE)" @click="$fireGTEvent($gtagEvents.DownloadAuditLogReport)">
          {{ $t('users.list.download_report') }}
        </b-button>
      </b-col>
    </page-banner>

     <b-row class="pb-2 px-4 pt-4 bg-white" align-v="center">
        <b-col cols="3">
          <p class="select-header" v-if="!apiUsers"> {{ $t('users.list.select_society') }}</p>
          <SelectHazardType class="bg-white" v-model="hazardTypeFilter" :hazardTypeList="filteredHazardsList"></SelectHazardType>
        </b-col>
        <b-col cols="3">
          <p class="select-header" v-if="!apiUsers"> {{ $t('users.list.select_society') }}</p>
          <v-select
            :dir="isLangRTL(locale) ? 'rtl' : 'ltr'"
            v-model="languageFilter"
            class="w-100 v-select-custom"
            :options="filteredLanguages"
            label="text" :disabled="filteredLanguages.length === 0"
            :placeholder="$t('content.whatnow.select_language')">
            <template slot="option" slot-scope="option">
              <div class="ml-2 rtl-mr-2 dropdown-option">
                {{ option.text }}
              </div>
            </template>
            <template slot="selected-option" slot-scope="option">
              <div class="ml-2 rtl-mr-2 dropdown-option">
                {{ option.text }}
              </div>
            </template>
          </v-select>
        </b-col>
        <b-col cols="3">
          <p class="select-header" v-if="!apiUsers"> {{ $t('users.list.select_society') }}</p>
          <selectSociety
            :selected.sync="selectedSoc"
            :staynull="true" />
        </b-col>
        <b-col cols="3">
          <b-button @click="clearFilters" :disabled="noFilters" class="float-right rtl-float-left new-btn">
            {{ $t('users.list.clear_filters') }}
          </b-button>
        </b-col>
      </b-row>

    <b-row class="pl-4 pr-4 pb-4 pt-4 bg-white">
      <b-col v-if="auditsAvailable">
        <table-loading :loading="fetchingAudits"></table-loading>
        <b-table hover
          :items="audits.data"
          :fields="fields"
          :perPage="audits.meta.per_page"
          :show-empty="false"
          no-local-sorting
          :sort-by.sync="orderBy"
          :sort-desc.sync="sortDesc">
          <template #cell(user)="data">
            <div v-if="data.item.user">
              <b-img v-if="data.item.user.user_profile.photo_url" :src="data.item.user.user_profile.photo_url" width="20" height="20" rounded="circle" alt="" role="presentation"></b-img>
              <span v-else><avatar
                :username="data.item.user.user_profile.first_name + ' ' + data.item.user.user_profile.last_name"
                :size="20" :customStyle="{display: 'inline-block'}"
                backgroundColor="#ff5607"></avatar></span>
              <span>{{ truncate(data.item.user.user_profile.first_name + ' ' + data.item.user.user_profile.last_name, 20) }}</span>
            </div>
            <span v-else>{{ $t('users.deleted') }}</span>
          </template>
          <template #cell(country_code)="data">
            {{ getSocietyByCode(data.item.country_code) }}
          </template>
          <template #cell(created_at)="data">
            {{ data.item.created_at | moment("MM/DD/YY HH:mm:ss") }}
          </template>
          <template #cell(actions)="data">
             <b-button v-if="data.item.entity_id" class="mb-1 new-btn" @click="viewChanges(data.item)" :to="{ name: '', params: {} }"> {{ $t('common.view_content') }} </b-button>
          </template>
        </b-table>
        <b-pagination

          v-if="audits.meta.total > audits.meta.per_page"
          size="md"
          :total-rows="audits.meta.total"
          v-model="currentPage"
          :per-page="audits.meta.per_page"
          :limit="10"
          align="center"></b-pagination>
        <p v-if="audits.data.length === 0">
          {{ $t('content.audit_log.empty') }}
        </p>
      </b-col>
      <b-col v-else>
        {{ $t('common.loading')}}
        <!-- Throw in a spooky boi here -->
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
import { languages } from 'countries-list'
import TableLoading from '~/components/global/TableLoading'
import PageBanner from '~/components/PageBanner'
import { mapGetters } from 'vuex'
import * as permissionsList from '../../store/permissions'
import Avatar from 'vue-avatar'
import SelectSociety from '~/pages/content/selectSociety'
import SelectHazardType from '~/pages/content/simpleHazardTypePicker'

const fetchHandler = {
  handler (val, oldVal) {
    if (val !== oldVal) {
      this.currentPage = 1
      this.fetchAudits()
    }
  },
  deep: true
}

export default {
  components: {
    TableLoading,
    PageBanner,
    Avatar,
    SelectHazardType,
    SelectSociety
  },
  props: ['apiAudits', 'userId'],
  data () {
    return {
      permissions: permissionsList,
      test: 'sgdfg',
      fields: [
                { key: 'user', sortable: true, tdClass: 'align-middle' },
                { key: 'action', sortable: true },
                { key: 'content', sortable: true },
                { key: 'language_code', sortable: true },
                { key: 'country_code', sortable: true, label: this.$t('content.audit_log.soc_label') },
                { key: 'created_at', sortable: true },
                { key: 'actions', sortable: false }
      ],
      currentPage: 1,
      fetchingAudits: false,
      languages,
      roles: null,
      roleFilter: null,
      orderBy: 'created_at',
      sortDesc: true,
      hazardTypeFilter: null,
      languageFilter: null,
      selectedSoc: null
    }
  },
  watch: {
    currentPage: {
      handler (val, oldVal) {
        if (val !== oldVal) {
          this.fetchAudits()
        }
      },
      deep: true
    },
    roleFilter: fetchHandler,
    languageFilter: fetchHandler,
    hazardTypeFilter: fetchHandler,
    orderBy: fetchHandler,
    sortDesc: fetchHandler,
    selectedSoc: {
      handler (val, oldVal) {
        this.setLocalStorage()
        this.fetchAudits()
      },
      deep: true
    }
  },
  mounted () {
    this.fetchOrganisations()
    this.fetchAudits()
    this.fetchAllHazardTypes()
  },
  metaInfo () {
    return { title: this.$t('content.audit_log.audit_log') }
  },
  methods: {
    clearFilters () {
      this.selectedSoc = null
      this.hazardTypeFilter = null
      this.languageFilter = null
    },
    async fetchAllHazardTypes () {
      try {
        await this.$store.dispatch('content/fetchHazardTypes')
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
      }
    },
    async fetchAudits () {
      this.fetchingAudits = true
      if (this.userId) {
        try {
          await this.$store.dispatch('audit/fetchUserAudits',
            {
              page: this.currentPage,
              filters: {
                content: this.hazardTypeFilter,
                country_code: this.selectedSoc ? this.selectedSoc.countryCode : null,
                language_code: this.languageFilter
              },
              orderBy: this.orderBy,
              sort: this.sortDesc ? 'desc' : 'asc',
              userId: this.userId
            })
        } catch (e) {
          this.$noty.error(this.$t('error_alert_text'))
        }
      } else {
        try {
          await this.$store.dispatch('audit/fetchAudits',
            {
              page: this.currentPage,
              filters: {
                content: this.hazardTypeFilter,
                country_code: this.selectedSoc ? this.selectedSoc.countryCode : null,
                language_code: this.languageFilter
              },
              orderBy: this.orderBy,
              sort: this.sortDesc ? 'desc' : 'asc'
            })
        } catch (e) {
          this.$noty.error(this.$t('error_alert_text'))
        }
      }
      this.fetchingAudits = false
    },
    async fetchOrganisations () {
      try {
        await this.$store.dispatch('content/fetchOrganisations')
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
      }
    },
    async viewChanges (item) {
      localStorage.setItem('soc', item.country_code)
      if (item.language_code) {
        localStorage.setItem('lang', item.language_code)
        this.$router.push({ name: 'content.editWhatnow', params: { whatnowId: item.entity_id, langCode: item.language_code }})
      } else {
        this.$router.push({ name: 'content.whatnow', params: { countryCode: item.country_code }})
      }
    },
    getSocietyByCode (code) {
      const soc = this.societies.find(soc => soc.countryCode === code)
      return soc ? soc.name : code
    },
    setLocalStorage () {
      if (this.selectedSoc) {
        localStorage.setItem('soc', this.selectedSoc.countryCode)
      } else {
        localStorage.removeItem('soc')
      }
    }
  },
  computed: {
    filteredHazardsList () {
      const alphabeticalHazardList = [...this.hazardsList].sort((a, b) => a.name.localeCompare(b.name))

      // 'other' hazard type should always be at the end of the list
      for (const key in alphabeticalHazardList) {
        alphabeticalHazardList[key].code == 'other' ? alphabeticalHazardList.push(alphabeticalHazardList.splice(key, 1)[0]) : 0
      }

      return [...alphabeticalHazardList]
    },
    filteredLanguages () {
      const languages = [{ value: null, text: this.$t('content.whatnow.select_language') }]

      for (const key in this.languages) {
        if (this.languages.hasOwnProperty(key)) {
          languages.push({
            value: key,
            text: `${this.languages[key].name}  (${this.languages[key].native} - ${key})`
          })
        }
      }

      return [...languages]
    },
    noFilters () {
      return this.selectedSoc === null && this.hazardTypeFilter === null && this.languageFilter == null
    },
    ...mapGetters({
      locale: 'lang/locale',
      user: 'auth/user',
      hazardsList: 'content/hazardsList',
      audits: 'audit/audits',
      auditsAvailable: 'audit/check',
      societies: 'content/organisations'
    })
  }

}
</script>
<style>
.v-select-custom {
  font-family: Poppins;
  div {
    background: #E9E9E9;
    font-family: Poppins;
    border: none;
    border-radius: 10px;
    padding: 2px;
  }
}
td {
  div {
    div {
      background: #F6333F!important;
      color: white!important;
    }
  }
}
.select-header {
  font-size: 1rem;
}
</style>
