<template>
  <div>
    <div class="summary-urgency-card mb-5" v-for="(urgency, i) in urgencyLevels" :key="'resume-' + i" v-if="hasSomeValue(urgency.stages)">
      <div class="urgency-card-header">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h4 class="urgency-title">
              {{ urgency.text }}
            </h4>
          </div>
        </div>
      </div>
      <div>
        <div v-for="stage in stages" :key="'resume-urgency-' + stage">
          <whatnow-summary-item v-if="urgency.stages.includes(stage) && translation.stages[stage]" :instructions="translation.stages[stage]"
            :instructionName="stage" :instructionId="translation.id" :langCode="translation.lang" class="mb-4"
            :disabled="true" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import WhatnowSummaryItem from './whatnowSummaryItem';

export default {
  props: ['translation'],
  components: {
    WhatnowSummaryItem
  },
  data() {
    return {
      stages: [
        'immediate',
        'warning',
        'anticipated',
        'assess_and_plan',
        'mitigate_risks',
        'prepare_to_respond',
        'recover'
      ],
      urgencyLevels: [
        {
          value: 'early_warning',
          text: this.$t('content.edit_whatnow.early_warning'),
          stages: ['immediate', 'warning', 'anticipated'],
          description: this.$t('content.edit_whatnow.early_warning_description')
        },
        {
          value: 'disaster_risk_reduction',
          text: this.$t('content.edit_whatnow.disaster_risk_reduction'),
          stages: ['assess_and_plan', 'mitigate_risks', 'prepare_to_respond'],
          description: this.$t('content.edit_whatnow.disaster_risk_reduction_description')
        },
        {
          value: 'recovery',
          text: this.$t('content.edit_whatnow.recovery'),
          stages: ['recover'],
          description: this.$t('content.edit_whatnow.recovery_description')
        },
      ],
      langCode: null,
    }
  },
  methods: {
    hasSomeValue(stages) {
      let hasStages = false;
      stages.forEach((stage) => {
        if (this.translation.stages[stage]) {
          hasStages = true;
        }
      });
      return hasStages;
    }
  }
}
</script>

<style scoped lang="scss">
@import '../../../sass/variables.scss';


.summary-urgency-card{
  padding: 25px 27px;
  border-radius: 8px;
  border: solid 0.8px $urgency-card-border;
  position: relative;
  box-shadow: 0px 1.6px 6.4px rgba(0, 0, 0, 0.1);
  background-color: #f7f7f7;

  .urgency-card-header {
    width: 100%;
    margin-bottom: 24px;

    h4.urgency-title {
      font-size: 25px;
      font-weight: 500;
      color: $text-dark;
      text-transform: uppercase;
    }

    p.urgency-description {
      font-size: 14px;
      font-weight: normal;
      color: $text-black;
    }

    &::after {
      content: '';
      display: block;
      width: 100%;
      height: 1px;
      background-color: $cad-solid-bg-3;
      margin-top: 11px;
    }
  }

  .btn-collapse {
    font-size: 24px;
    border: none;
    background-color: transparent;
    color: $grey;
  }
}
</style>