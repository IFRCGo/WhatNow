<template>
  <form @submit.prevent="update" @keydown="form.onKeydown($event)">
    <!-- Old Password -->
    <div class="row">
      <div class="col-4">
        <div class="form-group">
          <label class="col-form-label">{{ $t('old_password') }}</label>
          <input v-model="form.current_password" type="password" name="password"
                 :type="showPassword.current ? 'text' : 'password'"class="form-control inputs-form"
                 :class="{ 'is-invalid': form.errors.has('current_password') }
                 ">
          <span @click="togglePassword('current')">
              <i class="eye-icon fas fa-eye" :class="showPassword.current ? ' fa-eye' : ' fa-eye-slash'"></i>
          </span>
          <has-error :form="form" field="current_password"></has-error>
        </div>
      </div>
      <div class="col-4">
        <div class="form-group">
          <label class="col-form-label">{{ $t('new_password') }}</label>
          <input v-model="form.password" type="password" name="password"
                 :type="showPassword.new ? 'text' : 'password'" class="form-control inputs-form"
                 :class="{ 'is-invalid': form.errors.has('password') }">
          <has-error :form="form" field="password"></has-error>
          <span @click="togglePassword('new')">
              <i class="eye-icon fas" :class="showPassword.new ? 'fa-eye' : 'fa-eye-slash'"></i>
          </span>
        </div>
      </div>
      <div class="col-4">
        <div class="form-group">
          <label class="col-form-label">{{ $t('confirm_password') }}</label>
          <input v-model="form.password_confirmation" type="password" name="password_confirmation"
                 :type="showPassword.confirm ? 'text' : 'password'"class="form-control inputs-form"
                 :class="{ 'is-invalid': form.errors.has('password_confirmation') }">
          <span @click="togglePassword('confirm')">
              <i class="eye-icon fas" :class="showPassword.confirm ? 'fa-eye' : 'fa-eye-slash'"></i>
          </span>
          <has-error :form="form" field="password_confirmation"></has-error>
        </div>
      </div>
    </div>
    <!-- Submit Button -->
    <div class="form-group row">
      <div class="col-md-9 ml-md-auto update-btn">
        <v-button :loading="form.busy">{{ $t('update') }}</v-button>
      </div>
    </div>
  </form>
</template>

<script>
import Form from 'vform'

export default {
  scrollToTop: false,

  metaInfo () {
    return { title: this.$t('settings') }
  },

  data () {
    return {
      form: new Form({
        current_password: '',
        password: '',
        password_confirmation: ''
      }),
      showPassword: {
        current: false,
        new: false,
        confirm: false
      }
    }
  },

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
    },
    togglePassword (field) {
      this.showPassword[field] = !this.showPassword[field]
    },
    triggerSave () {
      this.update()
    }

  }
}
</script>
<style>
.update-btn {
  display: none;
}
.inputs-form {
  background: #E9E9E9;
  border: none;
  border-radius: 10px;
}
.eye-icon {
  position: absolute;
  right: 25px;
  top: 60%;
  transform: translateY(-50%);
  cursor: pointer;
  color: #A8A8A8;
  font-size: 18px;
}
</style>
