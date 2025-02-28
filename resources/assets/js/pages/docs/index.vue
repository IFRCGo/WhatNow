<template>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="doc-contents sidebar col-2 col-md-3 col-xl-2 pt-3">
        <b-nav vertical v-b-scrollspy>
          <b-nav-item href="#introduction">{{ $t('documentation.sidebar.introduction') }}</b-nav-item>
          <b-nav-item href="#base-url">{{ $t('documentation.sidebar.base_url') }}</b-nav-item>
          <b-nav-item href="#changelog">{{ $t('documentation.sidebar.changelog') }}</b-nav-item>
          <b-nav-item href="#authentication">{{ $t('documentation.sidebar.authentication') }}</b-nav-item>
          <b-nav-item href="#response-codes">{{ $t('documentation.sidebar.response_codes') }}</b-nav-item>
          <b-nav-item href="#what-now">{{ $t('documentation.sidebar.what_now') }}</b-nav-item>
        </b-nav>
      </div>

      <!-- Main Content -->
      <div class="doc-body bg-white col-12 col-md-9 col-xl-10 p-3 p-md-5">
        <h1>{{ $t('documentation.heading') }}</h1>

        <!-- Introduction Section -->
        <section>
          <h2 class="steps" id="introduction">{{ $t('documentation.steps.one.heading') }}</h2>
          <p class="lead">{{ $t('documentation.steps.one.body') }}</p>
          <b-card :title="$t('documentation.steps.one.support_card.title')" bg-variant="light">
            <p class="card-text">{{ $t('documentation.steps.one.support_card.body') }}</p>
          </b-card>
          <hr>
        </section>

        <!-- Base URL Section -->
        <section>
          <h2 class="steps" id="base-url">{{ $t('documentation.steps.two.heading') }}</h2>
          <b-card class="bg-light mb-4">
            <pre>https://api.preparecenter.org/v1</pre>
          </b-card>
          <p>{{ $t('documentation.steps.two.body') }}</p>
        </section>

        <!-- Authentication Section -->
        <section>
          <h2 class="steps" id="authentication">{{ $t('documentation.steps.four.heading') }}</h2>
          <i18n path="documentation.steps.four.body" tag="p" class="u-text-wrap">
            <router-link :to="{ path: 'applications' }">{{ $t('documentation.steps.four.create_app_cta') }}</router-link>
          </i18n>
          <pre>x-api-key: your_application_api_key</pre>
        </section>

        <!-- Response Codes Section -->
        <section>
          <h2 class="steps" id="response-codes">{{ $t('documentation.steps.five.heading') }}</h2>
          <div class="table-responsive">
            <table>
              <thead>
              <tr>
                <th>{{ $t('documentation.steps.five.table.headers.code') }}</th>
                <th>{{ $t('documentation.steps.five.table.headers.name') }}</th>
                <th>{{ $t('documentation.steps.five.table.headers.description') }}</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td>200</td>
                <td>OK</td>
                <td>{{ $t('documentation.steps.five.table.descriptions.ok') }}</td>
              </tr>
              <tr>
                <td>302</td>
                <td>Found</td>
                <td>{{ $t('documentation.steps.five.table.descriptions.found') }}</td>
              </tr>
              <tr>
                <td>404</td>
                <td>Not Found</td>
                <td>{{ $t('documentation.steps.five.table.descriptions.not_found') }}</td>
              </tr>
              <tr>
                <td>405</td>
                <td>Method Not Allowed</td>
                <td>{{ $t('documentation.steps.five.table.descriptions.not_allowed') }}</td>
              </tr>
              <tr>
                <td>500</td>
                <td>Server Error</td>
                <td>{{ $t('documentation.steps.five.table.descriptions.server_error') }}</td>
              </tr>
              <tr>
                <td>503</td>
                <td>Service Unavailable</td>
                <td>{{ $t('documentation.steps.five.table.descriptions.unavailable') }}</td>
              </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- What Now Section -->
        <section>
          <h2 class="steps" id="what-now">{{ $t('documentation.steps.eight.heading') }}</h2>
          <p class="u-text-wrap">{{ $t('documentation.steps.eight.body') }}</p>
          <ul>
            <li>immediate</li>
            <li>warning</li>
            <li>anticipated</li>
            <li>assess_and_plan</li>
            <li>mitigate_risks</li>
            <li>prepare_to_respond</li>
            <li>recover</li>
          </ul>
          <p><strong>{{ $t('documentation.steps.eight.endpoint') }}</strong></p>
          <pre>GET /org/{country_code}/whatnow?eventType={eventType}</pre>

          <!-- Query Parameters Table -->
          <p><strong>{{ $t('documentation.steps.eight.query_params') }}</strong></p>
          <div class="table-responsive">
            <table class="table">
              <thead>
              <tr>
                <th>{{ $t('documentation.steps.eight.table.query_params.name') }}</th>
                <th>{{ $t('documentation.steps.eight.table.query_params.description') }}</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td>eventType</td>
                <td>Optional event type filter. Multiple event types may be separated using a comma, e.g., <code>?eventType=earthquake,hurricane</code></td>
              </tr>
              <tr>
                <td>language</td>
                <td>Optional language filter. Specify the language code, e.g., <code>?language=en</code></td>
              </tr>
              <tr>
                <td>region</td>
                <td>Optional region filter. Specify the region name, e.g., <code>?region=north%20west</code></td>
              </tr>
              </tbody>
            </table>
          </div>

          <!-- Example Response -->
          <p><strong>{{ $t('documentation.steps.eight.example_response') }}</strong></p>
          <b-button @click="toggleResponse" variant="outline-primary mb-3">
            {{ showResponse ? 'Hide Response' : 'Show Response' }}
          </b-button>
          <b-collapse :visible="showResponse">
            <pre><code class="language-json">{{ exampleResponse }}</code></pre>
          </b-collapse>

          <!-- Response Properties -->
          <p><strong>{{ $t('documentation.steps.eight.response_properties') }}</strong></p>
          <b-card bg-variant="light">
            <i18n path="documentation.steps.eight.noteDetails" tag="p" class="card-text">
              <strong>{{ $t('documentation.steps.eight.note') }}</strong>
              <code>data</code>
            </i18n>
          </b-card>

          <!-- Structure of What Now Item Objects -->
          <p>{{ $t('documentation.steps.eight.structure') }}</p>
          <div class="table-responsive">
            <table class="table">
              <thead>
              <tr>
                <th>{{ $t('documentation.steps.eight.table.structure.property') }}</th>
                <th>{{ $t('documentation.steps.eight.table.structure.data_type') }}</th>
                <th>{{ $t('documentation.steps.eight.table.structure.description') }}</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td>id</td>
                <td>string</td>
                <td>Id of an item</td>
              </tr>
              <tr>
                <td>countryCode</td>
                <td>string</td>
                <td>Country code of issuing organization</td>
              </tr>
              <tr>
                <td>regionName</td>
                <td>string</td>
                <td>Name of the regional area the event type applies to, default is 'National'</td>
              </tr>
              <tr>
                <td>eventType</td>
                <td>string</td>
                <td>Event type this information applies to</td>
              </tr>
              <tr>
                <td>webUrl</td>
                <td>string</td>
                <td>Url of issuing organization</td>
              </tr>
              <tr>
                <td>attribution</td>
                <td>object</td>
                <td>Object containing attribution properties</td>
              </tr>
              <tr>
                <td>translations</td>
                <td>object</td>
                <td>Object containing translation objects</td>
              </tr>
              <tr>
                <td>createdAt</td>
                <td>datetime</td>
                <td>Creation datetime (ISO 8601)</td>
              </tr>
              <tr>
                <td>updatedAt</td>
                <td>datetime</td>
                <td>Last updated datetime (ISO 8601)</td>
              </tr>
              </tbody>
            </table>
          </div>

          <!-- Structure of Attribution Object -->
          <i18n path="documentation.steps.eight.stucture_attribution" tag="p">
            <code>attribution</code>
          </i18n>
          <div class="table-responsive">
            <table class="table">
              <thead>
              <tr>
                <th>{{ $t('documentation.steps.eight.table.structure.property') }}</th>
                <th>{{ $t('documentation.steps.eight.table.structure.data_type') }}</th>
                <th>{{ $t('documentation.steps.eight.table.structure.description') }}</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td>url</td>
                <td>string</td>
                <td>Url of the attributed organization website</td>
              </tr>
              <tr>
                <td>imageUrl</td>
                <td>string</td>
                <td>Url to an image for the attributed organization</td>
              </tr>
              <tr>
                <td>translations</td>
                <td>object</td>
                <td>Object containing translation properties</td>
              </tr>
              </tbody>
            </table>
          </div>

          <!-- Structure of Translation Object -->
          <i18n path="documentation.steps.eight.stucture_translation" tag="p">
            <code>translation</code>
          </i18n>
          <div class="table-responsive">
            <table class="table">
              <thead>
              <tr>
                <th>{{ $t('documentation.steps.eight.table.structure.property') }}</th>
                <th>{{ $t('documentation.steps.eight.table.structure.data_type') }}</th>
                <th>{{ $t('documentation.steps.eight.table.structure.description') }}</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td>mitigation</td>
                <td>array of strings</td>
                <td>Mitigation preparation steps</td>
              </tr>
              <tr>
                <td>seasonalForecast</td>
                <td>array of strings</td>
                <td>Seasonal Forecast preparation steps</td>
              </tr>
              <tr>
                <td>warning</td>
                <td>array of strings</td>
                <td>Warning preparation steps</td>
              </tr>
              <tr>
                <td>watch</td>
                <td>array of strings</td>
                <td>Watch preparation steps</td>
              </tr>
              <tr>
                <td>immediate</td>
                <td>array of strings</td>
                <td>Immediate preparation steps</td>
              </tr>
              <tr>
                <td>recover</td>
                <td>array of strings</td>
                <td>Recover preparation steps</td>
              </tr>
              <tr>
                <td>anticipated</td>
                <td>array of strings</td>
                <td>Anticipated preparation steps</td>
              </tr>
              <tr>
                <td>assess_and_plan</td>
                <td>array of strings</td>
                <td>Assess and Plan preparation steps</td>
              </tr>
              <tr>
                <td>mitigate_risks</td>
                <td>array of strings</td>
                <td>Mitigate Risks preparation steps</td>
              </tr>
              <tr>
                <td>prepare_to_respond</td>
                <td>array of strings</td>
                <td>Prepare to Respond preparation steps</td>
              </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      showResponse: false,
      exampleResponse: `{
        "data": [
          {
            "id": "581",
            "countryCode": "USA",
            "eventType": "Earthquake",
            "regionName": "National",
            "region": null,
            "attribution": {
              "name": "American Red Cross",
              "countryCode": "USA",
              "url": "https://whatnow.jazusoft.com/admin/content/whatnow/USA",
              "imageUrl": "https://whatnowimages.blob.core.windows.net/images/USA_logo.jpg",
              "translations": {
                "de": {
                  "languageCode": "de",
                  "name": "Tests",
                  "attributionMessage": "Testss",
                  "contributors": [],
                  "published": false
                },
                "en": {
                  "languageCode": "en",
                  "name": "International Federation of Red Cross and Red Crescent Societies",
                  "attributionMessage": "Key Messages from International Federation of Red Cross and Red Crescent Societies. Editado",
                  "contributors": [
                    {
                      "id": 1,
                      "name": "Test",
                      "logo": "https://whatnowimages.blob.core.windows.net/images/USA_en_contributor_logo0.jpg"
                    }
                  ],
                  "published": true
                },
                "es": {
                  "languageCode": "es",
                  "name": "American Red Cross",
                  "attributionMessage": "Test",
                  "contributors": [],
                  "published": false
                }
              }
            },
            "translations": {
              "en": {
                "id": "3276",
                "lang": "en",
                "webUrl": "",
                "title": "Key Messages for Earthquake",
                "description": "These are actions to take to reduce risk and protect you and your household from earthquakes.",
                "published": true,
                "createdAt": "2017-02-27T19:06:31+00:00",
                "stages": {
                  "warning": null,
                  "immediate": null,
                  "recover": null,
                  "anticipated": null,
                  "assess_and_plan": null,
                  "mitigate_risks": null,
                  "prepare_to_respond": null
                }
              },
              "es": {
                "id": "3277",
                "lang": "es",
                "webUrl": "",
                "title": "Mensajes clave para terremoto",
                "description": "Estas son medidas para reducir el riesgo y proteger a su familia y a usted mismo de los terremotos.",
                "published": true,
                "createdAt": "2017-02-27T19:06:31+00:00",
                "stages": {
                  "warning": null,
                  "immediate": null,
                  "recover": null,
                  "anticipated": null,
                  "assess_and_plan": null,
                  "mitigate_risks": null,
                  "prepare_to_respond": null
                }
              }
            }
          }
        ]
      }`
    }
  },
  methods: {
    toggleResponse() {
      this.showResponse = !this.showResponse;
    }
  }
}
</script>
<style lang="scss" scoped>
body {
  position: relative;
}

.steps {
  font-weight: 400;
  font-size: 36px;
  line-height: 34px;
  font: #000000;
}
</style>
