<template>
  <div class="container-fluid login-section">
    <div class="row w-100">
      <div class="col-lg-6 m-auto">
        <card class="card-container">
          <h1 class="text-center mb-5">
            {{ isReset ? $t('reset_password') : $t('set_password') }}
          </h1>

          <form @submit.prevent="submit" @keydown="form.onKeydown($event)">

            <!-- Email -->
            <div class="form-group">
              <label class="styled-label-signup styled-label">{{ $t('email') }}</label>
              <input v-model="form.email" type="email" name="email" class="form-control styled-input"
                :class="{ 'is-invalid': form.errors.has('email') }" required>
              <has-error :form="form" field="email" class="pl-2 font-italic"></has-error>
            </div>

            <!-- Password -->
            <div class="form-group">
              <label class="styled-label-signup styled-label">{{ $t('password') }}</label>
              <input v-model="form.password" type="password" name="password" class="form-control styled-input"
                :class="{ 'is-invalid': form.errors.has('password') }" required>
              <has-error :form="form" field="password" class="pl-2 font-italic"></has-error>
            </div>

            <!-- Password Confirmation -->
            <div class="form-group">
              <label class="styled-label-signup styled-label">{{ $t('confirm_password') }}</label>
              <input v-model="form.password_confirmation" type="password" name="password_confirmation" class="form-control styled-input"
                :class="{ 'is-invalid': form.errors.has('password_confirmation') }" required>
              <has-error :form="form" field="password_confirmation" class="pl-2 font-italic"></has-error>
            </div>

            <!-- Submit Button -->
            <div class="form-group mt-4">
              <v-button :loading="form.busy" class="btn-dark w-50 mx-auto d-block">{{ isReset ? $t('reset_password') : $t('set_password') }}</v-button>
            </div>
          </form>
        </card>
      </div>
    </div>
  </div>
</template>

<script>
import Form from 'vform'

export default {
  props: ['isReset', 'token'],
  metaInfo () {
    return { title: this.$t('reset_password') }
  },

  beforeCreate () {
    this.$store.dispatch('auth/logout')
  },

  data: () => ({
    form: new Form({
      token: '',
      email: '',
      password: '',
      password_confirmation: ''
    })
  }),

  methods: {
    async submit () {
      this.form.token = this.token

      const { data } = await this.form.post('/api/password/reset')

      this.$noty.success(this.$t('password_updated'))
      this.$router.push({ name: 'login' })
    }
  }
}
</script>
<style scoped>
.card-container {
  width: 100%;
  max-width: 760px;
  height: auto;
  margin: 10px auto;
  padding: 20px;
  border-radius: 20px;
  background-color: #f7f7f7;
}

.styled-label {
  font-size: 20px;
}

.container-fluid {
  margin-top: 8rem;
  margin-bottom: 5rem;
}
</style>
