<template>
  <v-select
    :dir="isLangRTL(locale) ? 'rtl' : 'ltr'"
    v-model="selectedSoc"
    class="w-100 select-custom"
    :options="listOfSocieties"
    label="name" :disabled="listOfSocieties.length === 0"
    :placeholder="$t('content.whatnow.no_soc')">
    <template slot="option" slot-scope="option">
      <div class="ml-1 rtl-mr- dropdown-option">
        {{ option.name }}
      </div>
    </template>
    <template slot="selected-option" slot-scope="option">
      <div class="ml-1 rtl-mr-1 dropdown-option">
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
<style scoped>
/deep/ .vs__dropdown-toggle {
  font-family: Poppins!important;
  background-color: #E9E9E9 !important;  /* Color de fondo */
  border-radius: 10px !important;
  border: none !important;
  padding: 5px !important; /* Ajusta el padding */
  margin: 10px !important; /* Ajusta el margen */
}

/deep/ .vs__selected {
  color: #333 !important;
}

/deep/ .vs__search {
  padding: 0 !important; /* Elimina padding interno */
}
.v-select {
  min-width: 192px;
}
</style>


