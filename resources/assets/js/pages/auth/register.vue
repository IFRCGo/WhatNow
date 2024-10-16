<template>
  <div class="container-fluid register-form-container pb-5 h-100">
    <div class="row">
      <div class="col-lg-12 m-auto">
        <h1 class="text-center mt-3 mb-5">
          {{ $t('register_form.heading') }}
        </h1>
      </div>
    </div>

    <div class="row m-auto">
      <card class="col-md-8 col-xl-6 m-auto bg-grey pb-2">
        <div class="row">
          <div class="col-lg-11 m-auto">
            <card class="mt-4 mb-4 p-sm-2 p-md-3">
              <h2 class="u-text-medium text-uppercase">
                {{ $t('register_form.who_are_you') }}
              </h2>
              <hr />
              <div class="d-lg-flex justify-content-around">
                <div>
                  <input class="c-radio-checkbox__input visually-hidden"
                         type="radio"
                         :id="registerTypes.DEVELOPER"
                         name="type"
                         :value="registerTypes.DEVELOPER"
                         v-model="registerType" />
                  <label :for="registerTypes.DEVELOPER"
                          class="text-uppercase font-weight-bold c-radio-checkbox__label u-text-normal u-text-spaced">
                    {{ $t('register_form.developer') }}
                  </label>
                </div>
                <div>
                  <input class="c-radio-checkbox__input visually-hidden"
                         type="radio"
                         :id="registerTypes.NATIONAL_SOCIETY"
                         name="type"
                         :value="registerTypes.NATIONAL_SOCIETY"
                         v-model="registerType" />
                  <label :for="registerTypes.NATIONAL_SOCIETY"
                          class="text-uppercase font-weight-bold c-radio-checkbox__label u-text-normal u-text-spaced">
                    {{ $t('register_form.national_society') }}
                  </label>
                </div>
              </div>
            </card>
          </div>
        </div>

        <div class="row test-register" v-if="registerType === registerTypes.DEVELOPER">
          <div class="col-lg-11 m-auto">
            <h4 class="text-center mb-3 u-text-medium">
              {{ $t('register_form.sign_up_with') }}
            </h4>
            <p class="text-center u-text-normal">
              {{ $t('register_form.sign_up_agreement') }}

              <span v-b-modal.termsModal @click="showTerms = true" class="underlined-link">
                {{ $t('register_form.terms_conditions') }}
              </span>
            </p>

            <div class="d-flex flex-wrap align-items-center justify-content-center">
              <!-- todo: update to use social login as part of another story -->
              <b-button size="lg" @click="fbLogin" variant="secondary" class="btn-facebook m-2">
                {{ $t('register_form.facebook') }}
              </b-button>

              <google-sign-in-button @click.native="googleLogin" class="m-2"></google-sign-in-button>
            </div>

            <hr />

            <h4 class="text-center mb-5 mt-2 u-text-medium">
              {{ $t('register_form.email_sign_up') }}
            </h4>

            <form @submit.prevent="register" @keydown="form.onKeydown($event)">
              <!-- Name -->
              <div class="form-group mb-4">
                <label class="text-uppercase font-weight-bold styled-label" for="name">{{ $t('first_name') }}</label>
                <input v-model="form.first_name" type="text" name="name" class="form-control styled-input" id="name"
                  :class="{ 'is-invalid': form.errors.has('first_name') }" v-bind:placeholder="$t('first_name')">
                <has-error :form="form" field="first_name" class="pl-2 font-italic"></has-error>
              </div>

              <!-- last name -->
              <div class="form-group mb-4">
                <label class="text-uppercase font-weight-bold styled-label" for="last_name">{{ $t('register_form.last_name') }}</label>
                <input v-model="form.last_name" type="text" name="last_name" class="form-control styled-input" id="last_name"
                :class="{ 'is-invalid': form.errors.has('last_name') }" v-bind:placeholder="$t('register_form.last_name')">
                <has-error :form="form" field="last_name" class="pl-2 font-italic"></has-error>
              </div>

              <!-- Location -->
              <div class="form-group mb-4">
                <label class="text-uppercase font-weight-bold styled-label" for="country_code">{{ $t('register_form.location') }}</label>
                <b-form-select id="country_code" name="country_code" v-model="form.country_code" :options="locationOptions" text-field="name" value-field="code" class="styled-input"
                :class="{ 'is-invalid': form.errors.has('country_code'), 'is-placeholder': form.country_code == null }">
                  <template slot="first">
                    <option :value="null" disabled>{{ $t('register_form.select_country') }}</option>
                  </template>
                </b-form-select>
                <has-error :form="form" field="country_code" class="pl-2 font-italic"></has-error>
              </div>

              <!-- Organisation -->
              <div class="form-group mb-4">
                <label class="text-uppercase font-weight-bold styled-label" for="organisation">{{ $t('register_form.organisation') }}</label>
                <input v-model="form.organisation" type="text" name="organisation" id="organisation" class="form-control styled-input"
                :class="{ 'is-invalid': form.errors.has('organisation') }" v-bind:placeholder="$t('register_form.organisation')">
                <has-error :form="form" field="organisation" class="pl-2 font-italic"></has-error>
              </div>

              <!-- Industry type -->
              <div class="form-group mb-4">
                <label class="text-uppercase font-weight-bold styled-label" for="industry_type">{{ $t('register_form.industry') }}</label>
                <b-form-select id="industry_type" name="industry_type" v-model="form.industry_type" :options="industryOptions" class="styled-input"
                :class="{ 'is-invalid': form.errors.has('industry_type'), 'is-placeholder': form.select_industry == null }">
                  <template slot="first">
                    <option :value="null" disabled>{{ $t('register_form.select_industry') }}</option>
                  </template>
                </b-form-select>
                <has-error :form="form" field="industry_type" class="pl-2 font-italic"></has-error>
              </div>

              <!-- Email -->
              <div class="form-group mb-4">
                <label class="text-uppercase font-weight-bold styled-label" for="email">{{ $t('email') }}</label>
                <input autocomplete="email" v-model="form.email" id="email" type="email" name="email" class="form-control styled-input"
                  :class="{ 'is-invalid': form.errors.has('email') }" v-bind:placeholder="$t('email')">
                <has-error :form="form" field="email" class="pl-2 font-italic"></has-error>
              </div>

              <!-- Api used in -->
              <div class="form-group mb-4">
                <label class="text-uppercase font-weight-bold styled-label" for="api_used_in">{{ $t('register_form.api_used_in') }}</label>
                <textarea v-model="form.api_used_in" name="api_used_in" id="api_used_in" class="form-control styled-input"
                :class="{ 'is-invalid': form.errors.has('api_used_in') }" v-bind:placeholder="$t('register_form.api_used_in_help')" />
                <has-error :form="form" field="api_used_in" class="pl-2 font-italic"></has-error>
              </div>

              <!-- Password -->
              <div class="form-group mb-4">
                <label class="text-uppercase font-weight-bold styled-label" for="password">{{ $t('password') }}</label>
                <input autocomplete="new-password" v-model="form.password" type="password" id="password" name="password" class="form-control styled-input"
                  :class="{ 'is-invalid': form.errors.has('password') }" v-bind:placeholder="$t('password')">
                <has-error :form="form" field="password" class="pl-2 font-italic"></has-error>
              </div>

              <!-- Password Confirmation -->
              <div class="form-group mb-4">
                <label class="text-uppercase font-weight-bold styled-label" for="password_confirmation">{{ $t('confirm_password') }}</label>
                <input autocomplete="new-password" v-model="form.password_confirmation" id="password_confirmation" type="password" name="password_confirmation" class="form-control styled-input"
                  :class="{ 'is-invalid': form.errors.has('password_confirmation') }" v-bind:placeholder="$t('confirm_password')">
                <has-error :form="form" field="password_confirmation" class="pl-2 font-italic"></has-error>
              </div>

              <p class="text-center u-text-normal">
                {{ $t('register_form.sign_up_agreement') }}

                <span v-b-modal.termsModal @click="showTerms = true" class="underlined-link">
                  {{ $t('register_form.terms_conditions') }}
                </span>

                <b-modal v-model="showTerms" id="termsModal" size="lg" ok-variant="dark">
                  <latest-terms class="text-left"></latest-terms>
                  <div slot="modal-footer" class="w-100">
                    <b-btn size="sm" class="float-right" variant="primary" @click="showTerms = false">
                      {{ $t('close') }}
                    </b-btn>
                  </div>
                </b-modal>
              </p>

              <div class="form-group mb-5">
                  <!-- Submit Button -->
                  <v-button :loading="form.busy" class="btn-dark btn-lg w-100">
                    {{ $t('register_form.create_account') }}
                  </v-button>
              </div>
            </form>
          </div>
        </div>
        <div class="text-center" v-else>
          <b-link href="mailto:gdpc@redcross.org?Subject=WhatNow%20Service%20Inquiry"
            class="btn m-2 btn-dark btn-lg font-weight-bold register-form__contact-gdpc">
            {{ $t('register_form.contact_gdpc') }}
          </b-link>
        </div>
      </card>
    </div>
    <div class="row mt-3">
      <div class="col-sm-5 m-auto">
        <p class="text-center u-text-medium">
          {{registerType === registerTypes.DEVELOPER ? $t('register_form.gdpc_note') : $t('register_form.gdpc_ns_note') }}
           <b-link class="underlined-link" href="mailto:gdpc@redcross.org?Subject=WhatNow%20Portal%20Access">gdpc@redcross.org</b-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import Form from 'vform'
import swal from 'sweetalert2'
import Terms from '~/pages/terms/index'
import Vue from 'vue'
import { mapGetters } from 'vuex'
import LatestTerms from '~/components/LatestTerms'
import GoogleSignInButton from '~/components/global/GoogleSignInButton'

const registerTypes = {
  DEVELOPER: 'developer',
  NATIONAL_SOCIETY: 'nationalSociety'
}

export default {
  components: {
    Terms,
    LatestTerms,
    GoogleSignInButton
  },
  props: {
    userType: String
  },
  metaInfo () {
    return { title: this.$t('register') }
  },
  data () {
    return {
      registerType: this.userType ? this.userType : registerTypes.DEVELOPER,
      registerTypes,
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
        first_name: '',
        last_name: '',
        email: '',
        country_code: null,
        organisation: '',
        industry_type: null,
        password: '',
        password_confirmation: '',
        api_used_in: ''
      })
    }
  },

  methods: {
    async register () {
      // Register the user.
      const { data } = await this.form.post('/api/register')

      if (data.created_at) {
        swal({
          title: this.$t('register_success'),
          text: this.$t('confirm_email'),
          type: 'success',
          confirmButtonColor: '#3085d6',
          confirmButtonText: this.$t('home_redirect')
        }).then((result) => {
          this.$router.push({ name: 'welcome' })
          this.$fireGTEvent(this.$gtagEvents.SignUp)
        }).catch(swal.noop)
      }
    },
    fbLogin () {
      FB.login(async (response) => {
        if (response.status === 'connected') {
          await this.$store.dispatch('auth/loginWithFacebook', {
            token: response.authResponse.accessToken
          })

          await this.handleSocialLogin()
        } else {
          return false
        }
      }, { scope: 'public_profile,email' })
    },
    googleLogin () {
      Vue.googleAuth().directAccess()
      Vue.googleAuth().signIn(async (user) => {
        await this.$store.dispatch('auth/loginWithGoogle', {
          token: user.getAuthResponse().id_token
        })

        await this.handleSocialLogin()
      })
    },
    async handleSocialLogin () {
      await this.$store.dispatch('auth/fetchUser')

      if (!this.user.data.user_profile.industry_type) {
        this.$router.push({ name: 'register-info' })
      } else {
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
