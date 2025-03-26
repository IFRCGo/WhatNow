  <template>
    <b-container fluid>
      <page-banner>
        <b-col cols="12">
          <h1 class="sec-title">{{ $t('subnationals.list.manage') }}</h1>
        </b-col>
        <b-col cols="12">
          <div class="input-section">
                <selectSociety
                  class="float-right s-soc"
                  :selected.sync="selectedSoc"
                  :staynull="true"
                />
          </div>
        </b-col>
      </page-banner>
      <b-row class="pb-2 px-4 pt-4 bg-white" align-v="center">
        <b-col>
          <b-button v-if="selectedSoc" class="float-right btn-outline-primary" v-b-modal.modal-create>
            {{ $t('subnationals.list.add') }}
            <fa :icon="['fas', 'plus']" />
          </b-button>
          <p class="text-right" v-else>{{ $t('subnationals.select_soc') }}</p>
        </b-col>
      </b-row>
      <b-row class="p-4 bg-white">
        <b-col class="pl-0 pr-0">
          <table-loading :loading="fetchingRegions"></table-loading>
          <div class="table-responsive">
            <b-table hover
              :items="subnationals || []"
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
                <b-button class="mb-1 btn-outline-primary" @click="() => editRegion(data.item)">{{ $t('common.edit') }}</b-button>
                <b-button class="mb-1 btn-outline-primary" @click="() => deleteRegion(data.item.id)">{{ data.item.activated ? $t('common.deactivate') : $t('common.delete') }}</b-button>
              </template>
            </b-table>
          </div>
          <p v-if="subnationals.length === 0 && !fetchingRegions">
            {{ $t('subnationals.list.empty') }}
          </p>
        </b-col>
      </b-row>
      <b-modal
        id="modal-create"
        size="lg"
        centered
        hide-footer
        ref="modal"
        :title="$t(isEdit ? 'subnationals.list.edit' : 'subnationals.list.create')"
      >
        <form ref="form" @submit.prevent="handleSubmit" class="px-3">
          <b-form-group
            :label="$t('subnationals.form.name')"
            label-for="name-input"
            class="add-subnational-label"
          >
            <b-form-input
              id="name-input"
              v-model="names[selectedLanguage]"
              class="new-subnational-input"
              required
              aria-describedby="name-input-help"
            ></b-form-input>
            <b-form-text id="name-input-help">{{ $t('subnationals.form.name_help') }}</b-form-text>

          </b-form-group>
          <b-form-group
            :label="$t('subnationals.form.description')"
            label-for="description-input"
            class="add-subnational-label"
          >
            <b-form-input
              id="description-input"
              v-model="descriptions[selectedLanguage]"
              class="new-subnational-input"
              required
              aria-describedby="description-input-help"
            ></b-form-input>
            <b-form-text id="description-input-help">{{ $t('subnationals.form.description_help') }}</b-form-text>

          </b-form-group>
          <div class="modal-footer pb-0 px-0">
            <b-button type="submit" :disabled="fetchingRegions" class="btn btn-outline-primary ">
              <fa :icon="['fas', 'spinner']" spin v-show="fetchingRegions"/>
              <i class="fas fa-check"></i>
              {{ $t(isEdit ? 'update' : 'common.create') }}
            </b-button>
            <b-button type="button" class="btn btn-outline-primary" @click.prevent="resetModal">
              <i class="fas fa-times"></i>
              {{ $t('common.cancel') }}
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
        { key: 'title', sortable: true, thClass: 'w-75', label: this.$t("subnationals.list.table_header.subnational") },
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
    return { title: this.$t('subnationals.list.manage') }
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
    editRegion(subnational) {
      this.resetModal();
      this.regionToEdit = subnational
      const langs = Object.keys(subnational.translations);
      langs.forEach(lang => {
        this.names[lang] = subnational.translations[lang].title
        this.descriptions[lang] = subnational.translations[lang].description
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
          await axios.put(`/api/subnationals/subnational/${this.regionToEdit.id}`, {
            ...this.regionToEdit,
            translations: payload.translations,
            countryCode: this.selectedSoc.countryCode,
            id: undefined
          })
        } else {
          await axios.post(`/api/subnationals`, payload)
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
        text: `${this.$t('subnationals.delete')}`,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: this.$t('common.delete')
      }).then(async () => {
        try {
          await axios.delete(`/api/subnationals/subnational/${id}`)
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
      // TODO - update for subnationals
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
      subnationals: 'content/regionsArray',
      currentLanguages: 'content/currentLanguages',
      cmsLocale: 'lang/locale',
      currentOrganisation: 'content/currentOrganisation',
    })
  }
}
</script>
  <style scoped>
  .input-section {
    background: #F7F7F7;
    border-radius: 10px;
    padding: 1.4rem;
    display: flex;
    justify-content: flex-start;
  }
  .s-soc {
    width: 25%!important;
  }
  .line-head {
    width: 100%;
    height: 1px;
    background: black;
    margin-bottom: 1rem;
    margin-top: -1rem;
  }
  .new-subnational-input{
    background: #F7F7F7;
    line-height: 1rem;
    border: none;
  }
  .add-subnational-label {
    font-size: 1rem;
  }
  </style>
