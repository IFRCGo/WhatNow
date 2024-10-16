import store from '~/store'
import { methods } from '~/methods'

export default async (to, from, next) => {
  if (!store.getters['auth/check']) {
    next({ name: 'login' })
  } else {
    if (to.meta.permission && !methods.can(store.getters['auth/user'], to.meta.permission)) {
      next({ name: 'home' })
    }

    if (from.name !== 'users.edit' && (to.name === 'api-usage.api-users' || to.name === 'users.list')) {
      // If we're coming to the users list from anything other than the edit page, clear filters.
      store.dispatch('users/clearFilters')
    }
    next()
  }
}
