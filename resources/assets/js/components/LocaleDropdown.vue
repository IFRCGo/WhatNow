<template>
  <li class="nav-item dropdown">
    <a class="nav-link dd-text dropdown-toggle" href="#" role="button"
      data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        {{$t(`languages.${locale}`)}}
      <i class="fa fa-chevron-down" aria-hidden="true"></i>
    </a>
    <div class="dropdown-menu">
      <a v-for="(value, key) in locales" class="dropdown-item dropdown-item--lang" href="#"
        @click.prevent="onLanguageChange(value)">
        {{$t(`languages.${value}`)}}
      </a>
    </div>
  </li>
</template>

<script>
import { mapGetters } from 'vuex'
import { setLocale } from '~/plugins/i18n'

export default {
  computed: mapGetters({
    locale: 'lang/locale',
    locales: 'lang/locales'
  }),
  methods: {
    onLanguageChange(localeValue) {
      setLocale(localeValue)
    }
  }
}
</script>
<style scoped>

.dropdown-toggle {
  border-bottom: none!important;
}

.dd-text {
  font-size: 14px!important;
}

.styled-navbar li.nav-item a.nav-link.dropdown-toggle:after {
  display: none;
}

.fa-chevron-down {
  margin-left: 3rem;
}

</style>
