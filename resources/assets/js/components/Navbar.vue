<template>
  <header class="bg-white header pt-2 pb-2">
    <b-navbar toggleable="lg" sticky class="navbar styled-navbar navbar-expand-lg navbar-light">
      <router-link :to="{ name: 'welcome' }" class="header__logo has-no-underline navbar-brand d-inline-flex mr-auto">
        <img class="site-footer__logo d-block ml-4" :src="src('ifrcLogo')">
        <h1 class="visually-hidden">
          {{ $t('what_now') }} {{ $t('message_portal') }}
        </h1>
      </router-link>

      <div class="hamburger-menu" @click="toggleMenu">
        <div class="bar" :class="{ 'open': isMenuOpen }"></div>
        <div class="bar" :class="{ 'open': isMenuOpen }"></div>
        <div class="bar" :class="{ 'open': isMenuOpen }"></div>
      </div>

      <div class="navbar-collapse" :class="{ 'show': isMenuOpen }">
        <b-navbar-nav class="mt-2 mt-lg-0 header-main-links rtl-mr-auto" v-bind:class="{'ml-auto': true, 'is-logged-in': user}">
          <li>
            <LocaleDropdown />
          </li>
          <div class="div-bar"></div>
          <li v-if="user && cannot(user, permissions.VIEW_BACKEND)" class="nav-item">
            <router-link :to="{ name: 'applications.dash', params: { } }" class="nav-link">
              {{ $t('create_key') }}
            </router-link>
          </li>
          <div v-if="user && cannot(user, permissions.VIEW_BACKEND)" class="div-bar"></div>
          <li v-if="user" class="nav-item right-aligned">
            <router-link :to="{ name: 'view-whatnow' }" class="nav-link">
              {{ $t('whatnow_content') }}
            </router-link>
          </li>
          <div v-if="user" class="div-bar"></div>
          <li v-if="user && can(user, permissions.VIEW_BACKEND)" class="nav-item">
            <router-link :to="{ name: 'home' }" class="nav-link">
              {{ $t('dashboard') }}
            </router-link>
          </li>
          <li v-if="!user" class="nav-item">
            <router-link :to="{ name: 'register' }" class="nav-link">
              {{ $t('sign_up') }}
            </router-link>
          </li>
          <div v-if="!user" class="div-bar"></div>
          <li v-if="!user" class="nav-item">
            <router-link :to="{ name: 'login' }" class="nav-link">
              {{ $t('login') }}
            </router-link>
          </li>

          <li class="nav-item d-lg-none" v-if="user">
            <router-link :to="{ name: 'settings.profile', params: { id: user.data.id, profile: true } }" class="nav-link text-uppercase">
              {{ $t('common.my_profile') }}
            </router-link>
          </li>
          <div  v-if="user" class="div-bar"></div>
          <li class="nav-item d-lg-none" v-if="user">
            <a href="#" @click.prevent="logout" class="nav-link text-uppercase">
              {{ $t('logout') }}
            </a>
          </li>
        </b-navbar-nav>
      </div>

      <b-navbar-nav v-if="user" class="ml-2 d-none d-lg-block header-logged-in">
        <b-nav-item-dropdown v-if="user" right class="has-no-underline">
            <span slot="button-content" class="text-dark py-0">
              <img v-if="user.data.user_profile.photo_url" :src="user.data.user_profile.photo_url" class="rounded-circle profile-photo mr-1 rtl-ml-1">
              <avatar v-else class="rounded-circle profile-photo mr-1 rtl-ml-1" :size="32" :username="user.data.user_profile.first_name + ' ' + user.data.user_profile.last_name" :customStyle="{'display': 'inline-block'}"></avatar>
              {{ user.data.user_profile.first_name }}
            </span>
          <b-dropdown-item :to="{ name: 'settings.profile', params: { id: user.data.id, profile: true } }" class="has-no-underline mt-2 text-center">
            <fa icon="cog" fixed-width/>
            {{ $t('common.my_profile') }}
          </b-dropdown-item>
          <b-dropdown-divider></b-dropdown-divider>
          <b-dropdown-item @click.prevent="logout" class="has-no-underline text-center">
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
  data () {
    return {
      appName: window.config.appName,
      permissions: permissionsList,
      isMenuOpen: false // Inicialmente el menú está cerrado
    }
  },
  computed: mapGetters({
    user: 'auth/user'
  }),
  components: {
    LocaleDropdown,
    Avatar
  },
  methods: {
    async logout () {
      await this.$store.dispatch('auth/logout')
      this.$router.push({ name: 'login' })
    },
    toggleMenu () {
      this.isMenuOpen = !this.isMenuOpen
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
  align-items: center;
}

.div-bar {
  padding: 0;
  margin: 0;
  height: 1.5rem;
  width: 2px;
  background: #BDBEC0;
}

.nav-icon {
  font-size: 1.5rem;
}

@media (max-width: 991px) {
  .navbar-collapse {
    ul {
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
      justify-content: center;
      gap: 10px;
    }
  }
}

.hamburger-menu {
  display: inline-block;
  cursor: pointer;
  padding: 10px;
}

.bar {
  width: 25px;
  height: 3px;
  background-color: #333;
  margin: 5px 0;
  transition: 0.4s;
}

.bar.open:nth-child(1) {
  transform: rotate(-45deg) translate(-6px, 6px);
}

.bar.open:nth-child(2) {
  opacity: 0;
}

.bar.open:nth-child(3) {
  transform: rotate(45deg) translate(-6px, -6px);
}

.navbar-collapse {
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  background-color: white;
  z-index: 1000;
  display: none;
  overflow: hidden;
  transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
  max-height: 0;
  opacity: 0;
  box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
  transition: box-shadow 0.3s ease-in-out;
}

.navbar-collapse.show {
  display: block;
  max-height: 500px;
  opacity: 1;
}
</style>
