<template>
  <v-select :dir="isLangRTL(locale) ? 'rtl' : 'ltr'" ref='hazardDropdown' v-model="hazardType" class="w-100 v-select-custom p-0" :options="hazardTypeList" label="name" :disabled="hazardTypeList.length === 0" :placeholder="$t('hazard_type.dropdown.select')">
    <template slot="option" slot-scope="option">
      <div class="dropdown-option">
        <b-img :src="option.url || hazardIcon(option.code)" class="rounded-circle mr-1" width="24" height="24" alt="" role="presentation"></b-img>
        {{ option.name }}
      </div>
    </template>
    <template slot="selected-option" slot-scope="option">
      <div class="dropdown-option">
        <b-img :src="option.url || hazardIcon(option.code)" class="rounded-circle mr-1" width="24" height="24" alt="" role="presentation"></b-img>
        {{ option.name }}
      </div>
    </template>
  </v-select>
</template>
<script>
import { mapGetters } from 'vuex'

export default {
  props: ['value', 'hazardTypeList'],
  async mounted () {
    this.hazardType = this.value
  },
  data () {
    return {
      hazardType: null
    }
  },
  methods: {
    clearSelect () {
      this.$refs.hazardDropdown.clearSelection();
    }
  },
  watch: {
    hazardType () {
      this.$emit('input', this.hazardType)
    },
    value: {
      handler (val, oldVal) {
        if (val !== oldVal && val === null) {
           this.clearSelect()
        }
      },
      deep: true
    }
  },
  computed: mapGetters({
    locale: 'lang/locale',
  }),
}
</script>
