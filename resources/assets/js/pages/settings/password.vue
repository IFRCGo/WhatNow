<template>
  <card :title="$t('your_password')">
    <form @submit.prevent="update" @keydown="form.onKeydown($event)">
      <!-- Old Password -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('old_password') }}</label>
        <div class="col-md-7">
          <input v-model="form.current_password" type="password" name="password" class="form-control"
                 :class="{ 'is-invalid': form.errors.has('current_password') }">
          <has-error :form="form" field="current_password"></has-error>
        </div>
      </div>

      <!-- Password -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('new_password') }}</label>
        <div class="col-md-7">
          <input v-model="form.password" type="password" name="password" class="form-control"
            :class="{ 'is-invalid': form.errors.has('password') }">
          <has-error :form="form" field="password"></has-error>
        </div>
      </div>

      <!-- Password Confirmation -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('confirm_password') }}</label>
        <div class="col-md-7">
          <input v-model="form.password_confirmation" type="password" name="password_confirmation" class="form-control"
            :class="{ 'is-invalid': form.errors.has('password_confirmation') }">
          <has-error :form="form" field="password_confirmation"></has-error>
        </div>
      </div>

      <!-- Submit Button -->
      <div class="form-group row">
        <div class="col-md-9 ml-md-auto">
          <v-button :loading="form.busy">{{ $t('update') }}</v-button>
        </div>
      </div>
    </form>
  </card>
</template>

<script>
import Form from 'vform'

export default {
  scrollToTop: false,

  metaInfo () {
    return { title: this.$t('settings') }
  },

  data: () => ({
    form: new Form({
      current_password: '',
      password: '',
      password_confirmation: ''
    })
  }),

  methods: {
    async update () {
      try {
        const response = await this.form.patch('/api/settings/password')
        // Save the token.
        if (response.status === 200) {
          this.$store.dispatch('auth/saveToken', {
              token: response.data.token
          })
          this.$noty.success(this.$t('password_updated'))
          // Fetch the user.
          await this.$store.dispatch('auth/fetchUser')

          this.form.reset()
        }
      } catch (e) {
        if (e.response.status === 403) {
          // We have to overwrite the error here because the form component can't handle the forbidden response
          this.form.errors.errors = { current_password: [this.$t('incorrect_password')] }
        } else {
          this.$noty.error(this.$t('error_alert_text'))
        }
      }
    }
  }
}
</script>
