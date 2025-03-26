import axios from 'axios'
import * as types from '../mutation-types'
import { ContentTranslation } from '../models/contentTranslation'

// state
export const state = {
  societies: [],
  currentContent: [],
  currentLanguages: ['en'],
  attr: null,
  hazardsList: [],
  errors: [],
  organisations: [],
  currentInstructions: null,
  currentOrganisation: null,
  subnationals: {}
}

// getters
export const getters = {
  currentContent: state => state.currentContent,
  regionsArray: state => Object.values(state.subnationals),
  subnationals: state => state.subnationals,
  attr: state => state.attr,
  currentLanguages: (state) => {
    const langs = state.currentLanguages || []
    if (state.currentOrganisation) {
      // Add in any missing languages..
      state.currentOrganisation.translations.forEach((translation) => {
        if (langs.find && !langs.find(lang => lang === translation.languageCode)) {
          langs.push(translation.languageCode)
        }
      })
    }
    return langs
  },
  hazardsList: state => state.hazardsList,
  organisations: state => state.organisations,
  currentOrganisation: state => state.currentOrganisation,
  translation: (state) => (whatnowId, langCode) => {
    // Temporary till we have an endpoint
    let toReturn = null
    const whatnow = state.currentContent.find(whatnow => parseInt(whatnow.id, 10) === whatnowId)
    if (whatnow) {
      toReturn = whatnow
      toReturn.currentLang = new ContentTranslation(whatnow.translations[langCode])
    }
    return toReturn
  },
  currentInstructions: state => state.currentInstructions
}

// mutations
export const mutations = {
  [types.FETCH_CONTENT_SUCCESS] (state, { content }) {
    state.currentContent = content.data
    if (content.meta.availableLanguages.lenght > 0) {
      state.currentLanguages = content.meta.availableLanguages
    }
  },
  [types.FETCH_REGIONS_SUCCESS] (state, { content }) {
    state.subnationals = content
  },
  [types.FETCH_HAZARDS_SUCCESS] (state, { content }) {
    state.hazardsList = content.data
  },
  [types.FETCH_INSTRUCTION_SUCCESS] (state, { instructions }) {
    for (const translation in instructions.data.translations) {
      if (instructions.data.translations.hasOwnProperty(translation)) {
        instructions.data.translations[translation] = new ContentTranslation(instructions.data.translations[translation])
      }
    }
    state.currentInstructions = instructions.data
  },
  [types.FETCH_ORGANISATIONS_SUCCESS] (state, { organisations }) {
    state.organisations = organisations.data
  },
  [types.FETCH_ORGANISATION_SINGLE_SUCCESS] (state, { organisation }) {
    state.organisations = state.organisations.filter(o => o.countryCode !== organisation.countryCode)
    state.organisations.push(organisation)
    state.currentOrganisation = organisation

    const organisationLanguages = organisation.translations.map(t => t.languageCode) || []

    if (organisationLanguages.length === 0) {
      organisationLanguages.push('en')
    }

    state.currentLanguages = [...new Set([...organisationLanguages])]
  },
  [types.UPDATE_CONTENT_SUCCESS] (state, data) {
    state.currentContent.forEach((whatnow) => {
      if (whatnow.translations) {
        let content = whatnow.translations.find(translation => translation.id === data.id)
        if (content) {
          content = data.content
        }
      }
    })
  },
  [types.DELETE_CONTENT_SUCCESS] (state, { id }) {
    // Find the id and remove it (preempt the server's response)
    const index = state.currentContent.findIndex(whatnow => parseInt(whatnow.id, 10) === parseInt(id, 10))
    if (index > -1) {
      state.currentContent.splice(index, 1)
    }
  },
  [types.FETCH_ATTR_SUCCESS] (state, { attr }) {
    state.attr = attr.data
  },
  [types.CONTENT_FAILURE] (state, { error }) {
    state.errors.push(error)
  },
  [types.SET_CURRENT_LANGUAGES] (state, languages) {
    state.currentLanguages = languages
  }
}
// actions
export const actions = {
  async fetchContent ({ commit }, societyCode) {
    try {
      const { data } = await axios.get(`/api/organisations/${societyCode}/instructions/revisions/latest`)
      commit(types.FETCH_CONTENT_SUCCESS, { content: data })
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
      throw error
    }
  },
  async fetchRegions ({ commit }, societyCode) {
    try {
      const { data } = await axios.get(`/api/subnationals/${societyCode}`)
      commit(types.FETCH_REGIONS_SUCCESS, { content: data })
    } catch (error) {
      if (error.response.status === 404) {
        // No Regions..
        commit(types.FETCH_REGIONS_SUCCESS, { content: {}})
      } else {
        commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
        throw error
      }
    }
  },
  async fetchHazardTypes ({ commit }) {
    try {
      const { data } = await axios.get(`/api/event-types`)
      commit(types.FETCH_HAZARDS_SUCCESS, { content: data })
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
      throw error
    }
  },
  async fetchPublished ({ commit }, societyCode) {
    try {
      const { data } = await axios.get(`/api/organisations/${societyCode}/instructions`)
      commit(types.FETCH_CONTENT_SUCCESS, { content: data })
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
      throw error
    }
  },
  async fetchInstructions ({ commit }, id) {
    try {
      const { data } = await axios.get(`/api/instructions/${id}/revisions/latest`)
      commit(types.FETCH_INSTRUCTION_SUCCESS, { instructions: data })
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
      throw error
    }
  },
  async fetchOrganisations ({ commit }) {
    try {
      const { data } = await axios.get(`/api/organisations`)
      commit(types.FETCH_ORGANISATIONS_SUCCESS, { organisations: data })
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
      throw error
    }
  },
  async fetchOrganisationSingle ({ commit }, countryCode) {
    try {
      const { data } = await axios.get(`/api/organisations/${countryCode}`)
      commit(types.FETCH_ORGANISATION_SINGLE_SUCCESS, { organisation: data.data })
    } catch (error) {
      console.error(error)
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
    }
  },
  async saveContent ({ commit }, options) {
    try {
      await axios.put(`/api/instructions/${options.data.id}`, options.data)
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
      throw error
    }
  },
  async createContent ({ commit }, options) {
    try {
      await axios.post(`/api/instructions`, options.data)
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
      throw error
    }
  },
  async createHazardType ({ commit }, options) {
    try {
      await axios.post(`/api/event-types`, options)
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
      throw error
    }
  },
  async updateAttribution ({ commit }, options) {
    try {
      await axios.put(`/api/organisations/${options.countryCode}`, options.data)
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
      throw error
    }
  },
  async fetchAttribution ({ commit }, societyCode) {
    try {
      const { data } = await axios.get(`https://api.preparecenter.org/v1/org/${societyCode}`)
      commit(types.FETCH_ATTR_SUCCESS, { attr: data })
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
      throw error
    }
  },
  async deleteContentTranslation ({ commit }, id) {
    try {
      await axios.delete(`/api/instructions/${id}`)
      commit(types.DELETE_CONTENT_SUCCESS, { id })
    } catch (error) {
      commit(`generic/${types.NETWORK_FAILURE}`, { error }, { root: true })
      throw error
    }
  },
  setCurrentLanguages ({ commit }, languages) {
    commit(types.SET_CURRENT_LANGUAGES, languages)
  }
}
