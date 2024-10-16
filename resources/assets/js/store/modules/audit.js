import axios from 'axios'
import * as types from '../mutation-types'
import querystring from 'querystring'

// state
export const state = {
  audits: null
}

// getters
export const getters = {
  audits: state => state.audits,
  check: state => state.audits !== null
}

// mutations
export const mutations = {
  [types.FETCH_AUDITS_SUCCESS] (state, { history }) {
    state.audits = history
  }
}
// actions
export const actions = {
  async fetchAudits ({ commit }, { page, filters, orderBy, sort }) {
    const queryOptions = { page }
    if (orderBy !== null) {
      queryOptions.orderBy = orderBy
      queryOptions.sort = sort
    }
    try {
      let filterString = filters.content !== null ? `&filters[content]=${filters.content.name}` : ''
      filterString += filters.country_code !== null ? `&filters[country_code]=${filters.country_code}` : ''
      filterString += filters.language_code !== null ? `&filters[language_code]=${filters.language_code}` : ''

      const { data } = await axios.get(`/api/history?${querystring.stringify(queryOptions)}${filterString}`)
      commit(types.FETCH_AUDITS_SUCCESS, { history: data })
    } catch (error) {
      console.error(error)
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
    }
  },
  async fetchUserAudits ({ commit }, { page, filters, orderBy, sort, userId }) {
    const queryOptions = { page }
    if (orderBy !== null) {
      queryOptions.orderBy = orderBy
      queryOptions.sort = sort
    }
    try {
      let filterString = filters.content !== null ? `&filters[content]=${filters.content.name}` : ''
      filterString += filters.country_code !== null ? `&filters[country_code]=${filters.country_code}` : ''
      filterString += filters.language_code !== null ? `&filters[language_code]=${filters.language_code}` : ''

      const { data } = await axios.get(`/api/users/${userId}/history?${querystring.stringify(queryOptions)}${filterString}`)
      commit(types.FETCH_AUDITS_SUCCESS, { history: data })
    } catch (error) {
      console.error(error)
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
    }
  }
}
