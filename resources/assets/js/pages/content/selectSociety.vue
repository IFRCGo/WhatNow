<template>
  <v-select
    :dir="isLangRTL(locale) ? 'rtl' : 'ltr'"
    v-model="selectedSoc"
    class="w-100 v-select-custom"
    :options="listOfSocieties"
    label="name" :disabled="listOfSocieties.length === 0"
    :placeholder="$t('content.whatnow.no_soc')">
    <template slot="option" slot-scope="option">
      <div class="ml-2 rtl-mr-2 dropdown-option">
        {{ option.name }}
      </div>
    </template>
    <template slot="selected-option" slot-scope="option">
      <div class="ml-2 rtl-mr-2 dropdown-option">
        {{ option.name }}
      </div>
    </template>
  </v-select>
</template>
<script>
import { mapGetters } from 'vuex'
import * as permissionsList from '../../store/permissions'

export default {
  props: ['selected', 'staynull', 'dontfilter', 'countryCode'],
  async mounted () {
    this.loading = true
    await this.fetchOrganisations()
    this.getLocalStorage()
    this.loading = false
  },
  data () {
    return {
      loading: false
    }
  },
  watch: {
    selectedSoc: {
      handler (val) {
        if (val) {
          this.setLocalStorage()
        }
      },
      deep: true
    }
  },
  methods: {
    async fetchOrganisations () {
      try {
        await this.$store.dispatch('content/fetchOrganisations')
      } catch (e) {
        console.log(e)
      }
    },
    getLocalStorage () {

      let soc = this.countryCode
      if (!soc) {
        soc = localStorage.getItem('soc')

        if (!soc) {
          soc = this.staynull ? null : 'USA'
        }
      }
      if (soc) {
        this.selectedSoc = this.filteredSocieties.find(society => society.countryCode.toLowerCase() === soc.toLowerCase())
      }

      if (!this.selectedSoc && !this.countryCode && !this.staynull) {
        this.selectedSoc = this.filteredSocieties[0]
      }
    },
    setLocalStorage () {
      localStorage.setItem('soc', this.selectedSoc.countryCode)
    },
    clearLocalStorage () {
      localStorage.removeItem('soc')
    }
  },
  computed: {
    selectedSoc: {
      get () {
        return this.selected
      },
      set (value) {
        if (!value) {
          this.clearLocalStorage()
        }
        this.$emit('update:selected', value)
      }
    },
    filteredSocieties () {
      if (this.dontfilter) {
        return this.societies
      }
      if (this.user) {
        if (this.can(this.user, permissionsList.ALL_ORGANISATIONS)) {
          return this.societies
        }
        return this.societies.filter(soc => this.user.data.organisations.indexOf(soc.countryCode) !== -1)
      }
      return this.societies
    },
    listOfSocieties () {
      if (this.filteredSocieties) {
        return [...this.filteredSocieties].sort((a, b) => a.name.localeCompare(b.name))
      }
      return []
    },
    ...mapGetters({
      locale: 'lang/locale',
      user: 'auth/user',
      societies: 'content/organisations'
    })
  }
}
</script>
<style>
.vs--disabled {
  .vs__dropdown-toggle,
  .vs__clear,
  .vs__search,
  .vs__selected,
  .vs__open-indicator {
    cursor: $disabled-cursor;
    background-color: #E9E9E9!important;
  }
}
</style>

