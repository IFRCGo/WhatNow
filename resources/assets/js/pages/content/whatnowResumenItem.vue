<template>
  <b-col class="p-0">
    <div class="d-flex justify-content-between align-items-end">
      <div>
        <div class="d-flex align-items-center">
          <h4 class="key-message-title">
            {{ $t(`content.edit_whatnow.${instructionName}`) }}
          </h4>
        </div>
        <b-collapse id="more">
          <p>
            {{ $t(`content.edit_whatnow.${instructionName}_extra`) }}
          </p>
        </b-collapse>
      </div>
    </div>
    <b-card 
      :class="`mb-5 resumen-key-message-card resumen-key-message-card-${instructionName} bg-grey`">
      <div v-for="(message, index) in keyMessages" :key="`message-${index}`" class="whatnow-instruction-list mb-3">
        <div class="mb-3" :key="`title-${index}`">
          <h5>{{ message.title }}</h5>
        </div>
        <div v-for="(instruction, n) in message.supportingMessages" class="mb-2"
          :key="`${instructionName}-${instruction.key}`">
          <div class="supporting-message-content">
            {{ instruction.message }}
          </div>
        </div>
      </div>
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
  margin-bottom: 15px;
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

.resumen-key-message-card {
  min-height: 36px;
  border-left: 24px solid $hazard-default;
  border-radius: 8px;
  background-color: $cad-solid-bg-2;
  letter-spacing: normal;

  .card-body {
    background-color: #f0f0f0;
  }

  h5.resumen-key-message-titel {
    font-size: 15px;
    font-weight: 500;
    color: #000;
  }

  .supporting-message-content {
    border-radius: 7px;
    background-color: #e6e6e6;
    color: #000;
    font-size: 12px;
    padding: 9px 11px;
  }

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
}
</style>
