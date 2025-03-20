<template>
  <b-container fluid>
    <page-banner>
      <b-col>
        <h1 class="sec-title">{{ $t('content.audit_log.my_audit_log') }}</h1>
        <h6>{{ $t('content.audit_log.subtitle') }}</h6>
      </b-col>
      <b-col>
        <b-button class="float-right rtl-float-left rtl-ml-2 btn-outline-primary" prop='link' href="/api/organisations/instructions/export" v-if="can(user, permissions.USERS_CREATE)" @click="$fireGTEvent($gtagEvents.DownloadAuditLogReport)">
          {{ $t('users.list.download_report') }}
        </b-button>
      </b-col>
    </page-banner>

    <b-row class="pb-2 px-4 pt-4 bg-white" align-v="center">
      <b-col cols="3">
        <SelectHazardType class="bg-white" v-model="hazardTypeFilter" :hazardTypeList="filteredHazardsList"></SelectHazardType>
      </b-col>
      <b-col cols="3">
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
        <selectSociety
          :selected.sync="selectedSoc"
          :staynull="true" />
      </b-col>
      <b-col cols="3">
        <b-button @click="clearFilters" :disabled="noFilters" class="btn-outline-primary float-right rtl-float-left">
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
                 :sort-desc.sync="sortDesc"
                 @sort-changed="onSortChanged">
          <template #cell(user)="data">
            <div v-if="data.item.user">
              <b-img v-if="data.item.user.user_profile.photo_url" :src="data.item.user.user_profile.photo_url" width="20" height="20" rounded="circle" alt="" role="presentation"></b-img>
              <span v-else>
                <avatar
                :username="data.item.user.user_profile.first_name + ' ' + data.item.user.user_profile.last_name"
                  :size="30"
                  :customStyle="{display: 'inline-block'}"
                  :backgroundColor="'#F6333F'"
                  :color="'#FFFFFF'"
                ></avatar>
              </span>
              <span>{{ truncate(data.item.user.user_profile.first_name + ' ' + data.item.user.user_profile.last_name, 20) }}</span>
            </div>
            <span v-else>{{ $t('users.deleted') }}</span>
          </template>
          <template #cell(country_code)="data">
            {{ getSocietyByCode(data.item.country_code) }}
          </template>
          <template #cell(language_code)="data">
            {{ data.item.language_code }}
          </template>
          <template #cell(content)="data">
            {{ data.item.content }}
          </template>
          <template #cell(action)="data">
            <div class="d-flex justify-content-between align-items-center">
              <b-badge v-if="data.item.action === 'Created content'" class="custom-badge custom-badge-hazard-create">
                <span>{{ $t('badges.hazardCreate') }}</span>
                <span class="small-date">{{ data.item.created_at | moment("MM-DD-YY HH:mm") }}</span>
              </b-badge>
              <b-badge v-else-if="data.item.action === 'Published a content translation'" class="custom-badge custom-badge-published">
                <span>{{ $t('badges.published') }}</span>
                <span class="small-date">{{ data.item.created_at | moment("MM-DD-YY HH:mm") }}</span>
              </b-badge>
              <b-badge v-else-if="data.item.action === 'Updated content'" class="custom-badge custom-badge-draft">
                <span>{{ $t('badges.draft') }}</span>
                <span class="small-date">{{ data.item.created_at | moment("MM-DD-YY HH:mm") }}</span>
              </b-badge>
              <b-badge v-else-if="data.item.action === 'Updated content via import'" class="custom-badge custom-badge-bulk-upload-draft">
                <span>{{ $t('badges.bulkUploadDraft') }}</span>
                <span class="small-date">{{ data.item.created_at | moment("MM-DD-YY HH:mm") }}</span>
              </b-badge>
              <span v-else>{{ data.item.action }}</span>
            </div>
            <span class="d-none">{{ data.item.created_at }}</span>
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
import debounce from 'lodash/debounce'

const fetchHandler = debounce(function(val, oldVal) {
  if (val !== oldVal) {
    this.currentPage = 1;
    this.fetchAudits();
  }
}, 300);

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
      fields: [
        { key: 'user', tdClass: 'align-middle' },
        { key: 'country_code', label: this.$t('content.audit_log.soc_label') },
        { key: 'language_code' },
        { key: 'content' },
        { key: 'action', sortable: true, sortBy: 'created_at' }

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
      handler: debounce(function(val, oldVal) {
        this.setLocalStorage();
        this.fetchAudits();
      }, 300),
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
    async fetchAudits() {
      this.fetchingAudits = true
      const filters = {
        content: this.hazardTypeFilter,
        country_code: this.selectedSoc ? this.selectedSoc.countryCode : null,
        language_code: this.languageFilter ? this.languageFilter.value : null
      }

      const params = {
        page: this.currentPage,
        filters,
        orderBy: this.orderBy,
        sort: this.sortDesc ? 'desc' : 'asc'
      }
      try {
        if (this.userId) {
          await this.$store.dispatch('audit/fetchUserAudits', { ...params, userId: this.userId })
        } else {
          await this.$store.dispatch('audit/fetchAudits', params)
        }
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
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
    onSortChanged(ctx) {
      if (ctx.sortBy === 'action') {
        this.orderBy = 'created_at';
      } else {
        this.orderBy = ctx.sortBy;
      }
      this.sortDesc = ctx.sortDesc;
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
<style scoped>
.custom-badge {
  width: 100%;
  height: 30px;
  border-radius: 15px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 8px;
  border: 2px solid;
  font-size: 16px;

}
.small-date {
  font-size: 11px;
}
.custom-badge-published {
  background: #E6FEDF;
  border-color: #21A656;
  color: #21A656;
}

.custom-badge-hazard-create {
  background: #FEDEEC;
  border-color: #F6333F;
  color: #F6333F;
}

.custom-badge-bulk-upload-draft {
  background: #C8E4F7;
  border-color: #4288B8;
  color: #4288B8;
}

.custom-badge-draft {
  background: #FFF8CA;
  border-color: #E56A2D;
  color: #E56A2D;
}

btn-outline-primary.disabled, .btn-outline-primary:disabled {
  color: white;
  background: grey;
}

</style>
