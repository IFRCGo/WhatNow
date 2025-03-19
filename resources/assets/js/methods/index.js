import { markdown } from 'markdown'
import { languages } from 'countries-list'

const icons = {
  'footerImage': {
    x1: require('../../img/landing_page/footer-logo.png'),
    x2: require('../../img/landing_page/footer-logo@2x.png'),
    x3: require('../../img/landing_page/footer-logo@3x.png')
  },
  'headerLogo': {
    x1: require('../../img/landing_page/header-logo.png'),
    x2: require('../../img/landing_page/header-logo@2x.png'),
    x3: require('../../img/landing_page/header-logo@3x.png')
  },
  'ifrcLogo': {
    x1: require('../../img/landing_page/ifrc-logo.png')
  },
  'mapImg': {
    x1: require('../../img/landing_page/landing-map.png')
  },
  'works1': {
    x1: require('../../img/landing_page/Icon-1.svg')
  },
  'works2': {
    x1: require('../../img/landing_page/Icon-2.svg')
  },
  'works3': {
    x1: require('../../img/landing_page/Icon-3.svg')
  },
  'works4': {
    x1: require('../../img/landing_page/Icon-4.svg')
  },
  'works5': {
    x1: require('../../img/landing_page/Icon-5.svg')
  },
  'landingHead': {
    x1: require('../../img/landing_page/new-landing-head.svg')
  },
  'logoLarge': {
    x1: require('../../img/logo-large.png'),
    x2: require('../../img/logo-large@2x.png'),
    x3: require('../../img/logo-large@3x.png')
  },
  'downloadIcon': {
    x1: require('../../img/download-icon.png'),
    x2: require('../../img/download-icon@2x.png'),
    x3: require('../../img/download-icon@3x.png')
  },
  'dataPreview': {
    x1: require('../../img/landing_page/data-preview.png'),
    x2: require('../../img/landing_page/data-preview@2x.png'),
    x3: require('../../img/landing_page/data-preview@3x.png')
  },
  'globalIcon': {
    x1: require('../../img/landing_page/global.png'),
    x2: require('../../img/landing_page/global@2x.png'),
    x3: require('../../img/landing_page/global@3x.png')
  },
  'warningIcon': {
    x1: require('../../img/landing_page/warning.png'),
    x2: require('../../img/landing_page/warning@2x.png'),
    x3: require('../../img/landing_page/warning@3x.png')
  },
  'homeIcon': {
    x1: require('../../img/landing_page/home.png'),
    x2: require('../../img/landing_page/home@2x.png'),
    x3: require('../../img/landing_page/home@3x.png')
  },
  'handsIcon': {
    x1: require('../../img/landing_page/hands.png'),
    x2: require('../../img/landing_page/hands@2x.png'),
    x3: require('../../img/landing_page/hands@3x.png')
  },
  'stormIcon': {
    x1: require('../../img/landing_page/storm.png'),
    x2: require('../../img/landing_page/storm@2x.png'),
    x3: require('../../img/landing_page/storm@3x.png')
  },
  'coldIcon': {
    x1: require('../../img/landing_page/cold.png'),
    x2: require('../../img/landing_page/cold@2x.png'),
    x3: require('../../img/landing_page/cold@3x.png')
  }
}

export const methods = {
  checkPermission (user, permission) {
    let hasPermission = false

    if (user && user.permissions) {
      user.permissions.permissions.forEach(p => {
        if (p === permission) {
          hasPermission = true
        }
      })
    }

    return hasPermission
  },
  srcSet (val) {
    return `${icons[val].x1} 1x, ${icons[val].x2} 2x, ${icons[val].x3} 3x`
  },
  src (val) {
    return `${icons[val].x1}`
  },
  can (user, permission) {
    const doesUserHavePermission = this.checkPermission(user, permission)
    return doesUserHavePermission
  },
  cannot (user, permission) {
    const userDoesNotHavePermission = !this.checkPermission(user, permission)
    return userDoesNotHavePermission
  },
  isLangRTL (locale) {
    if (this.locale === 'ar' || this.locale === 'ur') {
      return true
    } else {
      return false
    }
  },
  truncate: (str, length) => {
    if (!str) {
      return str
    }
    return str.length > length ? str.substr(0, length - 1).trim() + '…' : str
  },
  markdown: value => markdown.toHTML(value),
  getLangName: value => languages[value] ? languages[value].native : value,
  hazardIcon (str, hazardsList = []) {
    if (str === 'Create Hazard Type' || str === 'create') {
      return require('../../img/icons/add@3x.png')
    }

    let hazardUrl = null
    if (hazardsList.length) {
      const hazard = hazardsList.find(h => h.name === str || h.icon === str)
      if (hazard) {
        hazardUrl = hazard.url
      }
    }
    return hazardUrl
  }
}
export default methods
