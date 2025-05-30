<template>
  <b-container fluid>
    <page-banner>
      <b-col sm>
        <h1 class="sec-title">{{ $t('users.list.manage') }}</h1>
      </b-col>
      <b-col sm>
        <b-button class="float-right rtl-float-left btn-outline-primary" :to="{ name:'users.new' }" v-if="can(user, permissions.USERS_CREATE) && !apiUsers">
          {{ $t('users.list.create') }}
        </b-button>
      </b-col>
    </page-banner>
    <b-row class="pb-2 px-4 pt-4 bg-white" align-v="center">
      <b-col cols="12" md="6" lg="3" xl="2">
        <p class="select-header"> {{ $t('users.list.select_status') }}</p>
        <v-select
          v-model="activatedFilter"
          class="v-select-custom"
          :options="[
        { name: 'Status', value: null },
        { name: 'Activated', value: 1 },
        { name: 'Deactivated', value: 0 }
      ]"
          label="name"
          :reduce="option => option.value"
          placeholder="Select Status">
        </v-select>
      </b-col>
      <b-col cols="12" md="6" lg="3" xl="2" v-if="!apiUsers">
        <p class="select-header"> {{ $t('users.list.select_role') }}</p>
        <v-select
          v-model="roleFilter"
          class="v-select-custom"
          :options="roleOptions"
          label="name"
          :reduce="option => option.id"
          placeholder="Select Role">
        </v-select>
      </b-col>
      <b-col cols="12" md="6" lg="3" xl="2" v-if="apiUsers">
        <p class="select-header"> {{ $t('users.list.select_country') }}</p>
        <v-select
          v-model="countryFilter"
          class="v-select-custom"
          :options="countryList"
          label="name"
          :reduce="option => option.id"
          placeholder="Select Country">
        </v-select>
      </b-col>
      <b-col cols="12" md="6" lg="4" xl="3" v-if="apiUsers">
        <p class="select-header"> {{ $t('users.list.select_terms') }}</p>
        <v-select
          v-model="termsFilter"
          class="v-select-custom text-nowrap"
          :options="termsList"
          label="version"
          :reduce="option => option.version"
          placeholder="Select Terms">
        </v-select>
      </b-col>
      <b-col cols="12" md="6" lg="3" xl="2">
        <p class="select-header" v-if="!apiUsers"> {{ $t('users.list.select_society') }}</p>
        <selectSociety
          class="float-right"
          :selected.sync="selectedSoc"
          :staynull="true"
          v-if="!apiUsers"/>
      </b-col>
      <b-col class="text-right">
        <b-button @click="clearFilters" :disabled="noFilters" class="btn-outline-primary clear-filter-btn">
          {{ $t('users.list.clear_filters') }}
        </b-button>
      </b-col>
    </b-row>
    <b-row class="pb-4 pt-3 bg-white">
      <b-col v-if="usersAvailable" class="pl-0 pr-0">
        <table-loading :loading="fetchingUsers"></table-loading>
        <div class="table-responsive users-list-table-view">
          <b-table hover
                   :items="users.data"
                   :fields="fields"
                   :perPage="users.meta.per_page"
                   :show-empty="false"
                   no-local-sorting
                   :sort-by.sync="orderBy"
                   :sort-desc.sync="sortDesc">
            <!-- A virtual column -->
            <template #cell(first_name)="data">
              {{ data.item.user_profile?.first_name }}
            </template>
            <template #cell(last_name)="data">
              {{ data.item.user_profile?.last_name }}
            </template>
            <template #cell(country_code)="data">
              {{ getLocationByCode(data.item.user_profile?.country_code) }}
            </template>
            <template #cell(role)="data">
              {{ data.item.role.name }}
            </template>
            <template #cell(created_at)="data">
              {{ data.item.created_at | moment("MM/DD/YY") }}
            </template>
            <template #cell(last_logged_in_at)="data">
              {{ data.item.last_logged_in_at | moment("MM/DD/YY") }}
            </template>
            <template #cell(society)="data">
              <span v-if="data.item.organisations.length === 1 ">{{ getSocietyByCode(data.item.organisations[0]) }}</span>
              <span v-else-if="data.item.organisations.length > 1">{{ data.item.organisations.length }} {{ $t('users.list.societies')}}</span>
              <span v-else-if="data.item.role && data.item.role.super">{{ $t('all')}} {{ $t('users.list.societies')}}</span>
              <span v-else-if="data.item.role && data.item.role.permissions.find(perm => perm.name === permissions.ALL_ORGANISATIONS)">{{ $t('all')}} {{ $t('users.list.societies')}}</span>
            </template>
            <template #cell(organisation)="data">
              {{ data.item.user_profile?.organisation }}
            </template>
            <template #cell(industry_type)="data">
              {{ data.item.user_profile?.industry_type }}
            </template>
            <template #cell(terms_version)="data">
              {{ data.item.user_profile?.terms_version }}
            </template>
            <template #cell(profile_pic)="data">
              <div>
                <b-img v-if="data.item.user_profile?.photo_url" :src="data.item.user_profile.photo_url" width="35" height="35" rounded="circle" alt="" role="presentation"></b-img>
                <avatar v-else :username="data.item.user_profile.first_name + ' ' + data.item.user_profile.last_name" :size="35" class="custom-avatar" />
              </div>
            </template>
            <template #cell(actions)="data">
            <div class="d-flex flex-column">
              <div class="mb-1">
                <b-button class="btn-outline-primary small-text mb-1" :to="{ name: 'users.edit', params: { id: data.item.id, isApiUser: apiUsers } }" v-if="can(user, permissions.USERS_EDIT)">
                  {{$t('common.edit')}}
                </b-button>
                <b-button class="btn-outline-primary small-text mb-1" @click="toggleDeactivate(data.item)" v-if="can(user, permissions.USERS_DEACTIVATE) && can(user, permissions.USERS_REACTIVATE)">
                  {{ data.item.activated ? $t('common.deactivate') : $t('common.activate') }}
                </b-button>
                <b-button class="btn-outline-primary small-text mb-1 text-nowrap" @click="sendResetPasswordEmail(data.item.email)" v-if="can(user, permissions.USERS_EDIT)">
                  {{$t('common.reset_password')}}
                </b-button>
              </div>
            </div>
          </template>
          </b-table>
        </div>
        <b-pagination
          v-if="users.meta.total > users.meta.per_page"
          pills
          :total-rows="users.meta.total"
          v-model="currentPage"
          :per-page="users.meta.per_page"
          :limit="10"
          class="pagination"
          align="center">
        </b-pagination>
        <p v-if="users.data.length === 0">
          {{ $t('users.list.empty') }}
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
import swal from 'sweetalert2'
import axios from 'axios'
import TableLoading from '~/components/global/TableLoading'
import { mapGetters } from 'vuex'
import Avatar from 'vue-avatar'
import * as permissionsList from '../../store/permissions'
import PageBanner from '~/components/PageBanner'
import SelectSociety from '~/pages/content/selectSociety'

const adminFields = [
  { key: 'profile_pic', label: ' ', tdClass: 'align-middle' },
  { key: 'first_name', sortable: true, tdClass: 'align-middle' },
  { key: 'last_name', sortable: true, tdClass: 'align-middle' },
  { key: 'role', sortable: false, tdClass: 'align-middle' },
  { key: 'society', sortable: false, tdClass: 'align-middle' },
  { key: 'email', sortable: false, tdClass: 'align-middle' },
  { key: 'created_at', sortable: true, tdClass: 'align-middle' },
  { key: 'last_logged_in_at', sortable: true, tdClass: 'align-middle', label: 'Last Active' },
  { key: 'terms_version', sortable: false, tdClass: 'align-middle' },
  'actions'
]

const apiUserFields = [
  { key: 'profile_pic', label: ' ', tdClass: 'align-middle', thClass: 'small-cell align-middle' },
  { key: 'first_name', sortable: true, tdClass: 'align-middle', thClass: 'align-middle' },
  { key: 'last_name', sortable: true, tdClass: 'align-middle', thClass: 'align-middle' },
  { key: 'country_code', sortable: false, tdClass: 'align-middle', thClass: 'align-middle', label: 'Location' },
  { key: 'organisation', sortable: false, tdClass: 'align-middle', thClass: 'align-middle' },
  { key: 'industry_type', sortable: false, tdClass: 'align-middle', thClass: 'align-middle' },
  { key: 'email', sortable: false, tdClass: 'align-middle break-all', thClass: 'align-middle ' },
  { key: 'created_at', sortable: true, tdClass: 'align-middle', thClass: 'align-middle' },
  { key: 'last_logged_in_at', sortable: true, tdClass: 'align-middle', thClass: 'align-middle', label: 'Last Active' },
  { key: 'terms_version', sortable: false, tdClass: 'align-middle', thClass: 'small-cell align-middle' },
  { key: 'actions', label: 'Actions', thClass: 'small-cell align-middle' }
]

const fetchHandler = {
  handler (val, oldVal) {
    if (val !== oldVal) {
      this.currentPage = 1
      this.fetchUsers()
    }
  },
  deep: true
}

let termsDefault = 'Terms and Conditions'

export default {
  components: {
    TableLoading,
    Avatar,
    PageBanner,
    SelectSociety
  },
  props: ['apiUsers'],
  data () {
    return {
      permissions: permissionsList,
      fetchingUsers: false,
      roles: null,
      locationOptions: null,
      countries: require('country-list')()
    }
  },
  watch: {
    currentPage: {
      handler (val, oldVal) {
        if (val !== oldVal) {
          this.fetchUsers()
        }
      },
      deep: true
    },
    activatedFilter: fetchHandler,
    roleFilter: fetchHandler,
    countryFilter: fetchHandler,
    termsFilter: fetchHandler,
    orderBy: fetchHandler,
    sortDesc: fetchHandler,
    selectedSoc: {
      handler (val, oldVal) {
        if (!this.apiUsers) {
          this.setLocalStorage()
          this.fetchUsers()
        }
      },
      deep: true
    }
  },
  mounted () {
    this.fetchOrganisations()
    this.fetchUsers()
    this.fetchTerms()
  },
  metaInfo () {
    return { title: this.$t('users.list.manage') }
  },
  methods: {
    clearFilters () {
      this.activatedFilter = null
      this.roleFilter = null
      this.countryFilter = null
      this.selectedSoc = null
      this.termsFilter = termsDefault
    },
    getSocietyByCode (code) {
      const soc = this.societies.find(soc => soc.countryCode === code)
      return soc ? soc.name : code
    },
    getLocationByCode (code) {
      if (!code || !this.countries) {
        return ""
      }
      const name = this.countries.getName(code)
      return name ? name : code
    },
    setLocalStorage () {
      if (this.selectedSoc){
        localStorage.setItem('soc', this.selectedSoc.countryCode)
      } else {
        localStorage.removeItem('soc')
      }
    },
    toggleDeactivate (item) {
      const action = item.activated ? this.$t('common.deactivate') : this.$t('common.activate')
      const apiUrl = item.activated ? 'deactivate' : 'reactivate'

      swal({
        title: this.$t('common.are_you_sure'),
        text: `${action} ${item.user_profile.first_name} ${item.user_profile.last_name}`,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: action
      }).then((result) => {
        item.activated = !item.activated
        axios.post(`/api/users/${item.id}/${apiUrl}`)
      }).catch(swal.noop)
    },
    async fetchOrganisations () {
      await this.$store.dispatch('content/fetchOrganisations')
    },
    async fetchUsers () {
      this.fetchingUsers = true
      await this.fetchRoles()

      let apiUserRole = this.roleOptions.find(role => role.name === 'API User')
      let filterRoleId = this.roleFilter
      if (filterRoleId === null) {
        filterRoleId = this.apiUsers ? apiUserRole.id : null
      }

      if (!apiUserRole && this.apiUsers) {
        console.warn('Could not find API User in the role list')
      }

      try {
        await this.$store.dispatch('users/fetchUsers',
          { page: this.currentPage,
            filters: {
              activated: this.activatedFilter,
              role: filterRoleId,
              society: this.selectedSoc ? this.selectedSoc.countryCode : null,
              country_code: this.countryFilter,
              terms_version: this.termsFilter === termsDefault ? null : this.termsFilter
            },
            admin: !this.apiUsers && filterRoleId === null,
            orderBy: this.orderBy,
            sort: this.sortDesc ? 'desc' : 'asc'
          })
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
      }

      this.fetchingUsers = false
    },
    async fetchRoles () {
      this.roles = this.roleOptions
      await this.$store.dispatch('users/fetchRoles')
      this.roles = this.roleOptions
      this.roles[0].name = "All roles"
    },
    fetchTerms () {
      try {
        this.$store.dispatch('terms/fetchAllTerms')
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
      }
    },
    async sendResetPasswordEmail(email) {
      try {
        this.fetchingUsers = true
        const { data } = await axios.post('/api/admin/password/email', { email })
        this.$noty.success(this.$t('users.list.reset_password_email_sent'))
      } catch (error) {
        this.$noty.error(this.$t('error_alert_text'))
      } finally {
        this.fetchingUsers = false
      }
    }
  },
  computed: {
    currentPage: {
      set: function(newVal) {
        this.$store.dispatch('users/setCurrentPage', newVal)
      },
      get: function() {
        return this.$store.state.users.currentPage
      }
    },
    orderBy: {
      set: function(newVal) {
        this.$store.dispatch('users/setOrderBy', newVal)
      },
      get: function() {
        return this.$store.state.users.orderBy
      }
    },
    sortDesc: {
      set: function(newVal) {
        this.$store.dispatch('users/setSortDesc', newVal)
      },
      get: function() {
        return this.$store.state.users.sortDesc
      }
    },
    activatedFilter: {
      set: function(newVal) {
        this.$store.dispatch('users/setFilter', { activatedFilter: newVal })
      },
      get: function() {
        return this.$store.state.users.filters.activatedFilter
      }
    },
    roleFilter: {
      set: function(newVal) {
        this.$store.dispatch('users/setFilter', { roleFilter: newVal })
      },
      get: function() {
        return this.$store.state.users.filters.roleFilter
      }
    },
    countryFilter: {
      set: function(newVal) {
        this.$store.dispatch('users/setFilter', { countryFilter: newVal })
      },
      get: function() {
        return this.$store.state.users.filters.countryFilter
      }
    },
    selectedSoc: {
      set: function(newVal) {
        this.$store.dispatch('users/setFilter', { selectedSoc: newVal })
      },
      get: function() {
        return this.$store.state.users.filters.selectedSoc
      }
    },
    termsFilter: {
      set: function(newVal) {
        this.$store.dispatch('users/setFilter', { termsFilter: newVal })
      },
      get: function() {
        return this.$store.state.users.filters.termsFilter
      }
    },
    fields: function() {
      return this.apiUsers ? Object.assign([], apiUserFields) : Object.assign([], adminFields)
    },
    countryList: function() {
      return [{
        code: null,
        name: 'Country'
      }, ...this.countries.getData()]
    },
    termsList: function() {
      return [{
        version: termsDefault
      }, ...this.terms || []]
    },
    noFilters: function() {
      return this.activatedFilter === null &&
        this.roleFilter === null &&
        this.countryFilter === null &&
        this.selectedSoc === null &&
        this.termsFilter === termsDefault
    },
    ...mapGetters({
      user: 'auth/user',
      users: 'users/users',
      usersAvailable: 'users/check',
      roleOptions: 'users/roles',
      rolesEmpty: 'users/rolesEmpty',
      societies: 'content/organisations',
      terms: 'terms/allTerms'
    })
  }
}
</script>
<style scoped>
  .select-header {
    font-size: 1rem;
  }
  .small-text {
    font-size: 0.8rem;
  }
  .custom-avatar {
    background-color: #F6333F !important;
    color: #FFFFFF !important;
  }

  .clear-filter-btn {
    margin-top: 2.5rem;
  }
  btn-outline-primary.disabled, .btn-outline-primary:disabled {
    color: white;
    background: grey;
  }
  .text-nowrap {
    white-space: nowrap;
  }

</style>
