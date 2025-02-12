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
    immediate: stages.immediate || [],
    warning: stages.warning || [],
    anticipated: stages.anticipated || [],
    assess_and_plan: stages.assess_and_plan || [],
    mitigate_risks: stages.mitigate_risks || [],
    prepare_to_respond: stages.prepare_to_respond || [],
    recover: stages.recover || []
  }
}
