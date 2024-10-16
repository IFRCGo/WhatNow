<template>
  <div class="container-fluid login-section">
    <div class="row w-100">
      <div class="col-lg-6 m-auto">
        <card>
          <h1 class="text-center mb-5">
            {{ $t('reset_password') }}
          </h1>

          <form @submit.prevent="send" @keydown="form.onKeydown($event)">

            <alert-success :form="form" :message="status"></alert-success>

            <!-- Email -->
            <div class="form-group">
              <label class="text-uppercase font-weight-bold styled-label">{{ $t('email') }}</label>
              <!-- <div class="col-md-7"> -->
                <input v-model="form.email" type="email" name="email" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('email') }">
                <has-error :form="form" field="email" class="pl-2 font-italic"></has-error>
              <!-- </div> -->
            </div>

            <!-- Submit Button -->
            <div class="form-group mt-4">
              <!-- <div class="col-md-9 ml-md-auto"> -->
                <v-button :loading="form.busy" class="btn-dark w-100">{{ $t('send_password_reset_link') }}</v-button>
              <!-- </div> -->
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
  metaInfo () {
    return { title: this.$t('reset_password') }
  },

  data: () => ({
    status: '',
    form: new Form({
      email: ''
    })
  }),

  methods: {
    async send () {
      const { data } = await this.form.post('/api/password/email')

      this.status = data.status

      this.form.reset()
    }
  }
}
</script>
