import Vue from 'vue'
import fontawesome from '@fortawesome/fontawesome'
import FontAwesomeIcon from '@fortawesome/vue-fontawesome'

Vue.component('fa', FontAwesomeIcon)

import {
  faUser, faLock, faSignOutAlt, faCog, faCircleNotch, faSpinner, faBars, faTimes, faPlus, faEllipsisV, faChevronDown, faChevronUp, faChevronLeft, faInfoCircle, faPenSquare
} from '@fortawesome/fontawesome-free-solid'

import { faGithub } from '@fortawesome/fontawesome-free-brands'

fontawesome.library.add(
  faUser, faLock, faSignOutAlt, faCog, faGithub, faCircleNotch, faSpinner, faBars, faTimes, faPlus, faEllipsisV, faChevronDown, faChevronUp, faChevronLeft, faInfoCircle, faPenSquare
)
