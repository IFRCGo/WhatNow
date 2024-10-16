import store from '~/store'

export default (to, from, next) => {
  if (!store.getters['auth/user']) {
    next({ name: 'home' })
  } else if (store.getters['auth/user'].role === []) {
    next({ name: 'home' })
  } else if (store.getters['auth/user'].data.user_profile.accepted_agreement === false) {
    next({ name: 'user-agreement' })
  } else {
    next()
  }
}
