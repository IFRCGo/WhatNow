<template>
  <b-container fluid>
    <b-row align-h="between" align-v="center" class="pl-4 pr-4 mb-1">
      <b-col md="12" xl="6">
        <h1>{{ $t('api_usage.api_users') }}</h1>
      </b-col>
    </b-row>

    <b-row class="mt-4 mb-4 pb-0 pl-4 pr-4 pt-4 pb-4 bg-white">
      <b-col>
        <label for="roles-permissions" class="visually-hidden">{{ $t('users.edit.roles_label') }}</label>
        <b-form-select id="roles-permissions" name="roles-permissions" v-model="selected" :options="options" class="mb-3"></b-form-select>
        <b-table hover responsive :items="items" :fields="fields">
          <!-- A virtual column -->
          <template #cell(application)="data">
            <router-link class="underlined-link" :to="{ name: 'api-usage.app_detail', params: {} }">
              {{ data.item.application }}
            </router-link>
          </template>
          <template #cell(actions)="data">
            <b-button variant="primary" size="sm" class="mb-1" @click="toggleDeactivate(data.item.status)">{{ $t('common.deactivate') }} </b-button>
          </template>
        </b-table>

      </b-col>
    </b-row>

  </b-container>
</template>

<script>
import swal from 'sweetalert2'

const items = [
{ creator_name: 'First name', creator_surname: 'Last name', organisation: "Planet Express Enterprises Inc", industry_type: 'Industry name', location: 'Location', apps: "2", signed_up: '12/12/2015', last_activity: '3 days ago', status: 'App Deactivated' },
{ creator_name: 'First name', creator_surname: 'Last name', organisation: "Planet Express Enterprises Inc", industry_type: 'Industry name', location: 'Location', apps: "4", signed_up: '12/12/2015', last_activity: '3 days ago', status: 'App Deactivated' },
{ creator_name: 'First name', creator_surname: 'Last name', organisation: "Planet Express Enterprises Inc", industry_type: 'Industry name', location: 'Location', apps: "7", signed_up: '12/12/2015', last_activity: '3 days ago', status: 'App Deactivated' },
]
const fields = [{ key: 'creator_name', sortable: true, tdClass: 'align-middle' },
                { key: 'creator_surname', sortable: true, tdClass: 'align-middle' },
                { key: 'organisation', sortable: false, tdClass: 'align-middle' },
                { key: 'industry_type', sortable: true, tdClass: 'align-middle' },
                { key: 'location', sortable: false, tdClass: 'align-middle' },
                { key: 'apps', sortable: false, tdClass: 'align-middle' },
                { key: 'signed_up', sortable: true, tdClass: 'align-middle' },
                { key: 'last_activity', sortable: true, tdClass: 'align-middle' },
                'actions' ]

export default {
  data () {
    return {
      selected: null,
      items: items,
      fields: fields,
      options: [
        { value: null, text: 'Filter' },
        { value: 'a', text: 'This is First option' },
        { value: 'b', text: 'Selected Option' },
        { value: {'C': '3PO'}, text: 'This is an option with object value' },
        { value: 'd', text: 'This one is disabled', disabled: true }
      ]
    }
  },
  methods: {
    toggleDeactivate (val) {
      let action = val === "User Deactivated" ? this.$t('common.activate') : this.$t('common.deactivate')
      swal({
        title: this.$t('common.are_you_sure'),
        text: `${action}`,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: action
      }).catch(swal.noop)
    }
  }
}
</script>
