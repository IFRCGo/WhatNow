import Vue from 'vue'
import store from '~/store'
import { i18n } from '~/plugins/i18n'
import { secondsToHms } from '~/utils/time'

window.addEventListener('offline', (e) => {
  const lastLogin = store.getters['auth/user'].data.last_logged_in_at

  let logoutDate
  if (lastLogin) {
    logoutDate = new Date(lastLogin)
    // The logout time will be 6 hours from the last time the user has logged in
    logoutDate.setHours(logoutDate.getHours() + 6)
  }

  const printOfflineErrorMessage = () =>
    `<b>${i18n.t('offline_error.title')}</b></br>` +
    `${i18n.t('offline_error.description', { time: secondsToHms(logoutDate ? (logoutDate - new Date()) / 1000 : undefined) })}`

  const activeToast = Vue.noty.error(printOfflineErrorMessage(), {
    closeWith: [],
    timeout: false,
    // If true closes all visible notifications and shows itself
    killer: true
  })

  const clearIntervalNumber = setInterval(() => {
    activeToast.setText(printOfflineErrorMessage())
  }, 1000)

  const handleBackOnline = () => {
    activeToast.close()
    clearInterval(clearIntervalNumber)
    window.removeEventListener('online', handleBackOnline)
  }

  window.addEventListener('online', handleBackOnline)
})
