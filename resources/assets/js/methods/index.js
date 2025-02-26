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
    x1: require('../../img/landing_page/icon-1.svg')
  },
  'works2': {
    x1: require('../../img/landing_page/icon-2.svg')
  },
  'works3': {
    x1: require('../../img/landing_page/icon-3.svg')
  },
  'works4': {
    x1: require('../../img/landing_page/icon-4.svg')
  },
  'works5': {
    x1: require('../../img/landing_page/icon-5.svg')
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
  hazardIcon (str) {
    const eventType = str.toLowerCase()
    switch (true) {
      case eventType.includes('shooter'):
        return require('../../img/icons/active-shooter@3x.png')
      case eventType.includes('terror'):
        return require('../../img/icons/active-shooter@3x.png')
      case eventType.includes('quality'):
        return require('../../img/icons/air-quality@3x.png')
      case eventType.includes('airborne'):
        return require('../../img/icons/airborne-disease@3x.png')
      case eventType.includes('animal'):
        return require('../../img/icons/animal-borne-disease@3x.png')
      case eventType.includes('avalanche'):
        return require('../../img/icons/avalanche@3x.png')
      case eventType.includes('volcano'):
        return require('../../img/icons/volcano@3x.png')
      case eventType.includes('biological'):
        return require('../../img/icons/biological-hazard@3x.png')
      case eventType.includes('blizzard'):
        return require('../../img/icons/blizzard@3x.png')
      case eventType.includes('blood'):
        return require('../../img/icons/bloodborne-disease@3x.png')
      case eventType.includes('wildfire'):
        return require('../../img/icons/extreme-fire@3x.png')
      case eventType.includes('child'):
        return require('../../img/icons/child-safety@3x.png')
      case eventType.includes('coastal'):
        return require('../../img/icons/coastal@3x.png')
      case eventType.includes('drought'):
        return require('../../img/icons/drought@3x.png')
      case eventType.includes('earthquake'):
        return require('../../img/icons/earthquake@3x.png')
      case eventType.includes('fog'):
        return require('../../img/icons/dense-fog@3x.png')
      case eventType.includes('hurricane'):
        return require('../../img/icons/hurricane-alt@3x.png')
      case eventType.includes('typhoon'):
        return require('../../img/icons/hurricane-alt@3x.png')
      case eventType.includes('tornado'):
        return require('../../img/icons/tornado@3x.png')
      case eventType.includes('cyclone'):
        return require('../../img/icons/tropical-cyclone@3x.png')
      case eventType.includes('storm'):
        return require('../../img/icons/storm-alt@3x.png')
      case eventType.includes('tropical'):
        return require('../../img/icons/storm-alt@3x.png')
      case eventType.includes('tsunami'):
        return require('../../img/icons/tsunami@3x.png')
      case eventType.includes('wind'):
        return require('../../img/icons/wind@3x.png')
      case eventType.includes('child-safety'):
        return require('../../img/icons/child-safety@3x.png')
      case eventType.includes('body-fluid-borne-disease'):
        return require('../../img/icons/body-fluid-borne-disease@3x.png')
      case eventType.includes('flu'):
        return require('../../img/icons/body-fluid-borne-disease@3x.png')
      case eventType.includes('cough'):
        return require('../../img/icons/body-fluid-borne-disease@3x.png')
      case eventType.includes('chemical-hazard'):
        return require('../../img/icons/chemical-hazard@3x.png')
      case eventType.includes('chemical'):
        return require('../../img/icons/chemical-hazard@3x.png')
      case eventType.includes('coastal-flood'):
        return require('../../img/icons/coastal-flood@3x.png')
      case eventType.includes('dust-storm'):
        return require('../../img/icons/dust-storm@3x.png')
      case eventType.includes('epidemic'):
        return require('../../img/icons/epidemic@3x.png')
      case eventType.includes('extreme-cold'):
        return require('../../img/icons/extreme-cold@3x.png')
      case eventType.includes('cold'):
        return require('../../img/icons/extreme-cold@3x.png')
      case eventType.includes('extreme-heat'):
        return require('../../img/icons/extreme-heat@3x.png')
      case eventType.includes('excessive-heat'):
        return require('../../img/icons/extreme-heat@3x.png')
      case eventType.includes('flash-flood'):
        return require('../../img/icons/flash-flood@3x.png')
      case eventType.includes('flood'):
        return require('../../img/icons/flood@3x.png')
      case eventType.includes('foodborne-disease'):
        return require('../../img/icons/foodborne-disease@3x.png')
      case eventType.includes('global'):
        return require('../../img/icons/general@3x.png')
      case eventType.includes('hailstorm'):
        return require('../../img/icons/hailstorm@3x.png')
      case eventType.includes('heatwave'):
        return require('../../img/icons/heatwave@3x.png')
      case eventType.includes('heat'):
        return require('../../img/icons/heatwave@3x.png')
      case eventType.includes('hurricane-force-wind'):
        return require('../../img/icons/hurricane-force-wind@3x.png')
      case eventType.includes('hydrologic-advisory'):
        return require('../../img/icons/hydrologic-advisory@3x.png')
      case eventType.includes('lakeshore'):
        return require('../../img/icons/lakeshore@3x.png')
      case eventType.includes('landslide'):
        return require('../../img/icons/landslide@3x.png')
      case eventType.includes('marine-weather'):
        return require('../../img/icons/marine-weather@3x.png')
      case eventType.includes('nuclear-power-plant'):
        return require('../../img/icons/nuclear-power-plant@3x.png')
      case eventType.includes('nuclear'):
        return require('../../img/icons/nuclear@3x.png')
      case eventType.includes('pandemic'):
        return require('../../img/icons/pandemic@3x.png')
      case eventType.includes('pest-infestation'):
        return require('../../img/icons/pest-infestation@3x.png')
      case eventType.includes('public-health-statement'):
        return require('../../img/icons/public-health-statement@3x.png')
      case eventType.includes('radiological-hazard'):
        return require('../../img/icons/radiological-hazard@3x.png')
      case eventType.includes('road-works'):
        return require('../../img/icons/road-works@3x.png')
      case eventType.includes('severe-thunderstorm-warning'):
        return require('../../img/icons/severe-thunderstorm-warning@3x.png')
      case eventType.includes('tropical-storm'):
        return require('../../img/icons/tropical-cyclone@3x.png')
      case eventType.includes('covid-19'):
        return require('../../img/icons/epidemic@3x.png')
      case eventType.includes('request'):
      case eventType.includes('create'):
        return require('../../img/icons/add@3x.png')
      case eventType.includes('accounts'):
        return require('../../img/icons/accounts.svg')
      case eventType.includes('profile-duo'):
        return require('../../img/icons/profile-duo.svg')
      case eventType.includes('speedometer'):
        return require('../../img/icons/speedometer.svg')

      default:
        return require('../../img/icons/general@3x.png')
    }
  }
}
export default methods
