<template>
  <div class="tc-page">
    <div class="tc-page-header d-flex justify-content-between align-items-center">
      <h2 class="tc-page-title">{{ $t('terms_conditions.title') }}</h2>
      <div v-if="latestTerm && user && user?.data?.user_profile?.terms_version !== latestTerm.version">
        <b-button :disabled="disabled" class="btn-primary btn btn-lg" @click="acceptTerms">
          {{ $t('terms_conditions.accept') }}	
        </b-button>
      </div>
    </div>
    <div class="tc-container" @scroll="handleScroll">
      <div class="d-flex justify-content-center" v-if="loading">
        <b-spinner type="grow" label="Loading..."></b-spinner>
      </div>
      <div v-if="latestTerm && !loading" v-html="markdown(latestTerm.content)"></div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  mounted() {
    this.fetchLatestTerm();
    window.addEventListener('scroll', this.handleScroll);
  },
  beforeDestroy() {
    window.removeEventListener('scroll', this.handleScroll);
  },
  methods: {
    handleScroll() {
      const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      const windowHeight = window.innerHeight;
      const documentHeight = document.documentElement.scrollHeight;

      if (scrollTop + windowHeight >= documentHeight) {
        this.disabled = false;
      }
    },
    async fetchLatestTerm() {
      try {
        this.loading = true;
        await this.$store.dispatch('terms/fetchLatestTerms')
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
        console.error(e)
      } finally {
        this.loading = false;
      }
    },
    async acceptTerms () {
      try {
        this.loading = true;
        const changes = {
        accepted_agreement: true
      }
      await this.$store.dispatch('auth/patchUser', { id: this.user.data.id, changes })
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
        console.error(e)
      } finally {
        this.loading = false;
      }
    }
  },
  data() {
    return {
      disabled: true,
      loading: false,
    };
  },
  computed: mapGetters({
    user: 'auth/user',
    latestTerm: 'terms/latest',
  }),
}
</script>

<style scoped lang="scss">
@import '../../../sass/variables.scss';
.tc-page {
  width: 80%;
  max-width: 1622px;
  margin: 0 auto;

  .tc-page-header {
    position: sticky;
    top: 0;
    background-color: $body-bg;
  }

  .tc-page-title {
    font-size: 55px;
    font-weight: 600;
    color: $dark;
    margin: 33px 0 40px 0;
  }
}
.tc-container {
  font-size: 14px;
}
</style>