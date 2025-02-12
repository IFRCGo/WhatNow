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

    <page-banner v-show="selectedSoc" v-if="attributionTranslation && attributionTranslation.published">
      <b-col cols="auto" v-if="attribution && attribution.imageUrl">
        <b-img :src="attribution.imageUrl" width="128" height="128" class="ns-logo"></b-img>
      </b-col>
      <b-col v-if="!loadingContent && attribution !== null">
        <h1 class="font-weight-bold title" v-if="attributionTranslation.name">{{ $t('content.whatnow.title') }}</h1>
      </b-col>
    </page-banner>

    <b-row class="ml-4 mr-4 pl-4 pr-4 pb-3 pt-3 selects-container d-flex align-items-center justify-content-start" v-show="selectedSoc">
      <b-col cols="auto" class="d-flex align-items-center">
        <customSelectSociety class="select-society-custom"> :selected.sync="selectedSoc" :staynull="true" :dontfilter="true"></customSelectSociety>
        <p class="ml-2 mb-0">{{ selectedSoc ? selectedSoc.label : 'Select a society' }}</p>
      </b-col>
      <b-col cols="auto" v-if="hazardsList" class="d-flex align-items-center">
        <v-select
          :dir="isLangRTL(locale) ? 'rtl' : 'ltr'"
          label="eventType"
          class="v-select-custom"
          :options="hazardsList"
          :placeholder="$t('content.whatnow.choose_hazard')"
          v-model="selectedHazard"
          :disabled="hazardsList.length === 0">
        </v-select>
      </b-col>
    </b-row>
    <b-row class="m-3">
      <b-col>
        <b-nav tabs>
          <b-nav-item
            v-for="lang in currentLanguages"
            :key="lang"
            class="wn-item"
            @click="selectedLanguage = lang"
            :active="selectedLanguage === lang">
            <div class="nav-link-wrapper text-center h-100" v-if="lang">
              {{ lang | uppercase }} -
              <small v-if="languages[lang]">{{ truncate(languages[lang].name, 8) }}</small>
            </div>
          </b-nav-item>
        </b-nav>
      </b-col>
    </b-row>
    <whatnow-list :selectedSoc="selectedSoc" :selectedLanguage="selectedLanguage" :isPromo="true" class="whatnow-list-promo" v-show="selectedSoc" :selectedHazard="selectedHazard"></whatnow-list>
  </b-container>
</template>

<script>
import SelectSociety from '~/pages/content/selectSociety'
import CustomSelectSociety from '~/pages/content/customSelectSociety'
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
    PageBanner,
    CustomSelectSociety
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
<style>
.selects-container {
  background: #F7F7F7;
  border-radius: 10px;
}

.v-select-custom {
  font-family: Poppins;
  div {
    background: #E9E9E9;
    font-family: Poppins;
    border: none;
    border-radius: 10px;
    padding: 2px;
  }
}
.v-select {
  min-width: 192px;
}

.page-banner {
  background-image: none;
}

.nav-link-wrapper {
  color: red;
}

.nav-tabs {
  border-bottom: 1px solid #dee2e6;
}

.title {
  font-size: 55px;
}
</style>
