<template>
  <b-container fluid class="login-section is-mobile-full-height">
    <div class="row w-100">
      <div class="col-lg-6 m-auto">
        <card>
          <h3>{{ $t('user_agreement.title') }}</h3>

          <p>
            {{ $t('user_agreement.para_one') }}
          </p>

          <a :href="pdf" target="_blank" rel="noopener noreferrer" class="underlined-link mb-4 d-block">{{ $t('user_agreement.download') }}</a>

          <b-button variant="dark" size="lg" class="w-100 mb-4" @click="agreeToTerms()">{{ $t('user_agreement.cta') }}</b-button>
        </card>
      </div>
    </div>
  </b-container>
</template>

<script>
import pdfFile from '../../../pdf/user-agreement-2018-05-30.pdf'
import { mapGetters } from 'vuex'

export default {
  loading: false,

  data() {
    return {
      pdf: pdfFile
    }
  },

  methods: {
    async agreeToTerms() {
      try {
        const changes = {
            accepted_agreement: true
        }
        await this.$store.dispatch('auth/patchUser', { id: this.user.data.id, changes })
        this.$store.dispatch('auth/fetchUser')
        this.$router.push({ name: 'home' })
      } catch (err) {
        console.error(err)
      }
    }
  },
  computed: mapGetters({
    user: 'auth/user'
  })
}
</script>
