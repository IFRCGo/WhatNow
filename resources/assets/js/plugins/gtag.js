export const GTAG_EVENTS = {
  CreateContentUser: 'create_content_user',
  CreateHazard: 'create_hazard',
  SignUp: 'signup',
  DownloadAuditLogReport: 'download_audit_log_report',
  DownloadBulkTemplate: 'download_bulk_template',
  DownloadExportTemplateGuide: 'download_export_template_guide',
  ExportLanguageData: (locale) => `export_${locale}_data`,
  BulkDataImport: 'bulk_data_import',
  EditHazard: 'edit_hazard',
  EditAttribution: 'edit_attribution',
  PublishContent: 'publish_content',
  NewApp: 'new_app',
  FeedbackLink: 'feedback_link',
  PrivacyLink: 'privacy_link',
  DownloadAPIReport: 'download_api_report',
  CreateRegion: 'create_region'
}

const GTM_DEBUG = false

const TscGTAG = {
  install: (Vue) => {
    Vue.prototype.$fireGTEvent = (name, { param, rest = {}} = {}) => {
      if (!name) {
        console.log('An Event name is required to fire a GTAG Event')
      }

      const payload = Object.assign(
        {},
        rest,
        param && { param: param },
        { event: name }
      )

      if (window.dataLayer && (process.env.NODE_ENV === 'production' || GTM_DEBUG)) {
        window.dataLayer.push(payload)
      } else {
        console.log('GTAG Event', payload)
      }
    }

    Vue.prototype.$gtagEvents = GTAG_EVENTS
  }
}

export default TscGTAG
