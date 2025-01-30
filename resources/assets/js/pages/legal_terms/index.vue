<template>
  <div class="tc-page">
    <div class="tc-page-header d-flex justify-content-between align-items-center">
      <h2 class="tc-page-title">Terms and Conditions</h2>
      <div v-if="latestTerm && user && user?.data?.user_profile?.terms_version !== latestTerm.version">
        <v-button :disabled="disabled" class="btn-primary btn btn-lg" @click="acceptTerms">
          Accept
        </v-button>
      </div>
    </div>
    <div class="tc-container" @scroll="handleScroll">
      <div v-if="latestTerm" v-html="markdown(latestTerm.content)"></div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  loading: false,
  mounted() {
    this.fetchLatestTerm();
  },
  methods: {
    handleScroll(event) {
      const container = event.target;
      if (container.scrollTop + container.clientHeight >= container.scrollHeight) {
        this.disabled = false;
      }
    },
    async fetchLatestTerm() {
      try {
        await this.$store.dispatch('terms/fetchLatestTerms')
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
        console.error(e)
      }
    },
    async acceptTerms () {
      try {
        const changes = {
        accepted_agreement: true
      }
      await this.$store.dispatch('auth/patchUser', { id: this.user.data.id, changes })
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
        console.error(e)
      }
    }
  },
  data() {
    return {
      disabled: true,
    };
  },
  computed: mapGetters({
    user: 'auth/user',
    latestTerm: 'terms/latest',
  }),
}
</script>

<style scoped lang="scss">
.tc-page {
  width: 80%;
  max-width: 1622px;
  margin: 0 auto;

  .tc-page-title {
    font-size: 55px;
    font-weight: 600;
    color: #1e1e1e;
    margin: 33px 0 40px 0;
  }
}
.tc-container {
  font-size: 20px;
  height: 90vh;
  overflow-y: scroll;

  &::-webkit-scrollbar {
    -webkit-appearance: none;
    width: 10px;
  }

  &::-webkit-scrollbar-thumb {
    border-radius: 5px;
    background-color: rgba(0, 0, 0, .5);
    -webkit-box-shadow: 0 0 1px rgba(255, 255, 255, .5);
  }
}
</style>