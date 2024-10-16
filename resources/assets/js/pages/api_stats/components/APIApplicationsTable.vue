<template>
  <div>
    <!-- Table Loading Spinner -->
    <table-loading :loading="isLoading"></table-loading>

    <!-- The API Applications Stats Table -->
    <div class="table-responsive users-list-table-view">
      <b-table
        :items="currentRows"
        :fields="fields"
        :perPage="totalPerPage">
      </b-table>
    </div>

    <!-- Table Pagination -->
    <b-pagination
      v-if="totalRows > totalPerPage"
      size="md"
      :total-rows="totalRows"
      v-model="currentPage"
      :per-page="totalPerPage"
      :limit="10"
      align="center"></b-pagination>

    <p v-if="currentRows.length === 0 && !isLoading">
      {{ $t('api_stats.empty') }}
    </p>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import TableLoading from '~/components/global/TableLoading'

export default {
  props: ['toDate', 'fromDate'],
  components: {
    TableLoading
  },
  mounted () {
    this.fetchData()
  },
  watch: {
    // Fetch Data when the toDate changes, and we have a fromDate set
    toDate () {
      if (this.fromDate) this.fetchData(1)
    },
    // Fetch Data when the fromDate changes, and we have a toDate set
    fromDate () {
      if (this.toDate) this.fetchData(1)
    },
    // Fetch Data when the paginated page changes
    currentPage () {
      this.fetchData()
    }
  },
  methods: {
    async fetchData (page) {
      this.isLoading = true

      try {
        await this.$store.dispatch('usage/fetchApplicationUsage', {
          page: page || this.currentPage,
          fromDate: this.fromDate,
          toDate: this.toDate
        })

        if (page) {
          this.currentPage = page
        }
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
      } finally {
        this.isLoading = false
      }
    }
  },
  computed: {
    ...mapGetters({
      totalRows: 'usage/totalApplicationsRows',
      totalPerPage: 'usage/totalApplicationsPerPage',
      currentRows: 'usage/currentApplicationsData'
    })
  },
  data () {
    return {
      isLoading: true,
      currentPage: 1,
      fields: [
        { key: 'username', sortable: true, tdClass: 'align-middle', thClass: 'align-middle' },
        { key: 'organisation', sortable: true, tdClass: 'align-middle', thClass: 'align-middle' },
        { key: 'location', sortable: true, tdClass: 'align-middle', thClass: 'align-middle' },
        { key: 'name', label: this.$t('api_stats.field_headers.application_name'), sortable: true, tdClass: 'align-middle', thClass: 'align-middle' },
        { key: 'requestCount', label: this.$t('api_stats.field_headers.number_of_hits'), sortable: true, tdClass: 'align-middle', thClass: 'align-middle' },
        { key: 'estimatedUsers', label: this.$t('api_stats.field_headers.estimated_reach'), sortable: true, tdClass: 'align-middle', thClass: 'align-middle' }
      ]
    }
  }
}
</script>
