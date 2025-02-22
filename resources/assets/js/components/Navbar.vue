<template>
  <header class="bg-white header pt-2 pb-2">
    <b-navbar toggleable="lg" sticky class="navbar styled-navbar navbar-expand-lg navbar-light">
      <router-link :to="{ name: 'welcome' }" class="header__logo has-no-underline navbar-brand d-inline-flex mr-auto">
        <img class="site-footer__logo d-block" :src="src('headerLogo')" :srcSet="srcSet('headerLogo')">

        <h1 class="visually-hidden">
          {{ $t('what_now') }} {{ $t('message_portal') }}
        </h1>
      </router-link>

      <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

      <b-collapse id="nav-collapse" is-nav>
        <b-navbar-nav class="mt-2 mt-lg-0 header-main-links rtl-mr-auto" v-bind:class="{'ml-auto': !user, 'mr-auto': user, 'is-logged-in': user}">
          <li v-if="user && cannot(user, permissions.VIEW_BACKEND)" class="nav-item">
            <router-link :to="{ name: 'applications.dash', params: { } }" class="nav-link text-uppercase">
              {{ $t('my_apps') }}
            </router-link>
          </li>
          <li v-if="user" class="nav-item right-aligned">
            <router-link :to="{ name: 'view-whatnow' }" class="nav-link text-uppercase">
              {{ $t('whatnow_content') }}
            </router-link>
          </li>
          <li v-if="user && can(user, permissions.VIEW_BACKEND)" class="nav-item">
            <router-link :to="{ name: 'home' }" class="nav-link text-uppercase">
              {{ $t('dashboard') }}
            </router-link>
          </li>
          <li v-if="!user" class="nav-item">
            <router-link :to="{ name: 'register' }" class="nav-link text-uppercase">
              {{ $t('sign_up') }}
            </router-link>
          </li>
          <li v-if="!user" class="nav-item">
            <router-link :to="{ name: 'login' }" class="nav-link text-uppercase">
              {{ $t('login') }}
            </router-link>
          </li>

          <LocaleDropdown />

          <li class="nav-item d-lg-none" v-if="user">
            <router-link :to="{ name: 'settings.profile', params: { id: user.data.id, profile: true } }" class="nav-link text-uppercase">
              {{ $t('common.my_profile') }}
            </router-link>
          </li>
          <li class="nav-item d-lg-none" v-if="user">
            <a href="#" @click.prevent="logout" class="nav-link text-uppercase">
              {{ $t('logout') }}
            </a>
          </li>
        </b-navbar-nav>
      </b-collapse>

      <!-- Right aligned nav items -->
      <b-navbar-nav v-if="user" class="ml-2 d-none d-lg-block header-logged-in">
        <!-- Authenticated -->
        <b-nav-item-dropdown v-if="user" right class="has-no-underline">
            <span slot="button-content" class="text-dark py-0">
              <img v-if="user.data.user_profile.photo_url" :src="user.data.user_profile.photo_url" class="rounded-circle profile-photo mr-1 rtl-ml-1">
              <avatar v-else class="rounded-circle profile-photo mr-1 rtl-ml-1" :size="32" :username="user.data.user_profile.first_name + ' ' + user.data.user_profile.last_name" :customStyle="{'display': 'inline-block'}"></avatar>
              {{ user.data.user_profile.first_name }}
            </span>
          <b-dropdown-item :to="{ name: 'settings.profile', params: { id: user.data.id, profile: true } }" class="has-no-underline">
            <fa icon="cog" fixed-width/>
            {{ $t('common.my_profile') }}
          </b-dropdown-item>
          <b-dropdown-divider></b-dropdown-divider>
          <b-dropdown-item @click.prevent="logout" class="has-no-underline">
            <fa icon="sign-out-alt" fixed-width/>
            {{ $t('logout') }}
          </b-dropdown-item>
        </b-nav-item-dropdown>
      </b-navbar-nav>

    </b-navbar>
  </header>
</template>

<script>
import { mapGetters } from 'vuex'
import LocaleDropdown from './LocaleDropdown'
import * as permissionsList from '../store/permissions'
import Avatar from 'vue-avatar'

export default {
  data: () => ({
    appName: window.config.appName,
    permissions: permissionsList,
    isMenuOpen: true
  }),

  props: ['value'],

  computed: mapGetters({
    user: 'auth/user'
  }),

  components: {
    LocaleDropdown,
    Avatar
  },

  methods: {
    async logout () {
      // Log out the user.
      await this.$store.dispatch('auth/logout')

      // Redirect to login.
      this.$router.push({ name: 'login' })
    }
  }
}
</script>

<style scoped>
.profile-photo {
  height: 2rem;
}

.right-aligned {
  margin-left: auto;
}

.styled-navbar {
  box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
  transition: box-shadow 0.3s ease-in-out;
  padding-right: 1rem;
}

.styled-navbar:hover {
  box-shadow: 0 10px 14px rgba(0, 0, 0, 0.25);
}

.header-main-links {
  display: flex;
}
</style>
