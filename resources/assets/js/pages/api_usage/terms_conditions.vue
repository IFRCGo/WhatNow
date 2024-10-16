<template>
  <b-container fluid>
    <page-banner>
      <b-col cols="6">
        <h1>{{ $t('sidebar.terms_conditions') }}</h1>
      </b-col>
      <b-col cols="6">
        <b-button size="lg" variant="primary" class="float-right rtl-float-left mr-2" :disabled="updatingTerms" @click="saveTerms">
          <fa spin :icon="['fas', 'spinner']" v-if="updatingTerms"/>
          <span v-if="updatingTerms">{{ $t('content.whatnow.publishing') }}</span>
          <span v-else>{{ $t('common.publish_new') }}</span>
        </b-button>
      </b-col>
    </page-banner>
    <b-row class="mb-4 pb-0 pl-4 pr-4 pt-4 pb-4 bg-white">
      <b-col>
        <b-card>
          <b-row class="mb-3" v-if="can(user, permissions.TERMS_UPDATE)">
            <b-col cols="12">
              <p v-if="terms">
                {{ $t('common.published') }} {{ terms.createdAt | moment('MMM DD YYYY, HH:mm') }}
              </p>
              <router-link :to="{ name: 'api-usage.prev_terms_conditions', params: {} }" class="underlined-link">
                {{ $t('api_usage.view_prev_terms') }}
              </router-link>
            </b-col>

            <b-col cols="12" class="mt-4">
              <label for="termsVersion" class="text-uppercase text-secondary"><b>{{ $t('api_usage.version') }}</b></label>
              <b-form-input
                v-if="terms"
                id="termsVersion"
                type="number"
                v-model="terms.version"
                class="mb-2"
                :state="updateErrors.errors.version ? false : null">
              </b-form-input>
              <b-form-invalid-feedback id="termsVersionFeedback">
                <!-- This will only be shown if the preceeding input has an invalid state -->
                <p v-for="error in updateErrors.errors.version">
                  {{ error }}
                </p>
              </b-form-invalid-feedback>
              <div v-if="fetchingTerms">
                <spooky width="55%" height="40px" class="mb-3"></spooky>
                <spooky width="60%" height="12px" class="mb-2"></spooky>
                <spooky width="100%" height="12px" class="mb-2"></spooky>
                <spooky width="20%" height="12px" class="mb-2"></spooky>
                <spooky width="15%" height="12px" class="mb-2"></spooky>
                <spooky width="75%" height="12px" class="mb-2"></spooky>
                <spooky width="5%" height="12px" class="mb-2"></spooky>
                <spooky width="30%" height="12px" class="mb-2"></spooky>
              </div>
              <vue-simplemde v-else-if="terms" v-model="terms.content" ref="markdownEditor" :configs="mdeConfig" />
            </b-col>
          </b-row>
        </b-card>
      </b-col>
    </b-row>

  </b-container>
</template>

<style>
  @import '~simplemde/dist/simplemde.min.css';
</style>

<style lang="scss">
  .editor-toolbar {
    a {
      &:nth-of-type(10), &:nth-of-type(11) {
        display: none;
      }
    }
  }
</style>

<script>
import VueSimplemde from 'vue-simplemde'
import { mapGetters } from 'vuex'
import PageBanner from '~/components/PageBanner'
import * as permissionsList from '../../store/permissions'
import Spooky from '../../components/global/Spooky'

export default {
  components: {
    Spooky,
    PageBanner,
    VueSimplemde
  },
  data () {
    return {
      mdeConfig: {
        toolbar: false
      },
      permissions: permissionsList,
      fetchingTerms: false,
      updatingTerms: false,
      terms: null,
      updateErrors: {
        errors: {

        }
      }
    }
  },
  watch: {
    lastError () {
      if (this.lastError.response) {
        this.updateErrors = this.lastError.response.data
      }
    }
  },
  mounted () {
    this.fetchLatestTerms()
  },
  methods: {
    async fetchLatestTerms () {
      this.fetchingTerms = true
      try {
        await this.$store.dispatch('terms/fetchLatestTerms')
        this.terms = this.latestTerm
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
      }
      this.fetchingTerms = false
    },
    async saveTerms () {
      this.updatingTerms = true
      this.updateErrors = { errors: {}}
      try {
        await this.$store.dispatch('terms/updateTerms', this.terms)
        await this.fetchLatestTerms()
        this.$noty.success(this.$t('common.published'))
      } catch (e) {
        this.$noty.error(this.$t('error_alert_text'))
      }
      this.updatingTerms = false
    }
  },
  computed: mapGetters({
    user: 'auth/user',
    latestTerm: 'terms/latest',
    lastError: 'generic/lastError'
  })
}
</script>
