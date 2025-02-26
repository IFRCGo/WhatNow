<template>
  <b-col>
    <div class="d-flex justify-content-between align-items-end">
      <div>
        <div class="d-flex align-items-center">
          <h4 class="key-message-title">
            {{ $t(`content.edit_whatnow.${instructionName}`) }}
          </h4>
          <div class="mx-5 tooltip-container">
            <span
              class="more-info-icon"
              @mouseover="showTooltip = true"
              @mouseleave="showTooltip = false"
              >
              <fa :icon="['fas', 'info-circle']" />
            </span>
            <div v-if="showTooltip" class="custom-tooltip">
              {{ $t(`content.edit_whatnow.${instructionName}_extra`) }}
            </div>
          </div>
        </div>
        <b-collapse id="more">
          <p>
            {{ $t(`content.edit_whatnow.${instructionName}_extra`) }}
          </p>
        </b-collapse>
      </div>
      <div v-if="!this.disabled && canEdit">
        <b-button size="sm" variant="outline-primary" @click="addKeyMessage" class="btn mb-2">
          {{ $t('content.edit_whatnow.add_key_message') }}
        </b-button>
      </div>
    </div>
    <b-card
      :class="`mb-5 key-message-card key-message-card-${instructionName} bg-grey`">
      <!-- We have to use n in x here as you cannot directly mutate an object in a v-model -->
      <transition-group v-for="(message, index) in keyMessages" :key="`message-${index}`" name="fade-slide" tag="ol" class="pt-4 whatnow-instruction-list">
        <div class="mb-5" :key="`title-${index}`">
          <div class="mb-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
              <label class="key-message-label" :for="`${instructionName}-title-${index}`">
                {{ $t('content.edit_whatnow.key_message_label') }}
              </label>
              <div class="mx-5 tooltip-container">
            <span
              class="more-info-icon"
              @mouseover="showTooltip = true"
              @mouseleave="showTooltip = false"
            >
              <fa :icon="['fas', 'info-circle']" />
            </span>
                <div v-if="showTooltip" class="custom-tooltip">
                  {{ $t(`content.edit_whatnow.${instructionName}_extra`) }}
                </div>
              </div>
            </div>
            <div>
              <b-button class="btn-delete-key-message" variant="light" @click="deleteKeyMessage(message.key)">
                <i class="fas fa-trash"></i>
              </b-button>
            </div>
          </div>
          <b-form-input class="key-message-input" type="text" :id="`${instructionName}-title-${index}`"
            :name="`${instructionName}-title-${index}`" v-model="message.title" :placeholder="'Title of key message'"
            :disabled="!canEdit" @change="instructionChange" />
        </div>
        <div class="d-flex justify-content-between mb-3" :key="`add-supporting-message-${index}`">
          <div class="d-flex justify-content-start mb-3">
            <label class="supporting-message-label">
              {{ $t('content.edit_whatnow.supporting_message_label') }}
            </label>
          </div>
          <div>
            <b-button size="sm" variant="outline-primary" @click="addSupportingMessage(index)" class="btn mb-2">
              {{ $t('content.edit_whatnow.add_supporting_message') }}
            </b-button>
          </div>
        </div>
        <li v-for="(instruction, n) in message.supportingMessages" class="mb-4"
          :key="`${instructionName}-${instruction.key}`">

          <b-input-group class="supportin-message-input-container">
            <b-form-input class="supporting-message-input" type="text" :id="`${instructionName}-${n}`" :name="`${instructionName}-${n}`"
              v-model="instruction.message" :placeholder="$t('content.edit_whatnow.content_placeholder')"
              @change="instructionChange" :disabled="!canEdit" />
              <b-button class="btn-delete-item" variant="light" @click="deleteSupportingMessage(instruction.key, message.key)">
                <i class="fas fa-trash"></i>
              </b-button>
          </b-input-group>
        </li>
        <WhatnowDownloadImage v-if="message.supportingMessages.length > 0" :instructionId="instructionId" :langCode="langCode"
          :instructionName="instructionName" :revision="true" :key="message.key"/>
      </transition-group>
    </b-card>
  </b-col>
</template>
<script>
import WhatnowDownloadImage from './whatnowDownloadImage'
import { mapGetters } from 'vuex'
import * as permissionsList from '../../store/permissions'

export default {
  props: ['disabled', 'instructions', 'instructionName', 'instructionId', 'langCode'],
  components: {
    WhatnowDownloadImage
  },
  data() {
    return {
      instructionsCopy: [],
      permissions: permissionsList,
      keyMessages: [],
      showTooltip: false
    }
  },
  mounted() {
    this.keyMessages = this.instructions.map(instruction => {
      const { content = [], title } = instruction;
      const supportingMessages = content.map((message) => {
        return ({
          message,
          key: Math.random().toString(36).substr(2, 10)
        })
      });
      return {
        title,
        supportingMessages,
        key: Math.random().toString(36).substr(2, 10)
      }
    })
  },
  methods: {
    addKeyMessage() {
      this.keyMessages.push({
        title: '',
        supportingMessages: [],
        key: Math.random().toString(36).substr(2, 10)
      });
      instructionChange();
    },
    addSupportingMessage(index) {
      this.keyMessages[index].supportingMessages.push({
        message: '',
        key: Math.random().toString(36).substr(2, 10)
      })
      instructionChange();
    },
    instructionChange() {
      this.$emit('instructionUpdate', {
        instructionName: this.instructionName,
        instructions: this.keyMessages.map((keyMessage) => ({
          title: keyMessage.title,
          content: keyMessage.supportingMessages.map((supportingMessage) => supportingMessage.message)
        })),
      })
    },
    deleteKeyMessage(key) {
      this.keyMessages = this.keyMessages.filter(message => message.key !== key);
      this.instructionChange();
    },
    deleteSupportingMessage(key, messageKey) {
      const currentMessage = this.keyMessages.find(message => message.key === messageKey);
      currentMessage.supportingMessages = currentMessage.supportingMessages.filter(supportingMessage => supportingMessage.key !== key);
      this.keyMessages = [
        ...this.keyMessages.filter(message => message.key !== messageKey),
        currentMessage
      ]
      this.instructionChange();
    },
  },
  computed: {
    canEdit() {
      return this.can(this.user, permissionsList.CONTENT_EDIT);
    },
    ...mapGetters({
      user: 'auth/user'
    })
  }
}
</script>

<style scoped lang="scss">
@import '../../../sass/variables.scss';

.key-message-title {
  font-size: 20px;
  font-weight: 600;
  color: $text-dark;
  margin-bottom: 3px;
}

.more-info-icon {
  font-size: 12px;
  color: $text-color;
}

.key-message-label {
  font-size: 16px;
  font-weight: 500;
  font-stretch: normal;
  font-style: normal;
  color: $text-dark;
}

.key-message-input {
  border-radius: 6px;
  background-color: $bg-input-light;
  height: 36px;
  font-size: 11px;
  border: none;
  outline: none;
}

.supportin-message-input-container {
  position: relative;

  .btn-delete-item {
    width: 24px;
    height: 24px;
    position: absolute;
    right: 3px;
    top: 2px;
    padding: 0;
    border: none;
    background-color: transparent;
    color: $grey;
    z-index: 1;
  }
}

.btn-delete-key-message {
  width: 24px;
  height: 24px;
  padding: 0;
  border: none;
  background-color: transparent;
  color: $grey;
}

.supporting-message-label {
  font-size: 13px;
  font-weight: 500;
  color: $text-dark;
}

.supporting-message-input {
  height: 28px;
  border-radius: 6px;
  background-color: $bg-input-light;
  font-size: 9px;
  border: none;
  outline: none;
}

.key-message-card {
  min-height: 36px;
  border-left: 24px solid $hazard-default;
  border-radius: 8px;
  background-color: $cad-solid-bg-2;
  padding: 18px 13px 23px 21px;

  &-immediate {
    border-color: $immediate;
  }

  &-warning {
    border-color: $warning;
  }

  &-anticipated {
    border-color: $anticipated;
  }

  &-mitigate_risks {
    border-color: $mitigate_risks;
  }

  &-assess_and_plan {
    border-color: $assess_and_plan;
  }

  &-prepare_to_respond {
    border-color: $prepare_to_respond;
  }

  &-recover {
    border-color: $recover;
  }

  .hazard-card-download-container {
    color: $grey;

    .hazard-card-download-container-icon {
      width: 13px;
      height: 13px;
      object-fit: contain;
    }

    .hazard-card-download-container-button {
      color: $grey;
      padding: 0;
      border: none;
    }
  }

  h5 {
    letter-spacing: 1.2px;
  }

  ol {
    list-style-position: inside;
    margin: 0;
    padding: 0;

    li {
      border-bottom: $border-separator;
    }
  }
}

.tooltip-container {
  position: relative;
  display: inline-block;
  width: 10rem;
}

.custom-tooltip {
  position: absolute;
  bottom: 120%; /* Posición arriba del icono */
  left: 50%;
  transform: translateX(-50%);
  background-color: #E6E6E6;
  color: black;
  padding: 6px 10px;
  font-size: 12px;
  border-radius: 5px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.2s ease-in-out;

  /* Ajustes para el tamaño y los saltos de línea */
  width: 10rem; /* Fijar el ancho */
  max-width: 10rem; /* Evitar que crezca más */
  white-space: normal; /* Permitir saltos de línea */
  word-wrap: break-word; /* Romper palabras largas si es necesario */
}


.tooltip-container:hover .custom-tooltip {
  opacity: 1;
  visibility: visible;
}

</style>
