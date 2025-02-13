  <template>
    <b-container fluid>
      <page-banner>
        <b-col cols="12">
          <h1 class="sec-title">{{ $t('regions.list.manage') }}</h1>
        </b-col>
        <b-col cols="12">
          <selectSociety
            class="float-right"
            :selected.sync="selectedSoc"
            :staynull="true"
          />
        </b-col>
      </page-banner>
      <b-row class="pb-2 px-4 pt-4 bg-white" align-v="center">
        <b-col>
          <b-button v-if="selectedSoc" size="sm" variant="dark" class="float-right" v-b-modal.modal-create>
            {{ $t('regions.list.add') }}
            <fa :icon="['fas', 'plus']" />
          </b-button>
          <p class="text-right" v-else>{{ $t('regions.select_soc') }}</p>
        </b-col>
      </b-row>
      <b-row class="p-4 bg-white">
        <b-col class="pl-0 pr-0">
          <table-loading :loading="fetchingRegions"></table-loading>
          <div class="table-responsive">
            <b-table hover
              :items="regions || []"
              :fields="fields"
              :perPage="20"
              :show-empty="false">
              <template #cell(title)="data">
                <span v-if="data.item.translations[cmsLocale]?.title">
                  {{data.item.translations[cmsLocale].title}}
                </span>
                <span v-else>
                  {{data.item.translations[Object.keys(data.item.translations)[0]]?.title}}
                </span>
              </template>
              <template #cell(actions)="data">
                <b-button variant="dark" size="sm" class="mb-1" @click="() => editRegion(data.item)">{{ $t('common.edit') }}</b-button>
                <b-button variant="dark" size="sm" class="mb-1" @click="() => deleteRegion(data.item.id)">{{ data.item.activated ? $t('common.deactivate') : $t('common.delete') }}</b-button>
              </template>
            </b-table>
          </div>
          <p v-if="regions.length === 0 && !fetchingRegions">
            {{ $t('regions.list.empty') }}
          </p>
        </b-col>
      </b-row>
      <b-modal
        id="modal-create"
        size="lg"
        centered
        hide-header
        hide-footer
        ref="modal"
      >
        <form ref="form" @submit.prevent="handleSubmit" class="px-3">
          <h2 class="h3">{{ $t(isEdit ? 'regions.list.edit' : 'regions.list.create') }}</h2>
          <b-row class="whatnow-language-picker-in-modal mb-4">
            <b-col>
              <b-nav tabs>
                <b-nav-item
                  v-for="lang in currentLanguages"
                  :key="lang"
                  @click="selectedLanguage = lang"
                  :active="selectedLanguage === lang">
                  <div class="nav-link-wrapper text-center h-100">
                    {{ lang | uppercase }} <br />
                    <small v-if="languages[lang]">{{ truncate(languages[lang].name, 8) }}</small>
                  </div>
                </b-nav-item>
              </b-nav>
            </b-col>
          </b-row>
          <b-form-group
            :label="$t('regions.form.name')"
            label-for="name-input"
          >
            <b-form-input
              id="name-input"
              v-model="names[selectedLanguage]"
              required
              aria-describedby="name-input-help"
            ></b-form-input>
            <b-form-text id="name-input-help">{{ $t('regions.form.name_help') }}</b-form-text>

          </b-form-group>
          <b-form-group
            :label="$t('regions.form.description')"
            label-for="description-input"
          >
            <b-form-input
              id="description-input"
              v-model="descriptions[selectedLanguage]"
              required
              aria-describedby="description-input-help"
            ></b-form-input>
            <b-form-text id="description-input-help">{{ $t('regions.form.description_help') }}</b-form-text>

          </b-form-group>
          <div class="modal-footer pb-0 px-0">
            <b-button type="button" variant="outline-danger" class="btn btn-outline-danger" @click.prevent="resetModal">{{ $t('common.cancel') }}</b-button>
            <b-button type="submit" :disabled="fetchingRegions" variant="dark" class="btn btn-dark">
              <fa :icon="['fas', 'spinner']" spin v-show="fetchingRegions"/>
              {{ $t(isEdit ? 'update' : 'common.create') }}
            </b-button>
          </div>
        </form>
      </b-modal>
    </b-container>
  </template>

<script>
import swal from 'sweetalert2'
import axios from 'axios'
import TableLoading from '~/components/global/TableLoading'
import { mapGetters } from 'vuex'
import Avatar from 'vue-avatar'
import * as permissionsList from '../../store/permissions'
import PageBanner from '~/components/PageBanner'
import SelectSociety from '~/pages/content/selectSociety'
import { languages } from 'countries-list'

export default {
  components: {
    TableLoading,
    Avatar,
    PageBanner,
    SelectSociety
  },
  data () {
    return {
      permissions: permissionsList,
      fetchingRegions: false,
      roles: null,
      locationOptions: null,
      selectedLanguage: null,
      selectedSoc: null,
      fields: [
        { key: 'title', sortable: true, thClass: 'w-75', label: this.$t("regions.list.table_header.region") },
        { key: 'actions', thClass: 'text-right', tdClass: 'text-right', label: this.$t("table_headers.actions") }
      ],
      showModal: false,
      names: {},
      descriptions: {},
      languages,
      regionToEdit: null
    }
  },
  watch: {
    selectedSoc: {
      async handler (val, oldVal) {
        this.fetchingRegions = true
        try {
          await this.$store.dispatch('content/fetchOrganisationSingle', val.countryCode)
          await this.$store.dispatch('content/fetchContent', val.countryCode)
          await this.$store.dispatch('content/fetchRegions', val.countryCode) // TODO update to country code
        } catch (err) {
          // Handle error?
        } finally {
          this.fetchingRegions = false
        }

      },
      deep: true
    },
    currentLanguages: {
      handler (val, oldVal) {
        if (val.length > 0) {
          this.selectedLanguage = val[0]
        }
      }
    },
  },
  mounted () {
    this.fetchOrganisations()
  },
  metaInfo () {
    return { title: this.$t('regions.list.manage') }
  },
  methods: {
    setLocalStorage () {
      if (this.selectedSoc){
        localStorage.setItem('soc', this.selectedSoc.countryCode)
      } else {
        localStorage.removeItem('soc')
      }
    },
    async fetchOrganisations () {
      await this.$store.dispatch('content/fetchOrganisations')
      this.selectedSoc = this.currentOrganisation
    },
    resetModal() {
      this.names = {}
      this.descriptions = {}
      this.regionToEdit = false
      this.$refs['modal'].hide()
    },
    handleOk(bvModalEvent) {
        // Prevent modal from closing
        bvModalEvent.preventDefault()
        // Trigger submit handler
        this.$refs.form.submit()
    },
    editRegion(region) {
      this.resetModal();
      this.regionToEdit = region
      const langs = Object.keys(region.translations);
      langs.forEach(lang => {
        this.names[lang] = region.translations[lang].title
        this.descriptions[lang] = region.translations[lang].description
      })
      this.$refs['modal'].show()
      this.selectedLanguage = langs[0]

    },
    async handleSubmit() {
      const isNew = true
      // Create new
      this.fetchingRegions = true

      try {
        const translations = {}

        this.currentLanguages.forEach((lang) => {
          translations[lang] = {
            title: this.names[lang],
            description: this.descriptions[lang]
          }
        })

        let payload = {
          countryCode: this.selectedSoc.countryCode,
          title: Object.values(this.names)[0],
          translations
        };

        if (this.isEdit) {
          await axios.put(`/api/regions/region/${this.regionToEdit.id}`, {
            ...this.regionToEdit,
            translations: payload.translations,
            countryCode: this.selectedSoc.countryCode,
            id: undefined
          })
        } else {
          await axios.post(`/api/regions`, payload)
        }
        await this.$store.dispatch('content/fetchRegions', this.selectedSoc.countryCode)
        // Hide the modal manually
        this.$fireGTEvent(this.$gtagEvents.CreateRegion)
        this.resetModal();
      } catch (err) {
        // Validation?
        console.log(err);
      } finally {
        this.fetchingRegions = false;
      }
    },
    deleteRegion(id) {
      swal({
        title: this.$t('common.are_you_sure'),
        text: `${this.$t('regions.delete')}`,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: this.$t('common.delete')
      }).then(async () => {
        try {
          await axios.delete(`/api/regions/region/${id}`)
          await this.$store.dispatch('content/fetchRegions', this.selectedSoc.countryCode) // TODO update to country code
          this.$noty.success(this.$t('common.removed'))
        } catch (e) {
          this.$noty.error(this.$t('error_alert_text'))
        }
      }).catch(swal.noop)
    }
  },
  computed: {
    currentPage: {
      // TODO - update for regions
      set: function(newVal) {
        this.$store.dispatch('users/setCurrentPage', newVal)
      },
      get: function() {
        return this.$store.state.users.currentPage
      }
    },
    orderBy: {
      set: function(newVal) {
        this.$store.dispatch('users/setOrderBy', newVal)
      },
      get: function() {
        return this.$store.state.users.orderBy
      }
    },
    sortDesc: {
      set: function(newVal) {
        this.$store.dispatch('users/setSortDesc', newVal)
      },
      get: function() {
        return this.$store.state.users.sortDesc
      }
    },
    isEdit() {
      return Boolean(this.regionToEdit)
    },
    ...mapGetters({
      user: 'auth/user',
      societies: 'content/organisations',
      regions: 'content/regionsArray',
      currentLanguages: 'content/currentLanguages',
      cmsLocale: 'lang/locale',
      currentOrganisation: 'content/currentOrganisation',
    })
  }
}
</script>
