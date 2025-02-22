<template>
  <div id="app">
    <div :class="{'is-rtl': isRTL}">
      <loading ref="loading"></loading>

      <transition name="page" mode="out-in">
        <component v-if="layout" :is="layout"></component>
      </transition>

      <footer v-if="layout" class="site-footer">
        <div class="new-footer">
          <div>
            <h2>
              {{ $t('landing.footer.subtitle_1') }}
            </h2>
            <p>
              {{ $t('landing.footer.text_1') }}
            </p>
            <p>
              {{ $t('landing.footer.text_2') }}
            </p>
            <p>
              {{ $t('landing.footer.text_3') }}
            </p>
          </div>
          <div>
            <h2>
              {{ $t('landing.footer.subtitle_2') }}
            </h2>
            <b-link :href="`mailto:${$t('landing.footer.email_ifrc')}?Subject=GDPC%20Admin%20Portal`">
              {{ $t('landing.footer.email_ifrc') }}
            </b-link>
            <br>
            <a class="footer-link" href="https://ifrc.org" target="_blank" rel="noopener noreferrer">
              {{ $t('landing.footer.web_ifrc') }}
            </a>
          </div>
          <div>
            <h2>
              {{ $t('landing.footer.subtitle_3') }}
            </h2>
              <router-link class="footer-link" :to="{ name: 'terms-service' }">
                {{ $t('landing.footer.terms') }}
              </router-link>
          </div>
          <div>
            <h2>
              {{ $t('landing.footer.subtitle_4') }}
            </h2>
            <router-link class="mb-2" :to="{name: 'docs'}">{{ $t('landing.footer.api_doc') }}</router-link>
            <p>
              {{ $t('landing.footer.faq') }}
            </p>
          </div>
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

  .new-footer {
    width: 100%;
    background: #323232;
    padding-top: 2rem;
    color: white;
    display: flex;
    justify-content: space-around;
    h2 {
      font-size: 1.5rem;
      margin-bottom: 2rem;
    }
    p {
      font-size: 1rem;
      line-height: 1;
    }
    a {
      font-size: 1rem;
    }
  }
</style>
