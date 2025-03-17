<template>
  <div class="container-fluid register-form-container pb-5 h-100">
    <div class="row">
      <div class="col-lg-12 register-card my-4">
        <h1 class="text-center mt-3 register_form_heading">
          <span class="register_form_heading--active">{{$t('register_form.heading_2')}}</span>
          {{$t('register_form.heading_3')}}
        </h1>

        <div class="what-is-your-role">
          <p class="bottom-line">
            {{$t('register_form.subtitle_1')}}
          </p>
          <hr>
          <p class="bottom-line">
            {{$t('register_form.subtitle_2')}}
          </p>
          <hr>
          <p>
            {{$t('register_form.subtitle_3')}}
          </p>
        </div>
      </div>

      <div class="col-12 register-card my-4 register-card--form">
        <div>
          <h3 class="mb-5">{{ $t('register_form.email_sign_up') }}</h3>
          <form @submit.prevent="register" @keydown="form.onKeydown($event)">
            <div class="row justify-content-between">
              <div class="col-lg-6 col-md-6 col-sm-12">
                <!-- Name -->
                <div class="form-group register-input-container pr-lg-4 pr-md-4">
                  <label class="styled-label-signup styled-label" for="name">
                    {{ $t('first_name') }}
                    <span class="is-required"></span>
                  </label>
                  <input v-model="form.first_name" type="text" name="name" class="form-control styled-input" id="name"
                        :class="{ 'is-invalid': form.errors.has('first_name') }" >
                  <has-error :form="form" field="first_name" class="pl-2 font-italic"></has-error>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-12">
                <!-- last name -->
                <div class="form-group register-input-container pl-lg-4 pl-md-4">
                  <label class="styled-label-signup styled-label" for="last_name">
                    {{ $t('register_form.last_name') }}
                    <span class="is-required"></span>
                  </label>
                  <input v-model="form.last_name" type="text" name="last_name" class="form-control styled-input"
                        id="last_name"
                        :class="{ 'is-invalid': form.errors.has('last_name') }">
                  <has-error :form="form" field="last_name" class="pl-2 font-italic"></has-error>
                </div>
              </div>

              <div class="col-12">
                <!-- Email -->
                  <div class="form-group register-input-container">
                    <label class="styled-label-signup styled-label" for="email">
                      {{ $t('register_form.mail') }}
                      <span class="is-required"></span>
                  </label>
                  <input autocomplete="email" v-model="form.email" id="email" type="email" name="email"
                        class="form-control styled-input"
                        :class="{ 'is-invalid': form.errors.has('email') }">
                  <has-error :form="form" field="email" class="pl-2 font-italic"></has-error>
                </div>
              </div>

              <div class="col-lg-6 col-md-6 col-sm-12">
                <!-- Password -->
                <div class="form-group register-input-container pr-lg-4 pr-md-4">
                  <label class="styled-label-signup styled-label" for="password">
                    {{ $t('password') }}
                    <span class="is-required"></span>
                  </label>
                  <div class="input-group-password">
                    <input autocomplete="new-password" v-model="form.password" :type="showPassword ? 'text' : 'password'" id="password" name="password"
                        class="form-control styled-input"
                        :class="{ 'is-invalid': form.errors.has('password') }">
                    </input>
                    <div class="input-group-append showpassword" @click="showPassword = !showPassword">
                      <div v-if="showPassword" :key="1">
                        <i class="fas fa-eye"></i>
                      </div>
                      <div  v-else :key="2">
                        <i class="fas fa-eye-slash"></i>
                      </div>
                    </div>
                  </div>

                  <has-error :form="form" field="password" class="pl-2 font-italic"></has-error>
                </div>
              </div>

              <div class="col-lg-6 col-md-6 col-sm-12">
                <!-- Password Confirmation -->
                <div class="form-group register-input-container pl-lg-4 pl-md-4">
                  <label class="styled-label-signup styled-label" for="password_confirmation">
                    {{ $t('confirm_password') }}
                    <span class="is-required"></span>
                  </label>
                  <div class="input-group-password">
                    <input autocomplete="new-password" v-model="form.password_confirmation" id="password_confirmation"
                        :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" class="form-control styled-input"
                        :class="{ 'is-invalid': form.errors.has('password_confirmation') }">
                    <div class="input-group-append showpassword" @click="showConfirmPassword = !showConfirmPassword">
                      <div v-if="showConfirmPassword" :key="1">
                        <i class="fas fa-eye"></i>
                      </div>
                      <div  v-else :key="2">
                        <i class="fas fa-eye-slash"></i>
                      </div>
                    </div>
                  </div>

                  <has-error :form="form" field="password_confirmation" class="pl-2 font-italic"></has-error>
                </div>
              </div>

              <div class="bottom-line mb-3"></div>

              <div class="col-lg-6 col-md-6 col-sm-12">
                <!-- Location -->
                <div class="form-group register-input-container pr-lg-4 pr-md-4">
                  <label class="styled-label-signup styled-label" for="country_code">
                    {{ $t('register_form.location') }}
                    <span class="is-required"></span>
                  </label>
                  <b-form-select id="country_code" name="country_code" v-model="form.country_code"
                                :options="locationOptions" text-field="name" value-field="code" class="styled-input custom-select"
                                :class="{ 'is-invalid': form.errors.has('country_code'), 'is-placeholder': form.country_code == null }">
                    <template slot="first">
                      <option :value="null" disabled>{{ $t('register_form.select_country') }}</option>
                    </template>
                  </b-form-select>
                  <has-error :form="form" field="country_code" class="pl-2 font-italic"></has-error>
                </div>
              </div>

              <hr>

              <div class="col-lg-6 col-md-6 col-sm-12">
                <!-- Organisation -->
                <div class="form-group register-input-container pl-lg-4 pl-md-4">
                  <label class="styled-label-signup styled-label" for="organisation">
                    {{ $t('register_form.organisation') }}
                    <span class="is-required"></span>
                  </label>
                  <input v-model="form.organisation" type="text" name="organisation" id="organisation"
                        class="form-control styled-input"
                        :class="{ 'is-invalid': form.errors.has('organisation') }">
                  <has-error :form="form" field="organisation" class="pl-2 font-italic"></has-error>
                </div>
              </div>

              <div class="col-lg-6 col-md-6 col-sm-12">
                <!-- Industry type -->
                <div class="form-group register-input-container pr-lg-4 pr-md-4">
                  <label class="styled-label-signup styled-label" for="industry_type">
                    {{ $t('register_form.industry') }}
                    <span class="is-required"></span>
                  </label>
                  <b-form-select id="industry_type" name="industry_type" v-model="form.industry_type"
                                :options="industryOptions" class="styled-input custom-select"
                                :class="{ 'is-invalid': form.errors.has('industry_type'), 'is-placeholder': form.select_industry == null }">
                    <template slot="first">
                      <option :value="null" disabled>{{ $t('register_form.select_industry') }}</option>
                    </template>
                  </b-form-select>
                  <has-error :form="form" field="industry_type" class="pl-2 font-italic"></has-error>
                </div>
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-9 m-auto ">
                <p class="text-center mb-0 bottom-text">
                  {{
                    $t('register_form.ifrc_note')
                  }}
                  <b-link class="underlined-link" href="mailto:im@ifrc.org">
                    {{ $t('register_form.email_ifrc') }}
                  </b-link>
                </p>
              </div>
            </div>

            <div class="register_form-bottom">
              <p class="text-center mt-5 mb-0 bottom-text">
                {{ $t('register_form.sign_up_agreement') }}
                <b-link :to="{ name: 'legal_terms', params: {} }" target="_blank" class="underlined-link font-weight-normal">
                  {{ $t('register_form.terms_conditions') }}
                </b-link>
              </p>
              <div class="form-group mb-5">
                <!-- Submit Button -->
                <v-button :loading="form.busy" :disabled="!isFormComplete" @click="checkTerms" class="btn-primary btn btn-lg w-100">
                  {{ $t('register_form.create_account') }}
                </v-button>
              </div>
            </div>
          </form>
        </div>

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
  data() {
    return {
      sawTerms: false,
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
      }),
      showPassword: false,
      showConfirmPassword: false
    }
  },

  methods: {
    async register() {
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
    async checkTerms() {
      this.sawTerms = true
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
    }),
    isFormComplete() {
      return this.form.first_name && this.form.last_name && this.form.email && this.form.password && this.form.password_confirmation && this.form.country_code && this.form.organisation && this.form.industry_type
    }
  }
}
</script>

<style scoped lang="scss">
@import '../../../sass/variables.scss';

.register-card {
  width: 90%;
  max-width: 1381px;
  padding: 62px 55px 56px 55px;
  border-radius: 20px;
  background: $card-solid-bg;
  margin: 0 auto;
}

.register-card--form form {
  width: 90%;
  max-width: 1090px;
  margin: 0 auto;
}

.register-card--form .register-input-container {
  margin-bottom: 33px;
}

.register_form_heading {
  font-size: 55px;
  font-weight: 600;
  color: $text-dark;
}

.register_form_heading--active {
  color: $text-primary;
}

.register_form-bottom {
  width: 100%;
  max-width: 652px;
  margin: 0 auto;
}

.register_form-bottom button {
  margin-top: 33px;
}

.what-is-your-role {
  width: 100%;
  margin-top: 52px;
}

.what-is-your-role p {
  text-align: center;
  color: $text-black;
  font-size: 20px;
  padding-bottom: 2rem;
}

.facebook-button {
  background-color: $text-white;
  border: $border-primary-3;
  border-radius: 25px;
  width: 100%;
  max-width: 315px;
  font-size: 20px;
  font-weight: 600;
  color: $text-primary;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 23px;
  letter-spacing: normal;
  height: 46px;
  &:focus {
    background-color: rgb(238, 238, 238);
  }

  svg {
    width: 23px;
    height: 23px;
    margin-left: 10px;
  }
}

.register-form-container {
  background: $text-white;
  font-family: Poppins, sans-serif;
}

.register-form-container hr {
  border-top: 1px solid $body-bg;
}

.register-form-container h1 {
  font-size: 55px;
  font-weight: 600;
  line-height: 1.33;
  letter-spacing: -0.55px;
  width: auto;
  margin: 0 auto;
  display: block;
}

.register-form-container h3 {
  font-size: 45px;
  font-weight: 600;
  letter-spacing: -0.45px;
  text-align: center;
  color: $text-dark;
}

.styled-label-signup {
  font-size: 20px;
  font-weight: 300;
  line-height: 1.7;
}

@media screen and (max-width: 767px) {
  .register-form-container h1 {
    font-size: 24px;
  }
}

.register-form-container h2 {
  font-size: 35px;
  font-weight: 600;
  line-height: 2.29;
  letter-spacing: -0.35px;
  text-align: center;
  color: $text-dark;
}

//hide show password
.input-group-password {
  position: relative;
}

.input-group-append {
  position: absolute;
  right: 20px;
  top: 12.5px;
  font-size: 14px;
  color: $icon-not-active;
}

.input-group-append.showpassword {
  cursor: pointer;
}

.bottom-line {
  width: 100%;
  border-bottom: 1px solid #CECECE;
}

.bottom-text {
  font-size: 18px;
}


@media screen and (min-width: 992px) {
  .register-form-container .col-lg-6 {
    max-width: 550px;
  }
}

.register-form-container .styled-input {
  padding: 13px;
  letter-spacing: 0.4px;
  height: 45px;
}
.register-form-container {
  transform: scale(0.7);
  transform-origin: top center;
}

</style>
