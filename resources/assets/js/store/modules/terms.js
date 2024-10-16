import axios from 'axios'
import * as types from '../mutation-types'
import querystring from 'querystring'

// state
export const state = {
  terms: null,
  latest: null,
  allTerms: null
}

// getters
export const getters = {
  terms: state => state.terms,
  allTerms: state => state.allTerms,
  check: state => state.terms !== null,
  latest: state => state.latest
}

// mutations
export const mutations = {
  [types.FETCH_TERMS_SUCCESS] (state, { terms }) {
    state.terms = terms
  },
  [types.FETCH_ALL_TERMS_SUCCESS] (state, { terms }) {
    state.allTerms = terms
  },
  [types.FETCH_LATEST_TERM_SUCCESS] (state, { term }) {
    state.latest = term.data
  }
}
// actions
export const actions = {
  async fetchTerms ({ commit }, { page }) {
    try {
      const queryOptions = { page }
      const { data } = await axios.get(`/api/terms?${querystring.stringify(queryOptions)}`)
      commit(types.FETCH_TERMS_SUCCESS, { terms: data })
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
      throw error
    }
  },
  async fetchAllTerms ({ commit }) {
    try {
      const { data } = await axios.get(`/api/terms/all`)
      commit(types.FETCH_ALL_TERMS_SUCCESS, { terms: data.data })
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
      throw error
    }
  },
  async fetchLatestTerms ({ commit }) {
    try {
      const { data } = await axios.get(`/api/terms/latest`)
      commit(types.FETCH_LATEST_TERM_SUCCESS, { term: data })
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
      throw error
    }
  },
  async updateTerms ({ commit }, term) {
    try {
      const { data } = await axios.post(`/api/terms`, term)
      commit(types.FETCH_LATEST_TERM_SUCCESS, { term: data })
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
      throw error
    }
  }
}
