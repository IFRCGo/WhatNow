<template>
  <b-col>
    <div class="d-flex justify-content-between align-items-end">
      <div>
        <h4 class="text-uppercase text-secondary">
          <b>{{ $t(`content.edit_whatnow.${instructionName}`) }}</b>
        </h4>

        <p class="text-secondary">
          <i>{{ $t(`content.edit_whatnow.${instructionName}_help`) }}</i>
          <span variant="link" v-b-tooltip.hover :title="$t(`content.edit_whatnow.${instructionName}_extra`)"><fa :icon="['fas', 'info-circle']" /></span>
        </p>
        <b-collapse id="more">
          <p>
            {{ $t(`content.edit_whatnow.${instructionName}_extra`) }}
          </p>
        </b-collapse>
      </div>
      <div v-if="!this.disabled && canEdit">
        <b-button size="sm" variant="primary" @click="addItem" class="mb-2">
          {{ $t('content.edit_whatnow.add_item')}}
        </b-button>
      </div>
    </div>

    <b-card :class="`hazard-instruction-card hazard-instruction-card-${instructionName} bg-grey`">
      <!-- We have to use n in x here as you cannot directly mutate an object in a v-model -->
      <transition-group name="fade-slide" tag="ol" class="pt-4 whatnow-instruction-list">
        <li v-for="(instruction, n) in instructionsCopy" class="mb-4" :key="`${instructionName}-${instruction.key}`">
          <label :for="`${instructionName}-${n}`" class="sr-only">{{ $t('content.edit_whatnow.content_label') }}</label>
          <b-input-group>
            <b-form-input
              type="text"
              :id="`${instructionName}-${n}`"
              :name="`${instructionName}-${n}`"
              v-model="instruction.message"
              :placeholder="$t('content.edit_whatnow.content_placeholder')"
              @change="instructionChange"
              :disabled="!canEdit"/>
              <div class="input-group-append" v-if="canEdit">
                <b-button variant="danger" @click="deleteItem(n)">
                  {{ $t('content.edit_whatnow.delete_item')}}
                </b-button>
              </div>
          </b-input-group>
        </li>
      </transition-group>
      <WhatnowDownloadImage
        v-if="instructionsCopy.length > 0"
        :instructionId="instructionId"
        :langCode="langCode"
        :instructionName="instructionName"
        :revision="true"
        />
    </b-card>
  </b-col>
</template>
<script>
import axios from 'axios'
import WhatnowDownloadImage from './whatnowDownloadImage'
import { mapGetters } from 'vuex'
import * as permissionsList from '../../store/permissions'

export default {
  props: ['disabled', 'instructions', 'instructionName', 'instructionId', 'langCode'],
  components: {
    WhatnowDownloadImage
  },
  data () {
    return {
      instructionsCopy: [],
      permissions: permissionsList
    }
  },
  mounted () {
    // We need some random key for the for loop so vue doesn't get confused with what component is what.
    this.instructionsCopy = this.instructions.map(instruction => {
      return {
        message: instruction,
        key: Math.random().toString(36).substr(2, 10)
      }
    })
  },
  methods: {
    addItem () {
      this.instructionsCopy.push(
        {
          message: '',
          key: Math.random().toString(36).substr(2, 10)
        }
      )
      this.instructionChange()
    },
    deleteItem (index) {
      this.instructionsCopy.splice(index, 1)
      this.instructionChange()
    },
    instructionChange () {
      this.$emit('instructionUpdate', {
        instructionName: this.instructionName,
        instructions: this.instructionsCopy.map(instruction => instruction.message)
      })
    }
  },
  computed: {
    canEdit () {
      return this.can(this.user, permissionsList.CONTENT_EDIT);
    },
    ...mapGetters({
      user: 'auth/user'
    })
  }
}
</script>
