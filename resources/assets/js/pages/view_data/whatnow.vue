<template>
  <b-container fluid class="h-100">
    <b-row v-if="!selectedSoc" align-h="center" class="h-100 whatnow-choose">
      <b-col class="d-flex flex-column justify-content-center" sm="12" md="10">
        <h1 class="font-weight-bold whatnow-choose-header">{{ $t('view_data.which')}}</h1>
        <div>
          <selectSociety :selected.sync="selectedSoc" :staynull="true" :dontfilter="true"></selectSociety>
        </div>
      </b-col>
    </b-row>
    <b-row class="pl-4 pr-4 pb-1 pt-3 bg-black align-items-center" v-show="selectedSoc">
      <b-col>
        <selectSociety :selected.sync="selectedSoc" :staynull="true" :dontfilter="true" class="mt-1"></selectSociety>
        <p>{{ selectedSoc ? selectedSoc.label : 'Select a society' }}</p>
      </b-col>
    </b-row>

    <b-row class="pb-0 pl-4 pr-4 whatnow-language-picker text-light" v-if="selectedSoc">
      <b-col>
        <b-nav tabs>
          <b-nav-item
            v-for="lang in currentLanguages"
            :key="lang"
            @click="selectedLanguage = lang"
            :active="selectedLanguage === lang">
            <div class="nav-link-wrapper text-center h-100" v-if="lang">
              {{ lang | uppercase }} <br />
              <small v-if="languages[lang]">{{ truncate(languages[lang].name, 8) }}</small>
            </div>
          </b-nav-item>
        </b-nav>
      </b-col>
    </b-row>

    <page-banner v-show="selectedSoc" v-if="attributionTranslation && attributionTranslation.published">
      <b-col cols="auto" v-if="attribution && attribution.imageUrl">
        <b-img :src="attribution.imageUrl" width="128" height="128" class="ns-logo"></b-img>
      </b-col>
      <b-col v-if="!loadingContent && attribution !== null">
        <h1 class="font-weight-bold" v-if="attributionTranslation.name">{{ $t('content.whatnow.messages') }}<br />{{ attributionTranslation.name }}</h1>
        <p v-if="attributionTranslation.attributionMessage">
          <b>
            {{ truncate(attributionTranslation.attributionMessage, 90) }}
            <span class="ml-4">
              {{ $t('view_data.cap.more') }} <a :href="attribution.url" rel="noreferrer" target="_blank">{{ truncate(attribution.url, 20) }}</a>
            </span>
          </b>
        </p>
      </b-col>
    </page-banner>

    <b-row class="pl-4 pr-4 mt-3 mb-3" v-if="hazardsList">
      <b-col>
        <v-select :dir="isLangRTL(locale) ? 'rtl' : 'ltr'" label="eventType" class="float-right bg-white" :options="hazardsList" :placeholder="$t('content.whatnow.choose_hazard')" v-model="selectedHazard" :disabled="hazardsList.length === 0"></v-select>
      </b-col>
    </b-row>

    <whatnow-list :selectedSoc="selectedSoc" :selectedLanguage="selectedLanguage" :isPromo="true" class="whatnow-list-promo" v-show="selectedSoc" :selectedHazard="selectedHazard"></whatnow-list>
  </b-container>
</template>

<script>
import SelectSociety from '~/pages/content/selectSociety'
import WhatnowList from '~/pages/content/whatnowList'
import { mapGetters } from 'vuex'
import Spooky from '~/components/global/Spooky'
import axios from 'axios'
import PageBanner from '~/components/PageBanner'
import { languages } from 'countries-list'

export default {
  props: {
    countryCode: {
      type: String,
      default: null
    }
  },
  components: {
    SelectSociety,
    Spooky,
    WhatnowList,
    PageBanner
  },
  data () {
    return {
      selectedSoc: null,
      selectedLanguage: 'en',
      loadingContent: false,
      currentTranslations: null,
      selectedHazard: null,
      languages
    }
  },
  watch: {
    selectedLanguage: {
      handler (val, oldVal) {
        if (val !== oldVal) {
          this.setLocalStorage()
        }
      },
      deep: true
    },
    currentLanguages: {
      handler (val) {
        if (val && val.find) {
          if (!val.find(lang => lang === this.selectedLanguage)) {
            this.selectedLanguage = val[0]
          }
        }
      },
      deep: true
    },
    currentContent: {
      handler (val, oldVal) {
        this.contentChange()
      }
    },
    selectedSoc: {
      handler (val, oldVal) {
        if (val) {
          this.$router.push({ name: 'view-whatnow-code', params: { countryCode: val.countryCode } })
        }
      },
      deep: true
    }
  },
  mounted () {
    this.getLocalStorage()
  },
  methods: {
    setLocalStorage () {
      localStorage.setItem('lang', this.selectedLanguage)
    },
    getLocalStorage () {
      let lang = localStorage.getItem('lang')

      if (!lang) {
        lang = 'en'
      }

      this.selectedLanguage = lang
    },
    contentChange () {
      if (this.selectedSoc) {
        this.selectedHazard = null
        this.currentTranslations = this.currentContent.map((content) => {
          let flattened = JSON.parse(JSON.stringify(content))
          if (content.translations) {
            flattened.currentTranslation = content.translations[this.selectedLanguage]
          }
          return flattened
        })
      }
    }
  },
  metaInfo () {
    return { title: this.$t('content.whatnow.whatnow') }
  },
  computed: {
    attribution () {
      if (this.selectedSoc) {
        return this.societies.find(soc => soc.countryCode === this.selectedSoc.countryCode)
      }
      return null
    },
    attributionTranslation() {
      if (this.attribution && this.selectedLanguage) {
        return this.attribution.translations.find(trans => trans.languageCode === this.selectedLanguage)
      }
      return null
    },
    ...mapGetters({
      locale: 'lang/locale',
      user: 'auth/user',
      currentContent: 'content/currentContent',
      currentLanguages: 'content/currentLanguages',
      societies: 'content/organisations'
    }),
    hazardsList () {
      return this.currentTranslations
    }
  }
}
</script>
