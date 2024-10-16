<template>

  <div class="container-fluid">
    <div class="row">

      <div class="doc-contents sidebar col-2 col-md-3 col-xl-2 pt-3">
        <b-nav vertical v-b-scrollspy>
          <b-nav-item href="#step1">{{ $t('get_started_docs.sidebar.sign_up') }}</b-nav-item>
          <b-nav-item href="#step2">{{ $t('get_started_docs.sidebar.create_app') }}</b-nav-item>
          <b-nav-item href="#step3">{{ $t('get_started_docs.sidebar.get_api_key') }}</b-nav-item>
          <b-nav-item href="#step4">{{ $t('get_started_docs.sidebar.make_request') }}</b-nav-item>
        </b-nav>
      </div>

      <div class="doc-body bg-white col-12 col-md-9 col-xl-10 p-3 p-md-5">
        <h1>{{ $t('get_started_docs.title') }}</h1>

        <section>
          <h2 id="step1">{{ $t('get_started_docs.steps.one.heading') }}</h2>

          <i18n path="get_started_docs.steps.one.body" tag="p" class="u-text-wrap">
            <router-link :to="{ path: 'register' }">{{ $t('get_started_docs.steps.one.sign_up_cta') }}</router-link>
          </i18n>

          <router-link :to="{ path: 'register' }">
            <b-img :src="images.createAccount" border rounded fluid width="1000" height="350" :alt="$t('get_started_docs.steps.one.create_account_alt')" blank-color="#777" class="m-1"/>
          </router-link>

          <i18n path="get_started_docs.steps.one.existing_account" tag="p" class="u-text-wrap">
            <router-link :to="{ path: 'login' }">{{ $t('get_started_docs.steps.one.login_cta') }}</router-link>
          </i18n>

        </section>
        <section>
          <h2 id="step2">{{ $t('get_started_docs.steps.two.heading') }}</h2>
          <p>{{ $t('get_started_docs.steps.two.body') }}</p>

          <b-img :src="images.createApp" border rounded fluid width="1000" height="350" blank-color="#777" :alt="$t('get_started_docs.steps.two.create_app_alt')" class="m-1"/>
        </section>

        <section>
          <h2 id="step3">{{ $t('get_started_docs.steps.three.heading') }}</h2>
          <b-img :src="images.createApi" border rounded fluid width="1000" height="336" blank-color="#777" :alt="$t('get_started_docs.steps.three.create_api_key_alt')" class="m-1"/>

          <i18n path="get_started_docs.steps.three.body" tag="p" class="u-text-wrap">
            <strong>{{ $t('get_started_docs.steps.three.important') }}</strong>
          </i18n>
        </section>

        <section>
          <h2 id="step4">{{ $t('get_started_docs.steps.four.heading') }}</h2>

          <p class="u-text-wrap">{{ $t('get_started_docs.steps.four.body') }}</p>

          <b-card :header="$t('get_started_docs.steps.four.example_title')">
            <pre class="card-text"><code>
curl --request GET \
    --url https://api.preparecenter.org/v1/org/nzl/alerts \
    --header "x-api-key: your-api-key"
            </code></pre>
          </b-card>

          <i18n path="get_started_docs.steps.four.moreInfo" tag="p">
            <router-link :to="{ path: 'docs' }">{{$t('get_started_docs.steps.four.api_docs_cta')}}</router-link>
          </i18n>
        </section>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
  body {
    position: relative;
  }
</style>

<script>
export default {
  loading: false,
  data () {
    return {
      images: {
        createAccount: require('../../../img/get_started/create_account.jpg'),
        createApp: require('../../../img/get_started/create_app.jpg'),
        createApi: require('../../../img/get_started/create_apikey.jpg')
      }
    }
  }
}
</script>
