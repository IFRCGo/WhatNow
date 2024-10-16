<template>
  <b-container fluid>
    <b-row align-h="between" align-v="center" class="pl-4 pr-4 mb-1">
      <b-col md="12" xl="6">
        <h1>{{ $t('api_usage.applications') }}</h1>
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
            <p v-if="data.item.status != null">
              {{ data.item.status }}
            </p>
            <b-button variant="primary" v-if="data.item.status === null" size="sm" class="mb-1" @click="toggleDeactivate(data.item.status)">{{ $t('common.deactivate') }} </b-button>
            <b-button variant="secondary" v-if="data.item.status === 'App Deactivated'" size="sm" class="mb-1" :to="{ name: 'users.edit', params: {} }"> {{ $t('api_usage.view_user') }} </b-button>
            <b-button variant="secondary" v-if="data.item.status === 'User Deactivated'" size="sm" class="mb-1" @click="toggleDeactivate(data.item.status)">{{ $t('api_usage.reactivate') }} </b-button>
          </template>
        </b-table>

      </b-col>
    </b-row>

  </b-container>
</template>

<script>
import swal from 'sweetalert2'

const items = [
{ application: 'P.E Earthquake Tracker Map', creator_name: 'First name', creator_surname: 'Last name', industry_type: 'Industry name', location: 'Location', created: '12/12/2015', last_changed: '3 days ago', status: 'App Deactivated' },
{ application: 'P.E Earthquake Tracker Map', creator_name: 'First name', creator_surname: 'Last name', industry_type: 'Industry name', location: 'Location', created: '12/12/2015', last_changed: '3 days ago', status: 'User Deactivated' },
{ application: 'P.E Earthquake Tracker Map', creator_name: 'First name', creator_surname: 'Last name', industry_type: 'Industry name', location: 'Location', created: '12/12/2015', last_changed: '3 days ago', status: null },
]
const fields = ['application',
                { key: 'creator_name', sortable: true, tdClass: 'align-middle' },
                { key: 'creator_surname', sortable: true, tdClass: 'align-middle' },
                { key: 'industry_type', sortable: true, tdClass: 'align-middle' },
                { key: 'location', sortable: false, tdClass: 'align-middle' },
                { key: 'created', sortable: true, tdClass: 'align-middle' },
                { key: 'last_changed', sortable: true, tdClass: 'align-middle' },
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
