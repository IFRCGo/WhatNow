<template>
  <v-select :clearable="false" :dir="isLangRTL(locale) ? 'rtl' : 'ltr'" :value="value" @input="$emit('input', $event)" class="w-100 v-select-custom" :options="list" label="name" :disabled="list.length === 0" :placeholder="$t('content.whatnow.no_soc')">
    <template slot="option" slot-scope="option">
      <div class="ml-2 rtl-mr-2 dropdown-option">
        {{ option.name }}
      </div>
    </template>
    <template slot="selected-option" slot-scope="option">
      <div class="ml-2 rtl-mr-2 dropdown-option">
        {{ option.name }}
      </div>
    </template>
  </v-select>
</template>
<script>
import { mapGetters } from 'vuex'

export default {
  props: ['value', 'societyList'],
  data () {
    return {
      soc: null
    }
  },
  computed: {
    list() {
      if (this.societyList) {
        return this.societyList.sort((a, b) => a.name.localeCompare(b.name))
      }
      return []
    },
    ...mapGetters({
      locale: 'lang/locale',
    })
  },
}
</script>
