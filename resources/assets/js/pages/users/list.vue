  <template>
    <b-container fluid>
      <page-banner>
        <b-col sm>
          <h1 class="users-title">{{ $t('users.list.manage') }}</h1>
        </b-col>
        <b-col sm>
          <b-button  class="float-right rtl-float-left new-btn" :to="{ name:'users.new' }" v-if="can(user, permissions.USERS_CREATE) && !apiUsers">
            {{ $t('users.list.create') }}
          </b-button>
        </b-col>
      </page-banner>
      <b-row class="pb-2 px-4 pt-4 bg-white" align-v="center">
        <b-col cols="3" xl="2">
          <p class="select-header"> {{ $t('users.list.select_status') }}</p>
          <b-form-select
            v-model="activatedFilter"
            value-field="value"
            class="v-form-select-custom"
            text-field="name"
            :options="[
            {
              name: 'Status',
              value: null
            },
            {
              name: 'Activated',
              value: 1
            },
            {
              name: 'Deactivated',
              value: 0
            }]" />
        </b-col>
        <b-col cols="3" xl="2" v-if="!apiUsers">
          <p class="select-header"> {{ $t('users.list.select_role') }}</p>
          <b-form-select
            v-model="roleFilter"
            value-field="id"
            class="v-form-select-custom"
            text-field="name"
            :options="roleOptions"/>
        </b-col>
        <b-col cols="3" xl="2" v-if="apiUsers">
          <p class="select-header"> {{ $t('users.list.select_country') }}</p>
          <b-form-select
            v-model="countryFilter"
            class="v-form-select-custom"
            value-field="code"
            text-field="name"
            :options="countryList"/>
        </b-col>
        <b-col cols="3" xl="2" v-if="apiUsers">
          <p class="select-header"> {{ $t('users.list.select_terms') }}</p>
          <b-form-select
            v-model="termsFilter"
            class="v-form-select-custom"
            value-field="version"
            text-field="version"
            :options="termsList"/>
        </b-col>
        <b-col>
          <p class="select-header" v-if="!apiUsers"> {{ $t('users.list.select_society') }}</p>
          <selectSociety
            class="float-right"
            :selected.sync="selectedSoc"
            :staynull="true"
            v-if="!apiUsers"/>
        </b-col>
        <b-col cols="2" xl="1">
          <b-button @click="clearFilters" :disabled="noFilters" class="float-right new-btn">
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
                <b-img v-if="data.item.user_profile?.photo_url" :src="data.item.user_profile.photo_url" width="42" height="42" rounded="circle" alt="" role="presentation"></b-img>
                <avatar v-else :username="data.item.user_profile.first_name + ' ' + data.item.user_profile.last_name " />
              </div>
            </template>
            <template #cell(actions)="data">
              <b-button class="mb-1 new-btn" :to="{ name: 'users.edit', params: { id: data.item.id, isApiUser: apiUsers } }" v-if="can(user, permissions.USERS_EDIT)"> {{ $t('common.edit') }} </b-button>
              <b-button class="mb-1 new-btn" @click="toggleDeactivate(data.item)" v-if="can(user, permissions.USERS_DEACTIVATE) && can(user, permissions.USERS_REACTIVATE)"> {{ data.item.activated ? $t('common.deactivate') : $t('common.activate') }} </b-button>
            </template>
          </b-table>
          </div>
          <b-pagination

            v-if="users.meta.total > users.meta.per_page"
            size="md"
            :total-rows="users.meta.total"
            v-model="currentPage"
            :per-page="users.meta.per_page"
            :limit="10"
            align="center"></b-pagination>
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

let termsDefault = 'Terms and Conditions';

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
  <style>
  .users-title {
    font-family: Poppins;
    font-size: 55px;
    font-weight: 600;
    line-height: 80px;
    letter-spacing: -0.01em;
    text-align: left;
    text-underline-position: from-font;
    text-decoration-skip-ink: none;
  }

  .v-form-select-custom {
    background: #E9E9E9;
    font-family: Poppins;
    border: none;
    border-radius: 10px;
    padding: 8px 8px;
    font-family: Poppins;
    font-size: 18px;
    height: 48px;
  }
  .v-select-custom {
    input {
      font-family: Poppins;
      font-size: 18px;
      font-weight: 300;
      line-height: 34px;
      text-align: left;
      text-underline-position: from-font;
      text-decoration-skip-ink: none;
    }
  }
 th {
   border-right: none !important;
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
