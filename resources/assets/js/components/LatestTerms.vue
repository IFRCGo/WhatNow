<template>
  <div v-if="latestTerm && !loading" v-html="markdown(latestTerm.content)" class="terms-modal"></div>
  <div v-else-if="loading">
    <spooky width="55%" height="40px" class="mb-3"></spooky>
    <spooky width="60%" height="12px" class="mb-2"></spooky>
    <spooky width="100%" height="12px" class="mb-2"></spooky>
    <spooky width="20%" height="12px" class="mb-2"></spooky>
    <spooky width="15%" height="12px" class="mb-2"></spooky>
    <spooky width="75%" height="12px" class="mb-2"></spooky>
    <spooky width="5%" height="12px" class="mb-2"></spooky>
    <spooky width="30%" height="12px" class="mb-2"></spooky>
  </div>
</template>
<script>
import { mapGetters } from 'vuex'
import Spooky from './global/Spooky'

export default {
  components: {
    Spooky
  },
  data () {
    return {
      loading: false
    }
  },
  async mounted () {
    this.loading = true
    try {
      await this.$store.dispatch('terms/fetchLatestTerms')
    } catch (e) {
      this.$noty.error(this.$t('error_alert_text'))
    }
    this.loading = false
  },
  computed: mapGetters({
    latestTerm: 'terms/latest'
  })
}

</script>
