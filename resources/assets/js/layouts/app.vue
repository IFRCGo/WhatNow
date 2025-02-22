<template>
  <div class="app-layout">
    <navbar v-model="collapseSidebar"></navbar>
    <b-container fluid>
      <b-row class="position-relative">
        <transition name="page" mode="out-in">
          <sidebar v-if="hasSidebar" class="sidebar pt-4 pb-2" :class="[{ collapsed: collapseSidebar }]"></sidebar>
        </transition>
        <main :class="`col-sm-12 pl-0 pr-0 ${ hasSidebar ? '' : 'vh-min-100' }`">
          <router-view></router-view>
        </main>
      </b-row>
    </b-container>
  </div>
</template>

<script>
import Navbar from '~/components/Navbar'
import Sidebar from '~/components/Sidebar'
import Breadcrumbs from '~/components/Breadcrumbs'
import * as permissionsList from '../store/permissions'

import { mapGetters } from 'vuex'

export default {
  name: 'app-layout',
  data () {
    return {
      collapseSidebar: false,
      permissions: permissionsList
    }
  },
  components: {
    Navbar,
    Sidebar,
    Breadcrumbs
  },
  computed: {
    hasSidebar () {
      if (this.user) {
        if (this.cannot(this.user, this.permissions.VIEW_BACKEND)) return false
        let hideSidebarRoutes = [ 'docs', 'get_started' ]
        if (hideSidebarRoutes.indexOf(this.$route.name) != -1) return false
        return true
      }
      return false
    },
    ...mapGetters({
      user: 'auth/user'
    })
  }
}
</script>
