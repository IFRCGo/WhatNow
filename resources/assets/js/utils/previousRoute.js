import store from '~/store'

const PREVIOUS_ROUTE_KEY = 'previousRoute'

export default class PreviousRoute {
  static get hasRoute () {
    const previousRoute = JSON.parse(localStorage.getItem(PREVIOUS_ROUTE_KEY))

    return previousRoute && previousRoute.userId === store.getters['auth/user'].data.id
  }

  static get route () {
    const previousRoute = JSON.parse(localStorage.getItem(PREVIOUS_ROUTE_KEY))

    return previousRoute && previousRoute.userId === store.getters['auth/user'].data.id ? previousRoute.route : undefined
  }

  static set route (to) {
    localStorage.setItem(PREVIOUS_ROUTE_KEY, JSON.stringify({
      userId: store.getters['auth/user'].data.id,
      route: {
        name: to.name,
        params: to.params,
        path: to.path,
        query: to.query,
        hash: to.hash
      }
    }))
  }
}
