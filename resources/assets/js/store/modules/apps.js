import axios from 'axios'
import * as types from '../mutation-types'

// state
export const state = {
  userApps: [],
  currentApp: {}
}

// getters
export const getters = {
  userApps: state => state.userApps,
  currentApp: state => state.currentApp
}

// mutations
export const mutations = {
  [types.FETCH_APPS_SUCCESS] (state, { apps }) {
    state.userApps = apps.data
  },
  [types.FETCH_APP_SUCCESS] (state, { app }) {
    state.currentApp = app.data
  }
}
// actions
export const actions = {
  async fetchUserApps ({ commit }) {
    try {
      const { data } = await axios.get('/api/apps')
      commit(types.FETCH_APPS_SUCCESS, { apps: data })
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
    }
  },
  async fetchApp ({ commit }, id) {
    try {
      const { data } = await axios.get(`/api/apps/${id}`)
      commit(types.FETCH_APP_SUCCESS, { apps: data })
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
    }
  },
  async createApp ({ commit }, app) {
    try {
      await axios.post('/api/apps', app)
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
    }
  },

  async editApp ({ commit }, app) {
    try {
      await axios.patch(`/api/apps/${app.id}`, app)
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
    }
  },

  async deleteApp ({ commit }, id) {
    try {
      await axios.delete(`/api/apps/${id}`)
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
    }
  }
}
