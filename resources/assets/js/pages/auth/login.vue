<template>
  <div class="container-fluid login-section">
    <div class="row w-100">
      <div class="col-lg-6 m-auto">
        <card>
          <form @submit.prevent="login" @keydown="form.onKeydown($event)">

            <img class="mx-auto d-block mb-5 img-fluid" :src="src('logoLarge')" :srcSet="srcSet('logoLarge')">

            <b-alert show variant="success" v-if="confirmed">{{ $t('email_confirmed')}}</b-alert>
            <b-alert show variant="warning" v-if="confirmFailed">{{ $t('confirmation_failed')}}</b-alert>

            <!-- Email -->
            <div class="form-group">
              <label class="text-uppercase font-weight-bold styled-label">{{ $t('email') }}</label>
              <input v-model="form.email" type="email" name="email" class="form-control"
                :class="{ 'is-invalid': form.errors.has('email') }">
              <has-error :form="form" field="email" class="pl-2 font-italic"></has-error>
            </div>

            <!-- Password -->
            <div class="form-group">
              <label class="text-uppercase font-weight-bold styled-label">{{ $t('password') }}</label>
              <input v-model="form.password" type="password" name="password" class="form-control"
                :class="{ 'is-invalid': form.errors.has('password') }">
              <has-error :form="form" field="password" class="pl-2 font-italic"></has-error>
            </div>

            <div class="form-group mt-5 mb-5">
              <!-- Submit Button -->
              <v-button :loading="form.busy" class="btn-dark w-100">
                {{ $t('login') }}
              </v-button>
            </div>

            <div class="form-group text-center">
              <router-link class="underlined-link" :to="{ name: 'password.request' }">
                {{ $t('forgot_password') }}
              </router-link>
            </div>
          </form>
        </card>
      </div>
    </div>
  </div>
</template>

<script>
import Form from 'vform'
import PreviousRoute from '~/utils/previousRoute'

export default {
  props: ['confirmed', 'confirmFailed'],
  metaInfo () {
    return { title: this.$t('login') }
  },

  data: () => ({
    form: new Form({
      email: '',
      password: ''
    })
  }),

  methods: {
    async login () {
      // Submit the form.
      const { data } = await this.form.post('/api/login')

      // Save the token.
      this.$store.dispatch('auth/saveToken', {
        token: data.token
      })

      // Fetch the user.
      await this.$store.dispatch('auth/fetchUser')

      // Reset Selections
      localStorage.removeItem('soc')
      localStorage.removeItem('lang')

      if (PreviousRoute.hasRoute) {
        this.$router.push(PreviousRoute.route)
      } else {
        // Redirect home.
        this.$router.push({ name: 'home' })
      }
    }
  }
}
</script>
