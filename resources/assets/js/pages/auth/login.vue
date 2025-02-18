<template>
  <div class="container-fluid">
    <div class="row w-100">
      <card class="login-container">
        <form @submit.prevent="login" @keydown="form.onKeydown($event)">

          <h1 class="text-center mb-5">{{ $t('login') }}</h1>

          <b-alert show variant="success" v-if="confirmed">{{ $t('email_confirmed')}}</b-alert>
          <b-alert show variant="warning" v-if="confirmFailed">{{ $t('confirmation_failed')}}</b-alert>

          <div class="form-group">
            <label class="styled-label-signup styled-label" for="email">
              {{ $t('email') }}
              <span class="is-required"></span>
            </label>
            <input v-model="form.email" type="email" name="email" class="form-control styled-input"
                   :class="{ 'is-invalid': form.errors.has('email') }">
            <has-error :form="form" field="email" class="pl-2 font-italic"></has-error>
          </div>

          <div class="form-group">
            <label class="styled-label-signup styled-label" for="password">
              {{ $t('password') }}
              <span class="is-required"></span>
            </label>
            <div class="input-group-password">
              <input v-model="form.password" :type="showPassword ? 'text' : 'password'" name="password" class="form-control styled-input"
                     :class="{ 'is-invalid': form.errors.has('password') }">
              <div class="input-group-append showpassword" @click="showPassword = !showPassword">
                <div v-if="showPassword" :key="1">
                  <i class="fas fa-eye"></i>
                </div>
                <div v-else :key="2">
                  <i class="fas fa-eye-slash"></i>
                </div>
              </div>
            </div>
            <has-error :form="form" field="password" class="pl-2 font-italic"></has-error>
          </div>

          <div class="form-group text-right">
            <router-link class="underlined-link" :to="{ name: 'password.request' }">
              {{ $t('forgot_password') }}
            </router-link>
          </div>

          <div class="form-group mt-5 mb-5">
            <v-button :loading="form.busy" class="btn-dark w-50 mx-auto d-block">
              {{ $t('login') }}
            </v-button>
          </div>

        </form>
      </card>
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
    }),
    showPassword: false
  }),

  methods: {
    async login () {
      const { data } = await this.form.post('/api/login')

      this.$store.dispatch('auth/saveToken', {
        token: data.token
      })

      await this.$store.dispatch('auth/fetchUser')

      localStorage.removeItem('soc')
      localStorage.removeItem('lang')

      if (PreviousRoute.hasRoute) {
        this.$router.push(PreviousRoute.route)
      } else {
        this.$router.push({ name: 'home' })
      }
    }
  }
}
</script>

<style scoped>
.login-container {
  width: 100%;
  max-width: 769px;
  height: auto;
  margin: 10px auto;
  padding: 20px;
  border-radius: 20px;
  background-color: #f7f7f7;
}
.container-fluid {
  background-color: #ffffff;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

.styled-label-signup {
  font-size: 20px;
  font-weight: 300;
  line-height: 1.7;
}

.styled-input {
  padding: 13px;
  letter-spacing: 0.4px;
  height: 45px;
}

.is-required {
  color: red;
  margin-left: 5px;
}

.input-group-password {
  position: relative;
}

.input-group-append {
  position: absolute;
  right: 20px;
  top: 12.5px;
  font-size: 14px;
  color: #999;
}

.input-group-append.showpassword {
  cursor: pointer;
}

input:-webkit-autofill {
  -webkit-box-shadow: 0 0 0 30px #d3d3d3 inset;
  -webkit-text-fill-color: #000;
}

@media (min-width: 768px) {
  .login-container {
    padding: 57px 98px 82px 96px;
  }
}
</style>
