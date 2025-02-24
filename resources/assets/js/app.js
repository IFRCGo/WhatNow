import 'es6-promise/auto'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import Vue from 'vue'
import store from '~/store'
import router from '~/router'
import App from '~/components/App'
import { i18n } from '~/plugins/i18n'
import BootstrapVue from 'bootstrap-vue'
import vSelect from 'vue-select'
import Gravatar from 'vue-gravatar'
import VueNoty from 'vuejs-noty'
import VueMoment from 'vue-moment'
import methods from './methods'
import filters from './filters'
import GoogleAuth from 'vue-google-auth'
import TscGTAG from './plugins/gtag'
import * as Sentry from '@sentry/browser'
import * as Integrations from '@sentry/integrations'

import '~/plugins'
import '~/components'

import { library } from '@fortawesome/fontawesome-svg-core'
import { faPencil, faPen, faSpinner, faTrash, faXmark } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(faPencil, faSpinner, faPen, faTrash, faXmark)

Sentry.init({
  dsn: window.config.sentry_dsn,
  integrations: [new Integrations.Vue({ Vue, attachProps: true })]
})

Vue.use(BootstrapVue)
Vue.component('v-select', vSelect)
Vue.component('v-gravatar', Gravatar)
Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.use(VueNoty, {
  theme: 'sunset'
})
Vue.use(VueMoment)

Vue.use(TscGTAG)

Vue.use(GoogleAuth, { client_id: window.config.google.client_id, scopes: 'profile email openid' })
Vue.googleAuth().load()

Vue.config.productionTip = false

// Global mixin
// Use with caution! Once you apply a mixin globally, it will affect every Vue instance created afterwards.
// https://vuejs.org/v2/guide/mixins.html#Global-Mixin
Vue.mixin({
  methods,
  filters
})

new Vue({
  i18n,
  store,
  router,
  ...App
})
