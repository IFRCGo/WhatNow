<template>
  <div>
    <div v-if="cannot(user, permissions.VIEW_BACKEND)">
    </div>
    <b-nav v-else vertical>
      <!-- Manage users content -->
      <b-nav-item class="pl-1 w-100 nav-toggle" v-if="can(user, permissions.USERS_LIST)" v-b-toggle.collapse1>
        <div class="d-flex align-items-center">
          <div class="sidebar__icon mr-1 ml-1">
            <svg fill="white" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 512 512" enable-background="new 0 0 512 512" xml:space="preserve"><g><path d="M357.6,281.4L357.6,281.4C301.5,281.4,256,326.9,256,383v116.8c0,5.6,4.5,10.2,10.2,10.2H449c5.6,0,10.2-4.5,10.2-10.2V383   C459.2,326.9,413.7,281.4,357.6,281.4z"></path><circle cx="357.6" cy="154.4" r="76.2"></circle><path d="M154.4,205.2L154.4,205.2c-56.1,0-101.6,45.5-101.6,101.6v116.8c0,5.6,4.5,10.2,10.2,10.2H195c5.6,0,10.2-4.5,10.2-10.2   V383c0-40,15.5-76.3,40.7-103.5c2.7-2.9,3.7-7,2.2-10.6C233.1,231.6,197,205.2,154.4,205.2z"></path><circle cx="154.4" cy="78.2" r="76.2"></circle></g></svg>
          </div>
          <div>
            {{ $t('sidebar.content_users')}}
          </div>
        </div>
      </b-nav-item>
      <b-collapse id="collapse1" visible accordion="my-accordion" role="tabpanel">
        <b-nav-item class="pl-4 w-100" v-if="can(user, permissions.USERS_LIST)" :to="{ name: 'users.list', params: {} }">{{ $t('sidebar.manage') }}</b-nav-item>
        <b-nav-item class="pl-4 w-100" v-if="can(user, permissions.USERS_LIST)" :to="{ name: 'content.audit_log', params: {} }" exact-active-class>{{ $t('sidebar.audit_log') }}</b-nav-item>
      </b-collapse>

      <!-- What now content -->

      <b-nav-item class="pl-1 w-100 nav-toggle" v-if="can(user, permissions.VIEW_BACKEND)" v-b-toggle.collapse2>
        <div class="d-flex align-items-center">
          <div class="sidebar__icon mr-1 ml-1">
            <svg fill="white" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 512 512" enable-background="new 0 0 512 512" xml:space="preserve"><path d="M484.5,2H27.5C13.5,2,2.1,13.4,2.1,27.4v355.5c0,14,11.4,25.4,25.4,25.4h40.6c5.6,0,10.2,4.5,10.2,10.2v81.4  c0,8.1,9,13,15.8,8.5l133.9-98c1.7-1.3,3.8-2,6-2h250.6c14,0,25.4-11.4,25.4-25.4V27.4C509.9,13.4,498.5,2,484.5,2z M433.7,332.1  H78.3c-14,0-25.4-11.4-25.4-25.4s11.4-25.4,25.4-25.4h355.5c14,0,25.4,11.4,25.4,25.4S447.8,332.1,433.7,332.1z M433.7,230.5H78.3  c-14,0-25.4-11.4-25.4-25.4c0-14,11.4-25.4,25.4-25.4h355.5c14,0,25.4,11.4,25.4,25.4C459.1,219.2,447.8,230.5,433.7,230.5z   M433.7,129H78.3c-14,0-25.4-11.4-25.4-25.4s11.4-25.4,25.4-25.4h355.5c14,0,25.4,11.4,25.4,25.4S447.8,129,433.7,129z"></path></svg>
          </div>
          <div>
            {{ $t('sidebar.content') }}
          </div>
        </div>
      </b-nav-item>
      <b-collapse id="collapse2" visible accordion="my-accordion" role="tabpanel">
        <b-nav-item class="pl-4 w-100" v-if="firstSocietyCode" :to="{ name: 'content.whatnow', params: { countryCode: firstSocietyCode } }" exact-active-class>{{ $t('sidebar.whatnow') }}</b-nav-item>
        <b-nav-item class="pl-4 w-100" :to="{ name: 'content.audit_log', params: {} }" exact-active-class>{{ $t('sidebar.audit_log') }}</b-nav-item>
        <b-nav-item class="pl-4 w-100" :to="{ name: 'content.bulk_upload', params: {} }" exact-active-class>{{ $t('sidebar.bulk') }}</b-nav-item>
        <b-nav-item class="pl-4 w-100" v-if="can(user, permissions.CONTENT_CREATE)" :to="{ name: 'content.regions', params: {} }" exact-active-class>{{ $t('sidebar.regions') }}</b-nav-item>
      </b-collapse>

      <!-- API usage and management -->
      <b-nav-item class="pl-1 w-100 nav-toggle" v-if="can(user, permissions.USERS_LIST)" v-b-toggle.collapse3>
        <div class="d-flex align-items-center">
          <div class="sidebar__icon mr-1 ml-1">
            <svg fill="white" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 512 512" enable-background="new 0 0 512 512" xml:space="preserve"><g><path d="M484.6,2H27.4C13.4,2,2,13.4,2,27.4V383c0,14,11.4,25.4,25.4,25.4h116.8c5.6,0,10.2,4.5,10.2,10.2V449   c0,5.6-4.5,10.2-10.2,10.2h-34.4c-3.8,0-7.4,2.2-9.1,5.6l-15.2,30.5c-3.4,6.8,1.5,14.7,9.1,14.7h322.7c7.6,0,12.5-7.9,9.1-14.7   l-15.2-30.5c-1.7-3.4-5.2-5.6-9.1-5.6h-34.4c-5.6,0-10.2-4.5-10.2-10.2v-30.5c0-5.6,4.5-10.2,10.2-10.2h116.8   c14,0,25.4-11.4,25.4-25.4V27.4C510,13.4,498.6,2,484.6,2z M459.2,322c0,5.6-4.5,10.2-10.2,10.2H63c-5.6,0-10.2-4.5-10.2-10.2V63   c0-5.6,4.5-10.2,10.2-10.2H449c5.6,0,10.2,4.5,10.2,10.2V322z"></path><path d="M169.6,205.2h-30.5c-5.6,0-10.2,4.5-10.2,10.2v55.9c0,5.6,4.5,10.2,10.2,10.2h30.5c5.6,0,10.2-4.5,10.2-10.2v-55.9   C179.8,209.7,175.3,205.2,169.6,205.2z"></path><path d="M271.2,154.4h-30.5c-5.6,0-10.2,4.5-10.2,10.2v106.7c0,5.6,4.5,10.2,10.2,10.2h30.5c5.6,0,10.2-4.5,10.2-10.2V164.6   C281.4,158.9,276.9,154.4,271.2,154.4z"></path><path d="M372.8,103.6h-30.5c-5.6,0-10.2,4.5-10.2,10.2v157.5c0,5.6,4.5,10.2,10.2,10.2h30.5c5.6,0,10.2-4.5,10.2-10.2V113.8   C383,108.1,378.5,103.6,372.8,103.6z"></path></g></svg>
          </div>
          <div>
            {{ $t('sidebar.api') }}
          </div>
        </div>
      </b-nav-item>
      <b-collapse id="collapse3" visible accordion="my-accordion" role="tabpanel">
        <b-nav-item class="pl-4 w-100" v-if="can(user, permissions.USERS_LIST)" :to="{ name: 'api-usage.api-stats', params: {} }" exact-active-class>{{ $t('sidebar.api_stats') }}</b-nav-item>
        <b-nav-item class="pl-4 w-100" v-if="can(user, permissions.USERS_LIST)" :to="{ name: 'api-usage.api-users', params: {} }" exact-active-class>{{ $t('sidebar.api_users') }}</b-nav-item>
        <b-nav-item class="pl-4 w-100" v-if="can(user, permissions.TERMS_UPDATE)" :to="{ name: 'api-usage.terms-conditions', params: {} }" exact-active-class>{{ $t('sidebar.terms_conditions') }}</b-nav-item>
      </b-collapse>

    </b-nav>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import * as permissionsList from '../store/permissions'

export default {
  name: 'sidebar',
  data () {
    return {
      permissions: permissionsList
    }
  },
  computed: {
    firstSocietyCode () {
      if (this.user) {
        if (this.can(this.user, permissionsList.ALL_ORGANISATIONS)) {
          return 'GB'
        }
        if (this.user.data.organisations && this.user.data.organisations.length === 0) {
          return null
        } else if (this.user.data.organisations) {
          return this.user.data.organisations[0]
        }
      }
      return null
    },
    ...mapGetters({
      user: 'auth/user'
    })
  }
}
</script>
