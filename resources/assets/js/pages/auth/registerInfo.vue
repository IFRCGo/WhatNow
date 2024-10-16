<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-8 m-auto">
        <h1 class="text-center mb-5">
          {{ $t('register_form.heading') }}
        </h1>
      </div>

      <div class="col-lg-6 m-auto">

        <h4 class="text-center mb-5 mt-2">
          {{ $t('register_form.welcome', {name: this.user.data.user_profile.first_name }) }}
        </h4>

        <form @submit.prevent="update" @keydown="form.onKeydown($event)">

          <!-- Location -->
          <div class="form-group">
            <label class="text-uppercase font-weight-bold styled-label">{{ $t('register_form.location') }}</label>
            <b-form-select id="country_code" name="country_code" v-model="form.country_code"
                           :options="locationOptions" text-field="name" value-field="code"
                           class="styled-input"
                           :class="{ 'is-invalid': form.errors.has('country_code') }">
              <template slot="first">
                <option :value="null" disabled>{{ $t('register_form.select_country') }}</option>
              </template>
            </b-form-select>
            <has-error :form="form" field="country_code" class="pl-2 font-italic"></has-error>
          </div>

          <!-- Organisation -->
          <div class="form-group">
            <label class="text-uppercase font-weight-bold styled-label">{{ $t('register_form.organisation') }}</label>
            <input v-model="form.organisation" type="text" name="organisation"
                   class="form-control styled-input"
                   :class="{ 'is-invalid': form.errors.has('organisation') }">
            <has-error :form="form" field="organisation" class="pl-2 font-italic"></has-error>
          </div>

          <!-- Industry type -->
          <div class="form-group">
            <label class="text-uppercase font-weight-bold styled-label">{{ $t('register_form.industry') }}</label>
            <b-form-select id="industry_type" name="industry_type" v-model="form.industry_type"
                           :options="industryOptions" class="styled-input"
                           :class="{ 'is-invalid': form.errors.has('industry_type') }">
              <template slot="first">
                <option :value="null" disabled>{{ $t('register_form.select_industry') }}</option>
              </template>
            </b-form-select>
            <has-error :form="form" field="industry_type" class="pl-2 font-italic"></has-error>
          </div>

          <p class="text-center">
            {{ $t('register_form.sign_up_agreement') }}

            <span v-b-modal.termsModal @click="showTerms=true" class="underlined-link">
              {{ $t('register_form.terms_conditions') }}
            </span>

            <b-modal v-model="showTerms" id="termsModal" size="lg" ok-variant="dark">
              <terms></terms>

              <div slot="modal-footer" class="w-100">
                <b-btn size="sm" class="float-right" variant="dark" @click="showTerms=false">
                  Close
                </b-btn>
              </div>
            </b-modal>
          </p>

          <div class="form-group">
            <!-- Submit Button -->
            <v-button :loading="form.busy" class="btn-dark w-100">
              {{ $t('register_form.create_account') }}
            </v-button>
          </div>
        </form>
      </div>

    </div>
  </div>
</template>

<script>
import Form from 'vform'
import Terms from '~/pages/terms/index'
import { mapGetters } from 'vuex'

export default {
  components: {
    Terms
  },
  metaInfo() {
    return {title: this.$t('register')}
  },
  data() {
    return {
      showTerms: false,
      locationSelected: null,
      locationOptions: null,
      industrySelected: null,
      industryOptions: [
        { value: 'Media', text: this.$t('register_form.industries.media') },
        { value: 'Emergency Management', text: this.$t('register_form.industries.emergency') },
        { value: 'Non-Profit/NGO', text: this.$t('register_form.industries.non_profit') },
        { value: 'Humanitarian Organization', text: this.$t('register_form.industries.humanitarian') },
        { value: 'Red Cross/Red Crescent', text: this.$t('register_form.industries.red_cross') },
        { value: 'Education/Academia', text: this.$t('register_form.industries.education') },
        { value: 'Government Agency', text: this.$t('register_form.industries.gov') },
        { value: 'Other', text: this.$t('register_form.industries.other') }
      ],
      form: new Form({
        country_code: '',
        organisation: '',
        industry_type: ''
      })
    }
  },

  methods: {
    async update() {
      // Register the user.
      const { data } = await this.form.patch(`/api/users/${this.user.data.id}`)

      if (data) {
        this.$store.dispatch('auth/updateUser', {user: data})
        // Redirect home.
        this.$router.push({ name: 'home' })
      }
    }
  },

  created () {
    const countries = require('country-list')()
    this.locationOptions = countries.getData()
  },

  computed: {
    ...mapGetters({
      user: 'auth/user'
    })
  }
}
</script>
