<template>
  <b-col>
    <div class="d-flex justify-content-between align-items-end">
      <div>
        <div class="d-flex align-items-center">
          <h4 class="key-message-title">
            {{ $t(`content.edit_whatnow.${instructionName}`) }}
          </h4>
          <div class="mx-5">
            <span variant="link" class="more-info-icon" v-b-tooltip.hover
              :title="$t(`content.edit_whatnow.${instructionName}_extra`)">
              <fa :icon="['fas', 'info-circle']" />
            </span>
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
              <label class="key-message-label" :for="`${instructionName}-title-${index}`">Key message title</label>
              <div class="mx-5">
                <span variant="link" class="more-info-icon" v-b-tooltip.hover
                  :title="$t(`content.edit_whatnow.${instructionName}_extra`)">
                  <fa :icon="['fas', 'info-circle']" />
                </span>
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
            <label class="supporting-message-label">Supporting message</label>
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
      </transition-group>
      <WhatnowDownloadImage v-if="instructionsCopy.length > 0" :instructionId="instructionId" :langCode="langCode"
        :instructionName="instructionName" :revision="true" />
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
      console.log('this.keyMessages', this.keyMessages);
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
  font-size: 25px;
  font-weight: 600;
  color: #1e1e1e;
  margin-bottom: 4px;
}

.more-info-icon {
  font-size: 15px;
  color: #000;
}

.key-message-label {
  font-size: 20px;
  font-weight: 500;
  font-stretch: normal;
  font-style: normal;
  color: #1e1e1e;
}

.key-message-input {
  border-radius: 8px;
  background-color: #e6e6e6;
  height: 45px;
  font-size: 14px;
  border: none;
  outline: none;
}

.supportin-message-input-container {
  position: relative;

  .btn-delete-item {
    width: 30px;
    height: 30px;
    position: absolute;
    right: 4px;
    top: 2px;
    padding: 0;
    border: none;
    background-color: transparent;
    color: $grey;
    z-index: 1;
  }
}

.btn-delete-key-message {
  width: 30px;
  height: 30px;
  padding: 0;
  border: none;
  background-color: transparent;
  color: $grey;
}

.supporting-message-label {
  font-size: 16px;
  font-weight: 500;
  color: #1e1e1e;
}

.supporting-message-input {
  height: 35px;
  border-radius: 8px;
  background-color: #e6e6e6;
  font-size: 12px;
  border: none;
  outline: none;
}

.key-message-card {
  min-height: 45px;
  border-left: 30px solid $hazard-default;
  border-radius: 10px;
  background-color: #f6f6f6;
  padding: 22px 16px 29px 26px;
  // Instructions
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

  &-mitigate_risks {
    border-color: $mitigate_risks;
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
      width: 16px;
      height: 16px;
      object-fit: contain;
    }

    .hazard-card-download-container-button {
      color: $grey;
      padding: 0;
      border: none;
    }
  }

  h5 {
    letter-spacing: 1.5px;
  }
  ol {
    list-style-position: inside;
    margin: 0;
    padding: 0;
    li {
      border-bottom: 1px solid #e5e5e5;
    }
  }
}
</style>