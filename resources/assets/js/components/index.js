import Vue from 'vue'
import { HasError, AlertError, AlertSuccess } from 'vform/src/components/bootstrap4'

Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)
Vue.component(AlertSuccess.name, AlertSuccess)

// Load global components dynamically
const requireContext = require.context('./global', false, /.*\.(js|vue)$/)
requireContext.keys().forEach(file => {
  const Component = requireContext(file).default

  if (Component.name) {
    Vue.component(Component.name, Component)
  }
})
