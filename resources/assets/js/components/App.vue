<template>
  <div id="app">
    <div :class="{'is-rtl': isRTL}">
      <loading ref="loading"></loading>

      <transition name="page" mode="out-in">
        <component v-if="layout" :is="layout"></component>
      </transition>

      <footer v-if="layout" class="site-footer bg-white container-fluid pt-5 pb-5">
        <div class="container">
          <b-row align-h="between">
            <b-col>
              <div class="mb-3">
                <img class="site-footer__logo" :src="src('footerImage')" :srcSet="srcSet('footerImage')" :alt="$t('landing.footer.gdpc_logo')" />
                <img class="site-footer__logo" src="../../img/landing_page/footer-logo-ifrc.svg" :alt="$t('landing.footer.ifrc_logo')" />
              </div>

              <ul class="list-unstyled">
                <li>
                  <router-link :to="{ name: 'terms-service' }">
                    {{ $t('terms_service') }}
                  </router-link>
                </li>
                <li>
                  <b-link href="https://www.preparecenter.org/content/privacy-policy" target="_blank" rel="noreferrer noopener" @click="$fireGTEvent($gtagEvents.PrivacyLink)">{{ $t('landing.footer.privacy') }}</b-link>
                </li>
              </ul>
            </b-col>

            <b-col class="text-right">
              <ul class="list-unstyled d-flex flex-wrap justify-content-around">
                <li>
                  <router-link class="site-footer__heading u-text-normal m-1" :to="{name: 'get_started'}">{{ $t('landing.footer.getting_started') }}</router-link>
                </li>
                <li>
                  <router-link class="site-footer__heading u-text-normal m-1" :to="{name: 'docs'}">{{ $t('landing.footer.docs') }}</router-link>
                </li>
                <li>
                  <a class="site-footer__heading u-text-normal m-1" href="https://docs.google.com/forms/d/1ZgPYoInWaKbMrbhKBb2daPAbxXPq1NrHVNy7O3MAceU/" target="_blank" rel="noreferrer nofollow" @click="$fireGTEvent($gtagEvents.FeedbackLink)">
                    {{ $t('landing.footer.give_feedback') }}
                  </a>
                </li>
              </ul>
            </b-col>
          </b-row>
        </div>
      </footer>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Loading from './Loading'

// Load layout components dynamically.
const requireContext = require.context('../layouts', false, /.*\.vue$/)

const layouts = requireContext.keys()
  .map(file =>
    [file.replace(/(^.\/)|(\.vue$)/g, ''), requireContext(file)]
  )
  .reduce((components, [name, component]) => {
    components[name] = component.default
    return components
  }, {})

export default {
  el: '#app',

  components: {
    Loading
  },

  metaInfo () {
    const { appName } = window.config

    return {
      title: appName,
      titleTemplate: `%s · ${appName}`
    }
  },

  data: () => ({
    layout: null,
    defaultLayout: 'app',
    isRTL: false
  }),

  mounted () {
    this.$loading = this.$refs.loading
    this.setPageDirection()
  },

  watch: {
    '$store.state.lang.locale': function () {
      this.setPageDirection(this.$store.state.lang.locale)
    }
  },

  methods: {
    /**
     * Set the application layout.
     *
     * @param {String} layout
     */
    setLayout (layout) {
      if (!layout || !layouts[layout]) {
        layout = this.defaultLayout
      }

      this.layout = layouts[layout]
    },
    setPageDirection () {
      document.documentElement.lang = this.locale

      if (this.isLangRTL()) {
        document.documentElement.dir = 'rtl'
        this.isRTL = true
      } else {
        this.isRTL = false
        document.documentElement.dir = 'ltr'
      }
    }
  },
  computed: mapGetters({
    locale: 'lang/locale'
  })
}
</script>

<style>
  @import 'vue-select/dist/vue-select.css';
</style>
