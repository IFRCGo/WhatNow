export class ContentTranslation {
  constructor (
    {
      description = '',
      id = null,
      lang = '',
      title = '',
      stages = [],
      webUrl = ''
    }
  ) {
    this.description = description
    this.id = id
    this.stages = createStages(stages)
    this.lang = lang
    this.title = title
    this.webUrl = webUrl
  }
}

const createStages = (stages) => {
  return {
    seasonalForecast: stages.seasonalForecast || [],
    immediate: stages.immediate || [],
    mitigation: stages.mitigation || [],
    recover: stages.recover || [],
    warning: stages.warning || [],
    watch: stages.watch || []
  }
}
