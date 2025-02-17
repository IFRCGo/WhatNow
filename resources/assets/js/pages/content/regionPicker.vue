<template>
  <v-select v-if="list.length !== 0" :disabled="fetching || list.length === 0" :dir="isLangRTL(locale) ? 'rtl' : 'ltr'" :value="value" @input="$emit('input', $event)" class="w-100 styled-select" :options="list" label="title" :placeholder="$t('content.whatnow.no_region')">
    <template slot="option" slot-scope="option">
      <div class="ml-2 rtl-mr-2 dropdown-option">
        <span v-if="option.translations[selectLang]?.title">
          {{ option.translations[selectLang].title }}
        </span>
        <span v-else-if="Object.keys(option.translations)>0">
          {{ option.translations[Object.keys(option.translations)[0]]?.title }}
        </span>
        <span v-else>
          {{ option.title }}
        </span>
      </div>
    </template>
    <template slot="selected-option" slot-scope="option">
      <div class="ml-2 rtl-mr-2 dropdown-option">
        <span v-if="option.translations[selectLang]?.title">
          {{ option.translations[selectLang].title }}
        </span>
        <span v-else-if="Object.keys(option.translations)>0">
          {{ option.translations[Object.keys(option.translations)[0]]?.title }}
        </span>
        <span v-else>
          {{ option.title }}
        </span>
      </div>
    </template>
  </v-select>
</template>
<script>
import { mapGetters } from 'vuex'

export default {
  props: ['value', 'regionList', 'translationCode', 'socCode'],
  data () {
    return {
      fetching: true
    }
  },
  mounted () {
    this.fetch()
  },
  methods: {
    async fetch () {
      this.fetching = true
      if (this.socCode) {
        await this.$store.dispatch('content/fetchRegions', this.socCode)
        this.fetching = false
      }
    }
  },
  watch: {
    socCode () {
      this.fetch()
    }
  },
  computed: {
    list () {
      if (this.regions) {
        return this.regions.sort((a, b) => a.title.localeCompare(b.title))
      }
      return []
    },
    selectLang () {
      if (this.translationCode) {
        return this.translationCode
      } else {
        return this.locale
      }
    },
    ...mapGetters({
      locale: 'lang/locale',
      regions: 'content/regionsArray'
    })
  }
}
</script>
