import axios from 'axios'
import * as types from '../mutation-types'
import querystring from 'querystring'

// state
export const state = {
  applications: {
    current_page: 1,
    data: [],
    last_page: 1,
    per_page: 10
  },
  endpoints: [],
  cumulative: {}
}

// getters
export const getters = {
  totalApplicationsRows: state => state.applications.last_page * state.applications.per_page,
  totalApplicationsPerPage: state => state.applications.per_page,
  currentApplicationsData: state => state.applications.data,

  currentEndpointsData: state => state.endpoints,

  currentCumulativeData: state => state.cumulative
}

// mutations
export const mutations = {
  [types.FETCH_APPLICATION_USAGE_SUCCESS] (state, { content }) {
    state.applications = content
  },
  [types.FETCH_ENDPOINT_USAGE_SUCCESS] (state, { content }) {
    state.endpoints = content
  },
  [types.FETCH_CUMULATIVE_USAGE_SUCCESS] (state, { content }) {
    state.cumulative = content
  }
}

// actions
export const actions = {
  async fetchApplicationUsage ({ commit }, { fromDate, toDate, page }) {
    const queryOptions = {
      page,
      fromDate,
      toDate
    }

    try {
      const { data } = await axios.get(`/api/usage/applications?${querystring.stringify(queryOptions)}`)
      commit(types.FETCH_APPLICATION_USAGE_SUCCESS, { content: data })
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
      throw error
    }
  },
  async fetchEndpointUsage ({ commit }, { fromDate, toDate }) {
    const queryOptions = {
      fromDate,
      toDate
    }

    try {
      const { data } = await axios.get(`/api/usage/endpoints?${querystring.stringify(queryOptions)}`)
      commit(types.FETCH_ENDPOINT_USAGE_SUCCESS, { content: data })
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
      throw error
    }
  },
  async fetchCumulativeUsage ({ commit }) {
    try {
      const { data } = await axios.get('/api/usage/totals')
      commit(types.FETCH_CUMULATIVE_USAGE_SUCCESS, { content: data })
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
      throw error
    }
  }
}
