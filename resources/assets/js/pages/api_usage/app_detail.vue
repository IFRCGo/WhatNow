<template>
  <b-container fluid>
    <b-row align-h="between" align-v="center" class="pl-4 pr-4 mb-1">
      <b-col md="12" xl="6">
        <h1>{{ $t('api_usage.app_detail') }}</h1>
      </b-col>
    </b-row>

    <b-row class="mt-4 pb-0 pl-4 pr-4 pt-4 pb-4 bg-white">
      <b-col>
        <b-card :title="$t('api_usage.app_details')">

          <b-row class="mb-3 mt-4">
            <b-col cols="2">
              <b-img src="http://via.placeholder.com/42x42" rounded="circle" alt="" role="presentation"></b-img>
            </b-col>

            <b-col cols="10">
              <p>
                App name
                <b-button variant="primary" size="sm" class="float-right" @click="toggleDeactivate"> {{ $t('common.deactivate') }} </b-button>
              </p>
            </b-col>
          </b-row>

          <b-row class="mb-3">
            <b-col cols="2">
              <p>
                Description:
              </p>
            </b-col>

            <b-col cols="10">
              <p>
                App description
              </p>
            </b-col>
          </b-row>

          <b-row class="mb-3">
            <b-col cols="2">
              <p>
                API KEY:
              </p>
            </b-col>

            <b-col cols="10">
              <p>
                dj0yJmk9aVpCeXczUzVVUUp0JmQ9WVdrOVVYYzBTak5OTXpJbWNHbzlNQS0tJnM9Y29uc3VtZXJzZWNyZXQmeD0yZA
              </p>
            </b-col>
          </b-row>

          <b-row class="mb-3">
            <b-col cols="2">
              <p>
                Created By:
              </p>
            </b-col>

            <b-col cols="10">
              <p>
                <b-img src="http://via.placeholder.com/42x42" rounded="circle"  alt="" role="presentation"></b-img>
                Jimmy Jimbo
              </p>
            </b-col>
          </b-row>

          <b-row class="mb-3">
            <b-col>
              <p>
                Created: 12/12/17
              </p>
            </b-col>
          </b-row>
        </b-card>
      </b-col>
    </b-row>

    <b-row class="pb-0 pl-4 pr-4 pt-4 pb-4 bg-white">
      <b-col>
        <b-card :title="$t('api_usage.activity')">
          <b-table hover responsive :items="items" :fields="fields" class="mt-4">
            <template #cell(user)="data">
              {{ data.item.creator_name }} {{ data.item.creator_surname }}
            </template>
          </b-table>

          <p @click="viewAllPopup">
            {{ $t('api_usage.view_all_activity') }}
          </p>
        </b-card>
      </b-col>
    </b-row>

  </b-container>
</template>

<script>
import swal from 'sweetalert2'

const items = [
{ action: 'Changed Description', creator_name: 'First name', creator_surname: 'Last name', date: '12/12/2015' },
{ action: 'App Reactivated', creator_name: 'First name', creator_surname: 'Last name', date: '12/12/2015' },
{ action: 'Renamed App', creator_name: 'First name', creator_surname: 'Last name', date: '12/12/2015' },
]
const fields = [{ key: 'action', sortable: false, tdClass: 'align-middle' },
                'user',
                { key: 'date', sortable: false, tdClass: 'align-middle' }]

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
    viewAllPopup (item) {
      // todo: list all of activity below
      swal({
        title: 'All activity',
        text: 'List of activity',
        confirmButtonText: 'Close'
      }).catch(swal.noop)
    },
    toggleDeactivate (item) {
      swal({
        title: this.$t('common.are_you_sure'),
        text: 'Deactivate this app', // todo: add app name
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Deactivate'
      }).catch(swal.noop)
    }
  }
}
</script>
