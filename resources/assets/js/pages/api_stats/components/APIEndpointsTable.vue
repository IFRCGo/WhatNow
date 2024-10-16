<template>
  <div>
    <!-- Table Loading Spinner -->
    <table-loading :loading="isLoading"></table-loading>

    <!-- The API Applications Stats Table -->
    <div class="table-responsive users-list-table-view">
      <b-table
        :items="currentRows"
        :fields="fields"
        :perPage="10">
      </b-table>
    </div>

    <!-- Table Pagination -->
    <b-pagination
      v-if="currentEndpointsData.length > 10"
      size="md"
      :total-rows="currentEndpointsData.length"
      v-model="currentPage"
      :per-page="10"
      :limit="10"
      align="center"></b-pagination>

    <p v-if="currentEndpointsData.length === 0 && !isLoading">
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
      if (this.fromDate) this.fetchData()
    },
    // Fetch Data when the fromDate changes, and we have a toDate set
    fromDate () {
      if (this.toDate) this.fetchData()
    },
    // Fetch Data when the paginated page changes
    currentPage () {
      this.fetchData()
    }
  },
  methods: {
    async fetchData () {
      this.isLoading = true

      try {
        await this.$store.dispatch('usage/fetchEndpointUsage', {
          fromDate: this.fromDate,
          toDate: this.toDate
        })
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
      } finally {
        this.isLoading = false
      }
    }
  },
  computed: {
    currentRows () {
      return this.currentEndpointsData.slice(10 * this.currentPage - 10, 10 * this.currentPage)
    },
    ...mapGetters({
      currentEndpointsData: 'usage/currentEndpointsData'
    })
  },
  data () {
    return {
      isLoading: true,
      currentPage: 1,
      fields: [
        { key: 'organisation_name', label: this.$t('api_stats.field_headers.organisation'), sortable: true, tdClass: 'align-middle', thClass: 'align-middle' },
        { key: 'application_name', label: this.$t('api_stats.field_headers.application'), sortable: true, tdClass: 'align-middle', thClass: 'align-middle' },
        { key: 'endpoint', sortable: true, tdClass: 'align-middle', thClass: 'align-middle' },
        { key: 'hit_count', label: this.$t('api_stats.field_headers.number_of_hits'), sortable: true, tdClass: 'align-middle', thClass: 'align-middle' }
      ]
    }
  }
}
</script>
