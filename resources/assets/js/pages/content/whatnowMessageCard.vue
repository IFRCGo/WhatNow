<template>
  <div>
    <b-card class="h-100 key-message-card" >
      <div class="mb-2">
        <h4 class="card-title mb-3">
          {{ $t('content.edit_whatnow.'+stageName) }}
        </h4>
        <div class="key-message-item" v-for="(keyMessage, index) in stage" :key="'stage'+index">
          <h5 class="card-text key-message-item-title">
            {{ keyMessage.title  }}
          </h5>
          <ul class="key-message-item-list">
            <li class="text-card" v-for="(supportingMessage, index) in keyMessage.content" :key="'supportingMessage'+index">
              {{ supportingMessage }}
            </li>
          </ul>
        </div>
      </div>
      <div class="button-container mt-4 mb-4 key-message-item-btn">
        <b-button variant="outline-primary" class="button-go" @click="openKeyMessageVisualizer()">
          {{ $t('content.whatnow.show_more') }}
        </b-button>
      </div>
    </b-card>
    <b-modal size="xl"  ref="keyMessageVisualizer" :hide-footer="true" id="pre-image" centered :hide-header="true">
      <WhatnowPreviewer
        :keyMessage="stage"
        :eventType="eventType"
        :stageName="stageName"
        :title="title"
        :description="description"
        :selectedSoc="selectedSoc"
        :selectedLanguage="selectedLanguage"
        :contributors="contributors"
      />
    </b-modal>
  </div>
</template>
<script>
import WhatnowPreviewer from './whatnowPreviewer.vue'

export default {
  props: ['stage', 'stageName', 'eventType', 'stageName', 'title', 'description', 'selectedSoc', 'selectedLanguage'],
  data() {
    return {
      contributors: [],
    }
  },
  mounted() {
    if (this.selectedSoc.translations) {
      const translation = this.selectedSoc.translations.filter((translation) => translation.languageCode === this.selectedLanguage);
      if (translation.length) {
        this.contributors = translation[0].contributors;
      }
    }
  },
  components: {
    WhatnowPreviewer
  },
  methods: {
    openKeyMessageVisualizer() {
      this.$refs.keyMessageVisualizer.show()
    },
  }
}
</script>

<style scoped lang="scss">
@import '../../../sass/variables.scss';
.key-message-card {
  background: #E9E9E9;
  padding-bottom: 40px;
}
.key-message-item {
  margin-bottom: 11px;
  &::after {
    content: '';
    display: block;
    width: 90%;
    height: 0.7px;
    background-color: $cad-solid-bg-3;
    margin: 24px auto;
  }

  .key-message-item-title {
    font-size: 16.8px;
    font-weight: 500;
    color: #000;
  }

  ul.key-message-item-list {
    li {
      font-size: 14px;
      color: #1e1e1e;
    }
  }
}
</style>
