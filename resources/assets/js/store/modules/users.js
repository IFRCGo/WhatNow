import axios from 'axios'
import * as types from '../mutation-types'
import querystring from 'querystring'

// state
export const state = {
  users: {
    data: [],
    meta: {}
  },
  userEdit: null,
  roles: [{
    id: null,
    name: 'Fetching roles'
  }],
  sortDesc: false,
  orderBy: null,
  errors: [],
  formErrors: {},
  userPage: 1,
  currentPage: 1,
  filters: {
    activatedFilter: null,
    roleFilter: null,
    countryFilter: null,
    selectedSoc: null,
    termsFilter: 'Terms and Conditions'
  }
}

// getters
export const getters = {
  users: state => state.users,
  userEdit: state => state.userEdit, // Use user from list till we get an endpoint
  check: state => state.users !== null,
  errors: state => state.errors,
  roles: state => state.roles,
  rolesEmpty: state => (state.roles[0].name === 'Fetching roles' && state.roles.length === 1),
  formErrors: state => state.formErrors,
  formErrorsCheck: state => (Object.keys(state.formErrors).length === 0 && state.formErrors.constructor === Object),
  sortDesc: state => state.sortDesc,
  orderBy: state => state.orderBy
}

// mutations
export const mutations = {
  [types.SET_FILTERS] (state, options) {
    state.filters = {
      ...state.filters,
      ...options
    }
  },
  [types.FETCH_USERS_SUCCESS] (state, { users }) {
    state.users = users
  },
  [types.FETCH_USERS_FAILURE] (state, { error }) {
    state.errors.push(error)
  },
  [types.FETCH_EDIT_USER_SUCCESS] (state, { data }) {
    state.userEdit = data.data
  },
  [types.FETCH_ROLES_SUCCESS] (state, { roles }) {
    const arr = [{ id: null, name: 'Select role' }]
    for (const key in roles) {
      arr.push(roles[key])
    }
    state.roles = arr
  },
  [types.FETCH_EDIT_USER_FAILURE] (state, { error }) {
    state.errors.push(error)
  },
  [types.FETCH_ROLES_FAILURE] (state, { error }) {
    state.errors.push(error)
  },
  [types.CREATE_USER_FAILURE] (state, { error }) {
    state.errors.push(error)
    handleFailureErrors(state, error)
  },
  [types.PATCH_USER_FAILURE] (state, { error }) {
    state.errors.push(error)
    handleFailureErrors(state, error)
  },
  [types.RESET_USER_FORM_ERRORS] (state) {
    state.formErrors = {}
  },
  [types.SET_ORDER_BY] (state, orderBy) {
    state.orderBy = orderBy
  },
  [types.SET_SORT_DESC] (state, sortDesc) {
    state.sortDesc = sortDesc
  },
  [types.SET_CURRENT_PAGE] (state, page) {
    state.currentPage = page
  }
}
// actions
export const actions = {
  setFilter ({ commit }, options) {
    commit(types.SET_FILTERS, options)
  },
  clearFilters ({ commit }) {
    commit(types.SET_FILTERS, {
      activatedFilter: null,
      roleFilter: null,
      countryFilter: null,
      selectedSoc: null,
      termsFilter: 'Terms and Conditions'
    })
    commit(types.SET_ORDER_BY, null)
    commit(types.SET_SORT_DESC, false)
    commit(types.SET_CURRENT_PAGE, 1)
  },
  setCurrentPage ({ commit }, currentPage) {
    commit(types.SET_CURRENT_PAGE, currentPage)
  },
  setOrderBy ({ commit }, orderBy) {
    commit(types.SET_ORDER_BY, orderBy)
  },
  setSortDesc ({ commit }, sortDesc) {
    commit(types.SET_SORT_DESC, sortDesc)
  },
  async fetchUsers ({ commit }, { page, filters, excludes, admin, orderBy, sort }) {
    const queryOptions = { page }
    if (orderBy !== null) {
      queryOptions.orderBy = orderBy
      queryOptions.sort = sort
    }
    try {
      let filterString = filters.activated !== null ? `&filters[activated]=${filters.activated}` : ''
      filterString += filters.role !== null ? `&filters[role]=${filters.role}` : ''
      filterString += filters.society !== null ? `&filters[society]=${filters.society}` : ''
      filterString += filters.country_code !== null ? `&filters[country_code]=${filters.country_code}` : ''
      filterString += filters.terms_version !== null ? `&filters[terms_version]=${filters.terms_version}` : ''

      const url = admin ? '/api/users/admins' : '/api/users'
      const { data } = await axios.get(`${url}?${querystring.stringify(queryOptions)}${filterString}`)
      commit(types.FETCH_USERS_SUCCESS, { users: data })
    } catch (e) {
      commit(types.FETCH_USERS_FAILURE, { error: e })
    }
  },
  async fetchEditUser ({ commit }, id) {
    commit(types.RESET_USER_FORM_ERRORS)
    try {
      const { data } = await axios.get(`/api/users/${id}`)
      commit(types.FETCH_EDIT_USER_SUCCESS, { data })
    } catch (e) {
      commit(types.FETCH_EDIT_USER_FAILURE, { error: e })
    }
  },
  async fetchRoles ({ commit }) {
    try {
      const { data } = await axios.get('/api/roles')
      commit(types.FETCH_ROLES_SUCCESS, { roles: data.data })
    } catch (e) {
      commit(types.FETCH_ROLES_FAILURE, { error: e })
    }
  },
  async createUser ({ commit }, user) {
    commit(types.RESET_USER_FORM_ERRORS)
    try {
      await axios.post('/api/users', user)
    } catch (e) {
      commit(types.CREATE_USER_FAILURE, { error: e })
      throw e
    }
  },
  async patchUser ({ commit }, { id, changes }) {
    commit(types.RESET_USER_FORM_ERRORS)
    try {
      await axios.patch(`/api/users/${id}`, changes)
    } catch (e) {
      commit(types.PATCH_USER_FAILURE, { error: e })
      throw e
    }
  }
}

const handleFailureErrors = (state, error) => {
  switch (error.response.status) {
    case 422:
      // Invalid data
      // Set each form field
      state.formErrors = error.response.data.errors
      break
    case 403:
      // Password error
      state.formErrors = {
        password: [error.response.data.message]
      }
      break
  }
}
