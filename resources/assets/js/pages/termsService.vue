<template>
  <b-container fluid>
    <page-banner>
      <b-col>
        <h1>{{ $t('terms_service') }}</h1>
      </b-col>
    </page-banner>
    <b-row class="pl-4 pr-4 pb-4 pt-4 bg-white">
      <b-col>
        <p class="text-center">
          <b>{{ $t('terms_service_page.heading') }}</b>
        </p>

        <i18n path="terms_service_page.overview.body" tag="p">
          <b-link href="http://www.redcross.org/terms-and-conditions" target="_blank" rel="noreferrer noopener">http://www.redcross.org/terms-and-conditions</b-link>
          <router-link :to="{ name: 'register' }">{{ $t('terms_service_page.overview.cta') }}</router-link>
        </i18n>

        <p class="u-text-wrap">{{ $t('terms_service_page.intro') }}</p>

        <p>
          <b>{{ $t('terms_service_page.license_limitations.heading') }}</b> {{ $t('terms_service_page.license_limitations.body') }}
        </p>

        <p class="u-text-wrap">{{ $t('terms_service_page.body') }}</p>

        <p>
          <b>{{ $t('terms_service_page.prohibited_activities.heading') }}</b> {{ $t('terms_service_page.prohibited_activities.body') }}
          <ul>
            <li>
              {{ $t('terms_service_page.prohibited_activities.prohibited_list.one') }}
            </li>

            <li>
              {{ $t('terms_service_page.prohibited_activities.prohibited_list.two') }}
            </li>

            <li>
              {{ $t('terms_service_page.prohibited_activities.prohibited_list.three') }}
            </li>

            <li>
              {{ $t('terms_service_page.prohibited_activities.prohibited_list.four') }}
            </li>

            <li>
              {{ $t('terms_service_page.prohibited_activities.prohibited_list.five') }}
            </li>

            <li>
              {{ $t('terms_service_page.prohibited_activities.prohibited_list.six') }}
            </li>
          </ul>
        </p>

        <p class="text-uppercase">
          <b class="u-text-wrap">{{ $t('terms_service_page.disclaimer') }}</b>
        </p>

        <p>{{ $t('terms_service_page.reserve_rights') }}</p>

        <p>
          <b>{{ $t('terms_service_page.suspension.heading') }}</b> {{ $t('terms_service_page.suspension.body') }}
        </p>

        <p>
          <b>{{ $t('terms_service_page.privacy_policy.heading') }}</b>
          <i18n path="terms_service_page.privacy_policy.body" tag="span">
            <b-link href="https://www.preparecenter.org/content/privacy-policy" target="_blank" rel="noreferrer noopener">https://www.preparecenter.org/content/privacy-policy</b-link>
          </i18n>
        </p>

        <p>
          <b>{{ $t('terms_service_page.severability.heading') }}</b> {{ $t('terms_service_page.severability.body') }}
        </p>

        <p>
          <b>{{ $t('terms_service_page.applicable_law.heading') }}</b> {{ $t('terms_service_page.applicable_law.body') }}
        </p>

        <p>
          <b>{{ $t('terms_service_page.entire_agreement.heading') }}</b> {{ $t('terms_service_page.entire_agreement.body') }}
        </p>

        <i18n path="terms_service_page.contact" tag="p">
          <b-link href="mailto:Jessica.Robbins@redcross.org?Subject=GDPC%20Admin%20Portal">Jessica.Robbins@redcross.org</b-link>
        </i18n>
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
import PageBanner from '~/components/PageBanner'
export default {
  components: {
    PageBanner
  }
}
</script>
