import Cookies from 'js-cookie'
import * as types from '../mutation-types'

const { locale } = window.config
const localeList = ['am', 'ar', 'bn', 'de', 'en', 'es', 'fr', 'ht', 'id', 'it', 'ja', 'my', 'ne', 'pt', 'ru', 'rw', 'sw', 'th', 'tr', 'ur', 'vi']

// state
export const state = {
  locale: Cookies.get('locale') || locale,
  locales: localeList
}

// getters
export const getters = {
  locale: state => state.locale,
  locales: state => state.locales
}

// mutations
export const mutations = {
  [types.SET_LOCALE] (state, { locale }) {
    state.locale = locale
  }
}

// actions
export const actions = {
  setLocale ({ commit, dispatch }, { locale }) {
    commit(types.SET_LOCALE, { locale })

    Cookies.set('locale', locale, { expires: 365 })
  }
}
