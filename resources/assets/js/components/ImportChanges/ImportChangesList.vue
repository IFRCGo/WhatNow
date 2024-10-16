<template>
  <ul class="list-unstyled" v-if="resultsToShow.length">
    <li v-for="importResult in resultsToShow" :key="importResult.eventName">
      <import-change-list-item :import-action="importResult.importAction">{{ importResult.eventName }}</import-change-list-item>


      <ul class="list-unstyled pl-5" v-if="importResult.stages.length">
        <li v-for="importStageResult in importResult.stages" :key="importStageResult.stageName">
          <import-change-list-item import-action="updated">{{ $t(`content.edit_whatnow.${importStageResult}`) }}</import-change-list-item>
        </li>
      </ul>
    </li>
  </ul>
  <div v-else>
    <h2 class="c-import-changes-list-empty-state__title">No changes were made</h2>
    <p class="c-import-changes-list-empty-state__subtitle">The CSV you imported did not have any changes</p>
  </div>
</template>

<script>
import ImportChangeListItem from './ImportChangeListItem'

export default {
  components: {
    ImportChangeListItem
  },
  props: {
    'importResults': {
      type: Object,
      required: true
    }
  },
  computed: {
    resultsToShow () {
      return this.importResults.filter(result => result.importAction === 'added' || result.importAction === 'updated' || result.importAction === 'failed')
    }
  }
}
</script>
