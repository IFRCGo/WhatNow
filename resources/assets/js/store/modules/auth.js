import axios from 'axios'
import Cookies from 'js-cookie'
import * as types from '../mutation-types'
import * as permissionsList from '../permissions'

// state
export const state = {
  user: null,
  token: Cookies.get('token')
}

// getters
export const getters = {
  user: state => state.user,
  token: state => state.token,
  check: state => state.user !== null
}

// mutations
export const mutations = {
  [types.SAVE_TOKEN] (state, { token, remember }) {
    state.token = token
    Cookies.set('token', token, { expires: remember ? 365 : null })
  },

  [types.FETCH_USER_SUCCESS] (state, { user }) {
    user.role = []
    user.permissions = null
    user.role.push(permissionsList.ADMIN)
    var base64Url = state.token.split('.')[1]
    var base64 = base64Url.replace('-', '+').replace('_', '/')
    user.permissions = JSON.parse(window.atob(base64))
    state.user = user
  },

  [types.FETCH_USER_FAILURE] (state) {
    state.token = null
    Cookies.remove('token')
  },

  [types.LOGOUT] (state) {
    state.user = null
    state.token = null

    Cookies.remove('token')
  },

  [types.UPDATE_USER] (state, { user }) {
    state.user = user
  }
}

// actions
export const actions = {
  saveToken ({ commit, dispatch }, payload) {
    commit(types.SAVE_TOKEN, payload)
  },

  async fetchUser ({ commit }) {
    try {
      const { data } = await axios.get('/api/users/me')

      commit(types.FETCH_USER_SUCCESS, { user: data })
    } catch (e) {
      commit(types.FETCH_USER_FAILURE)
    }
  },

  async patchUser ({ commit }, { id, changes }) {
    try {
      const { data } = await axios.patch(`/api/users/${id}`, changes)
      commit(types.FETCH_USER_SUCCESS, { user: data })
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
    }
  },

  updateUser ({ commit }, payload) {
    commit(types.UPDATE_USER, payload)
  },

  async logout ({ commit }) {
    try {
      await axios.post('/api/logout')
    } catch (e) {
    }

      // Reset Selections
    localStorage.removeItem('soc')
    localStorage.removeItem('lang')

    commit(types.LOGOUT)
  },

  // Exchanges a FB auth token with the API
  async loginWithFacebook ({ commit }, { token }) {
    try {
      const { data } = await axios.post('/api/login/facebook', { token: token })
      commit(types.SAVE_TOKEN, { token: data.token })
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
    }
  },

  // Exchanges a Google auth token with the API
  async loginWithGoogle ({ commit }, { token }) {
    try {
      const { data } = await axios.post('/api/login/google', { token: token })
      commit(types.SAVE_TOKEN, { token: data.token })
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
    }
  }
}
