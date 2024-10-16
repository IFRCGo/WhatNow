<template>
  <b-container fluid>
    <b-row align-h="between" align-v="center" class="pl-4 pr-4 mb-1">
      <b-col md="12" xl="6">
        <h1>{{ $t('api_usage.terms_conditions') }}</h1>
      </b-col>
    </b-row>

    <b-row class="mt-4 mb-4 pb-0 pl-4 pr-4 pt-4 pb-4 bg-white">
        <b-col v-if="termsAvailable">
            <table-loading :loading="fetchingTerms"></table-loading>
            <b-table hover
                     :items="terms.data"
                     :fields="fields"
                     :perPage="terms.meta.per_page"
                     :show-empty="false"
                     no-local-sorting>
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
                <template #cell(createdAt)="data">
                    {{ data.item.createdAt | moment("MM/DD/YY") }}
                </template>
            </b-table>
            <b-pagination

                    v-if="terms.meta.total > terms.meta.per_page"
                    size="md"
                    :total-rows="terms.meta.total"
                    v-model="currentPage"
                    :per-page="terms.meta.per_page"
                    :limit="10"
                    align="center"></b-pagination>
            <p v-if="terms.data.length === 0">
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
import TableLoading from '~/components/global/TableLoading'
import swal from 'sweetalert2'
import * as permissionsList from '../../store/permissions'
import { mapGetters } from 'vuex'
import Avatar from 'vue-avatar'

const fields = [
    { key: 'version', sortable: false, tdClass: 'align-middle' },
    'user',
    { key: 'createdAt', sortable: false, tdClass: 'align-middle' }
]

const fetchHandler = {
  handler (val, oldVal) {
    if (val !== oldVal) {
      this.currentPage = 1
      this.fetchTerms()
    }
  },
  deep: true
}

export default {
  components: {
    TableLoading,
    Avatar
  },
  data () {
    return {
      permissions: permissionsList,
      fetchingTerms: false,
      selected: null,
      fields: fields,
      currentPage: 1,
    }
  },
  mounted () {
    this.fetchTerms()
  },
  watch: {
    currentPage: {
      handler (val, oldVal) {
        if (val !== oldVal) {
           this.fetchTerms()
        }
      },
      deep: true
    },
  },
  methods: {
    async fetchTerms () {
      this.fetchingTerms = true
        try {
            await this.$store.dispatch('terms/fetchTerms',
                {
                    page: this.currentPage
                })
        } catch (e) {
            this.$noty.error(this.$t('error_alert_text'))
        }
    this.fetchingTerms = false
    },
    toggleDeactivate (val) {
      let action = val === "User Deactivated" ? this.$t('common.activate') : this.$t('common.deactivate')
      swal({
        title: this.$t('common.are_you_sure'),
        text: `${action}`,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: action
      }).catch(swal.noop)
    }
  },
 computed: mapGetters({
  user: 'auth/user',
  terms: 'terms/terms',
  termsAvailable: 'terms/check',
 })
}
</script>
