<template>
    <b-row class="pl-4 pr-4 pb-4 pt-3">
      <b-col>
        <span v-if="!isPromo">{{ $t('content.whatnow.content') }}</span>
        <b-button
          size="sm"
          variant="primary"
          class="float-right rtl-float-left"
          v-if="user && can(user, permissions.CONTENT_CREATE) && selectedSoc && !isPromo"
          :to="{ name: 'content.create', params: { organisation: selectedSoc.countryCode, langCode: this.selectedLanguage, regionSlug: this.selectedRegion?.title } }">
          {{ $t('content.whatnow.create') }}
          <fa :icon="['fas', 'plus']" />
        </b-button>
        <hr v-if="!isPromo"/>
        <b-row v-if="!isPromo && translationsExist">
          <b-col>
            <b>{{ $t('content.whatnow.hazard') }}</b>
          </b-col>
          <b-col>
            <b>{{ $t('common.title') }}</b>
          </b-col>
          <b-col>
            <b>{{ $t('common.description') }}</b>
          </b-col>
          <b-col>
            <b>{{ $t('common.unpublished_edits') }}</b>
          </b-col>
          <b-col v-if="user">
            <b>{{ $t('common.actions') }}</b>
          </b-col>
        </b-row>
        <b-row v-if="!loadingContent && !translationsExist" class="mt-2">
          <b-col>
            <h3>{{ $t('content.whatnow.empty') }}</h3>
          </b-col>
        </b-row>
        <transition-group name="fade-slide">
          <whatnow-content-item
            v-for="content in filteredTranslations"
            :key="content.id"
            v-if="filteredTranslations !== null && !loadingContent"
            :selectedLanguage="selectedLanguage"
            :content="content"
            :isPromo="isPromo"
            v-on:languageChange="languageChange"
            :regionSlug="selectedRegion?.title">
          </whatnow-content-item>
          <whatnow-content-item
            v-for="content in uncreatedTranslations"
            :key="content.id"
            v-if="uncreatedTranslations !== null && !loadingContent && !isPromo && selectedRegion"
            :selectedLanguage="selectedLanguage"
            :content="content"
            :isPromo="isPromo"
            v-on:languageChange="languageChange"
            :regionSlug="selectedRegion?.title"
            :forceCreate="true">
          </whatnow-content-item>
        </transition-group>

        <transition-group name="fade" v-if="!isPromo">
          <b-row class="whatnow-row mt-2 border border-secondary" v-for="n in 5" :key="n" v-if="loadingContent">
            <b-col class="border border-top-0 border-bottom-0 border-left-0 pt-5 pb-5">
              <spooky width="75%" height="20px"></spooky>
            </b-col>
            <b-col class="border border-top-0 border-bottom-0 border-left-0 pt-5 pb-5">
              <spooky width="75%" height="20px"></spooky>
            </b-col>
            <b-col class="border border-top-0 border-bottom-0 border-left-0 pt-5 pb-5">
              <spooky width="75%" height="20px"></spooky>
              <spooky width="100%" height="10px" class="mt-2"></spooky>
              <spooky width="100%" height="10px" class="mt-1"></spooky>
            </b-col>
            <b-col class="border border-top-0 border-bottom-0 border-left-0 pt-5 pb-5">
              <spooky width="15%" height="20px"></spooky>
            </b-col>
            <b-col class="pt-5 pb-5 d-flex">
              <spooky width="100%" height="31px" style="width: 25%;"></spooky>
              <spooky width="100%" height="31px" style="width: 30%;" class="ml-1"></spooky>
            </b-col>
          </b-row>
        </transition-group>
      </b-col>
    </b-row>
</template>

<script>
import WhatnowContentItem from '~/pages/content/whatnowContentItem'
import { mapGetters } from 'vuex'
import * as permissionsList from '../../store/permissions'
import Spooky from '~/components/global/Spooky'

export default {
  props: ['selectedSoc', 'selectedLanguage', 'isPromo', 'selectedHazard', 'selectedRegion'],
  components: {
    Spooky,
    WhatnowContentItem
  },
  data () {
    return {
      permissions: permissionsList,
      loadingContent: false,
      currentTranslations: null
    }
  },
  mounted () {
    this.fetch()
  },
  watch: {
    selectedLanguage: {
      handler (val, oldVal) {
        if (val !== oldVal) {
          this.languageChange()
        }
      },
      deep: true
    },
    selectedSoc: {
      handler (val, oldVal) {
        if (val && oldVal && val.countryCode === oldVal.countryCode) {
          return
        }
        this.fetch()
      },
      deep: true
    },
    currentContent: {
       handler (val, oldVal) {
         this.languageChange()
       },
       deep: true
    }
  },
  methods: {
    async fetch (silent = false) {
      this.loadingContent = !silent
      await this.fetchContent()
      this.loadingContent = false
    },
    async fetchContent () {
      if (this.selectedSoc) {
        try {
          if (this.isPromo) {
            await this.$store.dispatch('content/fetchPublished', this.selectedSoc.countryCode)
          } else {
            await this.$store.dispatch('content/fetchOrganisationSingle', this.selectedSoc.countryCode)
            await this.$store.dispatch('content/fetchContent', this.selectedSoc.countryCode)
          }
          this.languageChange()
        } catch (e) {
          this.$noty.error(this.$t('error_alert_text'))
        }
      }
    },
    languageChange () {
      if (this.selectedSoc) {
        this.currentTranslations = []
        for (let i = 0; i < this.currentContent.length; i++) {
          let content = this.currentContent[i]
          let flattened = JSON.parse(JSON.stringify(content))
          if (content.translations) {
            flattened.currentTranslation = content.translations[this.selectedLanguage]
            this.currentTranslations.push(flattened)
          }
        }
      }
    }
  },
  computed: {
    ...mapGetters({
      user: 'auth/user',
      currentContent: 'content/currentContent'
    }),
    hazardSelected (val) {
      return this.selectedHazard
    },
    nationalTranslations () {
      const hazardfilter = this.hazardSelected
      let filtered = this.currentTranslations

      if (hazardfilter) {
        filtered = filtered.filter((translation) => {
          return translation.eventType === hazardfilter.eventType
        })
      }

      const national = filtered.filter((translation) => {
        return translation.regionName?.toLowerCase() === "national" || translation.regionName?.toLowerCase() === ""
      })

      return national;
    },
    filteredTranslations () {
      const hazardfilter = this.hazardSelected
      const regionFilter = this.selectedRegion
      let filtered = this.currentTranslations || []

      if (hazardfilter) {
        filtered = filtered.filter((translation) => {
          return translation.eventType === hazardfilter.eventType
        })
      }

      if (regionFilter) {
        filtered = filtered.filter((translation) => {
          return translation.regionName === regionFilter.title
        })
      } else {
        // Filter by empty region Name
        filtered = this.nationalTranslations
      }

      return filtered
    },
    uncreatedTranslations () {
      // If there is a region selected, get an empty list of events created from a national level.
      return this.nationalTranslations.filter((translation) => {
        return this.filteredTranslations.findIndex((fTranslation) => fTranslation.eventType === translation.eventType) === -1
      }).map((translation) => ({...translation, translations: {}, currentTranslation: {}})) || []
    },
    translationsExist () {
      if (!this.currentTranslations) {
        return false
      } else {
        let foundTranslation = false
        this.currentTranslations.forEach((translation) => {
          if (translation.currentTranslation) {
            foundTranslation = true
          }
        })
        return foundTranslation
      }
    }
  }
}
</script>
