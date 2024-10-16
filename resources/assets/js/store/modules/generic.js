import * as types from '../mutation-types'

// state
export const state = {
  errors: []
}

// getters
export const getters = {
  errors: state => state.errors,
  lastError: state => state.errors[state.errors.length - 1]
}

// mutations
export const mutations = {
  [types.NETWORK_FAILURE] (state, { error }) {
    state.errors.push(error)
  }
}

// actions
export const actions = {

}
