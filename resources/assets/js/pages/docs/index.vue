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
          <b-card
            :title="$t('documentation.steps.one.support_card.title')"
            class="bg-grey d-inline-block w-auto p-2"
          >
            <p class="card-text">
              {{ $t('documentation.steps.one.support_card.body') }}
            </p>
          </b-card>
        </section>

        <section>
          <h2 class="steps" id="introduction">Swagger</h2>
          <b-card class="bg-light mb-4">
            <p><a :href="apiDocumentationUrl" target="_blank">Swagger of API</a></p>
          </b-card>
        </section>

        <!-- Base URL Section -->
        <section>
          <h2 class="steps" id="base-url">{{ $t('documentation.steps.two.heading') }}</h2>
          <b-card class="bg-light mb-4">
            <p><a :href="apiPrepareCenterUrl" target="_blank">{{ apiPrepareCenterUrl }}</a></p>
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
          <p class="u-text-wrap">
            The public WhatNow content API has two versions. <strong>V2 is the current API</strong> and exposes published WhatNow content as <strong>Prepare Messages</strong> through the <code>preparemessages</code> resource. <strong>V1 is legacy</strong> and remains available for older integrations that still call the <code>whatnow</code> resource.
          </p>

          <h3>V2 Prepare Messages API</h3>
          <p class="u-text-wrap">
            Use V2 for new integrations. V2 returns published preparedness messages grouped by stage. Each populated stage is an array of structured message objects, where <code>title</code> is the key safety message and <code>content</code> is a list of supporting messages.
          </p>
          <div class="table-responsive">
            <table class="table">
              <thead>
              <tr>
                <th>Endpoint</th>
                <th>Description</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td><code>GET /v2/org/{country_code}</code></td>
                <td>Returns the organisation metadata for the supplied country code.</td>
              </tr>
              <tr>
                <td><code>GET /v2/org/{country_code}/preparemessages</code></td>
                <td>Returns the published Prepare Messages feed for an organisation.</td>
              </tr>
              <tr>
                <td><code>GET /v2/preparemessages/{id}</code></td>
                <td>Returns one published Prepare Messages item by id.</td>
              </tr>
              </tbody>
            </table>
          </div>

          <p><strong>V2 feed query parameters</strong></p>
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
                <td>Optional event type filter. Multiple event types may be separated using a comma, e.g., <code>?eventType=earthquake,hurricane</code>.</td>
              </tr>
              <tr>
                <td>language</td>
                <td>Optional language filter. Use the ISO language code for the translation to return, e.g., <code>?language=en</code>. If omitted, V2 can also use the <code>Accept-Language</code> header when present.</td>
              </tr>
              <tr>
                <td>subnational</td>
                <td>Optional subnational filter. Use the subnational slug configured for the organisation, e.g., <code>?subnational=north-west</code>.</td>
              </tr>
              </tbody>
            </table>
          </div>

          <p><strong>V2 stage keys</strong></p>
          <ul>
            <li>immediate</li>
            <li>warning</li>
            <li>anticipated</li>
            <li>assess_and_plan</li>
            <li>mitigate_risk</li>
            <li>prepare_to_respond</li>
            <li>recover</li>
          </ul>

          <h3>V1 Legacy WhatNow API</h3>
          <p class="u-text-wrap">
            V1 is provided for backwards compatibility. Existing clients can continue to call the legacy WhatNow endpoints, but new clients should use V2 Prepare Messages endpoints instead.
          </p>
          <div class="table-responsive">
            <table class="table">
              <thead>
              <tr>
                <th>Endpoint</th>
                <th>Description</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td><code>GET /v1/org/{country_code}</code></td>
                <td>Returns legacy organisation metadata for the supplied country code.</td>
              </tr>
              <tr>
                <td><code>GET /v1/org/{country_code}/whatnow</code></td>
                <td>Returns the legacy published WhatNow feed for an organisation. The legacy feed supports <code>eventType</code> filtering.</td>
              </tr>
              <tr>
                <td><code>GET /v1/whatnow/{id}</code></td>
                <td>Returns one legacy published WhatNow item by id.</td>
              </tr>
              </tbody>
            </table>
          </div>
          <p><strong>V1 legacy stage keys</strong></p>
          <ul>
            <li>mitigation</li>
            <li>seasonalForecast</li>
            <li>watch</li>
            <li>warning</li>
            <li>immediate</li>
            <li>recover</li>
          </ul>
          <p class="u-text-wrap">
            V1 stage content is returned in the legacy stored format. V2 should be used when consumers need the current Prepare Messages structure with explicit <code>title</code> and <code>content</code> fields.
          </p>

          <!-- Example Response -->
          <p><strong>{{ $t('documentation.steps.eight.example_response') }}</strong></p>
          <button @click="toggleResponse" class="show-btn mb-3">
            {{ showResponse ? 'Hide Response' : 'Show Response' }}
          </button>
          <b-collapse :visible="showResponse">
            <pre><code class="language-json">{{ exampleResponse }}</code></pre>
          </b-collapse>

          <!-- Response Properties -->
          <p><strong>{{ $t('documentation.steps.eight.response_properties') }}</strong></p>
          <b-card class="bg-grey d-inline-block w-auto p-2">
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
                <td>warning</td>
                <td>array&lt;message object&gt; | null</td>
                <td>Messages for warning lead-time actions.</td>
              </tr>
              <tr>
                <td>immediate</td>
                <td>array&lt;message object&gt; | null</td>
                <td>Messages for actions during an active or imminent hazard.</td>
              </tr>
              <tr>
                <td>recover</td>
                <td>array&lt;message object&gt; | null</td>
                <td>Messages for recovery actions after a hazard event.</td>
              </tr>
              <tr>
                <td>anticipated</td>
                <td>array&lt;message object&gt; | null</td>
                <td>Messages for anticipated hazards and early preparedness actions.</td>
              </tr>
              <tr>
                <td>assess_and_plan</td>
                <td>array&lt;message object&gt; | null</td>
                <td>Messages for assessing risk and planning risk-reduction activities.</td>
              </tr>
              <tr>
                <td>mitigate_risk</td>
                <td>array&lt;message object&gt; | null</td>
                <td>Messages for longer-term mitigation measures that reduce hazard risk.</td>
              </tr>
              <tr>
                <td>prepare_to_respond</td>
                <td>array&lt;message object&gt; | null</td>
                <td>Messages for developing response capacity before a hazard event.</td>
              </tr>
              </tbody>
            </table>
          </div>

          <p><strong>Structure of a stage message object</strong></p>
          <p class="u-text-wrap">
            Each V2 stage value is either <code>null</code> when no messages are published for that stage, or an array of message objects. Each message object contains a priority safety message in <code>title</code> and zero or more supporting messages in <code>content</code>.
          </p>
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
                <td>title</td>
                <td>string</td>
                <td>The key safety message or priority action for the stage.</td>
              </tr>
              <tr>
                <td>content</td>
                <td>array&lt;string&gt;</td>
                <td>Supporting messages that add detail to the key safety message. This array may be empty.</td>
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
import axios from 'axios'

export default {
  data () {
    return {
      adminDocumentationUrl: null,
      apiDocumentationUrl: null,
      apiPrepareCenterUrl: null,
      prepareCenterUrl: null,
      showResponse: false,
      // --- API Tester Data ---
      apiKey: '',
      isLoading: false,
      isTesting: false,
      dataLoaded: false,
      organizations: [],
      eventTypes: [],
      selectedOrganization: null,
      selectedEventType: null,
      languageParam: '',
      subnationalParam: '',
      testResponse: null,
      testError: null,
      // --- End API Tester Data ---
      exampleResponse: `{
    "data": [
        {
            "id": "3012",
            "countryCode": "USA",
            "eventType": "Earthquake",
            "regionName": "National",
            "subnational": null,
            "attribution": {
                "name": "American Red Cross",
                "countryCode": "USA",
                "url": "https://www.redcross.org/get-help/how-to-prepare-for-emergencies/types-of-emergencies/coronavirus-safety/preparing-for-disaster-during-covid-19.html",
                "imageUrl": "https://smdbstorageaccount.blob.core.windows.net/images-stage/USA_logo.png",
                "translations": {
                    "en": {
                        "languageCode": "en",
                        "name": "American Red Cross",
                        "attributionMessage": "Key Messages from American Red Cross",
                        "contributors": [],
                        "published": true
                    }
                }
            },
            "translations": {
                "en": {
                    "id": "5632",
                    "lang": "en",
                    "webUrl": "https://www.redcross.org/get-help/how-to-prepare-for-emergencies/types-of-emergencies/earthquake.html",
                    "title": "Key Messages for Earthquake",
                    "description": "Take these actions to prepare for earthquakes so that you and your loved ones can reduce risk, stay safe, and recover quickly. Earthquakes strike suddenly and without warning.\\n\\nEarthquakes can bring mild to violent shaking and can occur anytime, anywhere. Earthquakes can happen at any time of the year and occur without warning. All U.S. states and territories are at some risk for earthquakes. The risk is higher in identified seismic zones. Larger earthquakes may cause deaths, injuries, and extensive property damage. Most casualties and injuries during an earthquake occur when: people fall while trying to walk or run during the shaking; when they are hit by falling, flying, or sliding household items or non-structural debris; and/or when they are struck or trapped by collapsing walls or other parts of the building. Transportation, power, water, gas, and other services may be disrupted. In some areas, shaking can cause liquefaction— when the ground acts more like a liquid. When this happens, the ground can no longer support the weight of a building. In coastal areas, earthquakes under the sea floor can cause tsunamis.\\n\\nThe COVID-19 pandemic continues to pose a serious public health risk. Know how to help protect yourself and others by reviewing safety tips at cdc.gov/coronavirus.",
                    "published": true,
                    "createdAt": "2020-08-17T22:50:18+00:00",
                    "stages": {
                        "warning": [
                            {
                                "title": "Earthquakes strike suddenly and with no warning. There is no way for scientists to predict the magnitude, timing, and location of a future earthquake. Always be prepared to respond at the first signs of shaking.",
                                "content": []
                            }
                        ],
                        "immediate": [
                            {
                                "title": "Drop, Cover, and Hold On when the earth shakes.  Drop: Wherever you are, drop down on to your hands and knees. If you’re using a wheelchair or walker with a seat, make sure your wheels are locked and remain seated until the shaking stops.  Cover: Cover your head and neck with your arms. If a sturdy table or desk is nearby, crawl underneath it for shelter. If no shelter is nearby, crawl next to an interior wall (away from windows). Crawl only if you can reach better cover without going through an area with more debris. Stay on your knees or bent over to protect vital organs.  Hold on: If you are under a table or desk, hold on with one hand and be ready to move with it if it moves. If seated and unable to drop to the floor, bend forward, cover your head with your arms and hold on to your neck with both hands.",
                                "content": []
                            },
                            {
                                "title": "In bed: Do not get out of bed. Lie face down to protect your vital organs, and Cover your head and neck with a pillow, keeping your arms as close to your head as possible. Hold On to your head and neck with both hands until shaking stops. You are less likely to be injured by fallen and broken objects by staying where you are.",
                                "content": []
                            },
                            {
                                "title": "In a high-rise: Drop, Cover, and Hold On. Avoid windows and other hazards. Do not use elevators. Do not be surprised if sprinkler systems or fire alarms activate.",
                                "content": []
                            },
                            {
                                "title": "Outdoors: Move to a clear area if you can safely do so. Avoid power lines, trees, signs, buildings, vehicles, and other hazards. Then, Drop, Cover, and Hold On. This protects you from any objects that may be thrown from the side, even if nothing is directly above you.",
                                "content": []
                            },
                            {
                                "title": "Driving: Pull over to the side of the road, stop, and set the parking brake. Avoid overpasses, bridges, power lines, signs and other hazards. Stay inside the vehicle until the shaking stops, then proceed carefully by avoiding fallen debris, cracked or shifted pavement, and emergency vehicles. If a power line falls on the car, stay inside until a trained person removes the wire.",
                                "content": []
                            },
                            {
                                "title": "Near the shore: As soon as the shaking reduces so that you are able to stand, walk quickly to high ground or inland, as a tsunami may arrive soon. Don’t wait for officials to issue a warning. Walk, rather than drive, to avoid traffic, debris, and other hazards.",
                                "content": []
                            },
                            {
                                "title": "Once the shaking has stopped, wait a minute before getting up and then look around for debris or other dangers. If you are able to safely move to exit the building and there is an open space to go to, exit the building and avoid damaged areas and downed power lines. For buildings in metropolitan areas that do not have nearby open space, it may be safer to remain in the building until you are certain you will avoid additional glass and debris that may fall from nearby buildings. Remember aftershocks may cause further damage to weakened structures and present hazards to those exiting buildings. Drop, Cover, and Hold On whenever you feel shaking.",
                                "content": []
                            },
                            {
                                "title": "If you are trapped, do not move about or kick up dust. Cover your mouth with a handkerchief or clothing. Shout only as a last resort. Shouting can cause you to inhale dangerous amounts of dust. Use your cell phone to call or text for help. Tap on a pipe or wall, or use a whistle, if available, so rescuers can locate you.",
                                "content": []
                            },
                            {
                                "title": "If you are in a damaged building and there is a safe way out through the debris, leave and go to an open space outside. If you can do so safely, take a moment to take what you might need immediately and can carry easily, such as a purse or evacuation kit. Once outside, do not re-enter until the building is certified to be safe.",
                                "content": []
                            },
                            {
                                "title": "Check for injuries and provide assistance if you have training.",
                                "content": []
                            },
                            {
                                "title": "Once safe, monitor local news reports (battery-operated radio, TV, and cell phone text alerts) for emergency information and instructions.",
                                "content": []
                            }
                        ],
                        "recover": null,
                        "anticipated": null,
                        "assess_and_plan": null,
                        "mitigate_risk": null,
                        "prepare_to_respond": null
                    }
                }
            }
        }
    ]
}`
    }
  },
  computed: {
    organizationOptions () {
      return this.organizations.map(org => ({ value: org.countryCode, text: org.name }))
    },
    eventTypeOptions () {
      return this.eventTypes.map(event => ({ value: event.code, text: event.name }))
    },
    finalUrl () {
      if (!this.selectedOrganization) {
        return ''
      }
      const url = `${this.apiPrepareCenterUrl}/org/${this.selectedOrganization}/preparemessages`
      const params = new URLSearchParams()
      if (this.selectedEventType) {
        params.append('eventType', this.selectedEventType)
      }
      if (this.languageParam) {
        params.append('language', this.languageParam)
      }
      if (this.subnationalParam) {
        params.append('subnational', this.subnationalParam)
      }
      const queryString = params.toString()
      return queryString ? `${url}?${queryString}` : url
    },
    formattedResponse () {
      return JSON.stringify(this.testResponse, null, 2)
    }
  },
  mounted () {
    const Appconfig = window.config || {}
    this.adminDocumentationUrl = `${Appconfig.url || ''}/admin/documentation`
    this.apiDocumentationUrl = `${Appconfig.api_url || ''}/api/documentation`
    this.apiPrepareCenterUrl = `${Appconfig.api_url || ''}/v2`
    this.prepareCenterUrl = `${Appconfig.url || ''}`
  },
  methods: {
    toggleResponse () {
      this.showResponse = !this.showResponse
    },

    async loadInitialData () {
      this.isLoading = true
      this.testError = null
      this.dataLoaded = false
      try {

        const orgsResponse = await axios.get(`${this.apiPrepareCenterUrl}/org`, {
          headers: { 'x-api-key': this.apiKey }
        })
        this.organizations = orgsResponse.data.data

        const eventTypesResponse = await axios.get(`${this.apiPrepareCenterUrl}/event-types/`)
        this.eventTypes = eventTypesResponse.data.data

        this.dataLoaded = true
      } catch (error) {
        this.testError = `Error al cargar los datos iniciales: ${error.response ? `${error.response.status} - ${error.response.statusText}` : error.message}`
      } finally {
        this.isLoading = false
      }
    },

    async testWhatNowEndpoint () {
      this.isTesting = true
      this.testResponse = null
      this.testError = null
      try {
        const response = await axios.get(this.finalUrl, {
          headers: { 'x-api-key': this.apiKey }
        })
        this.testResponse = response.data
      } catch (error) {
        this.testError = `Error al probar el endpoint: ${error.response ? `${error.response.status} - ${error.response.statusText}` : error.message}`
      } finally {
        this.isTesting = false
      }
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
  font-color: #000000;
}

.card {
  font-size: 1rem;
}

.show-btn {
  background: transparent;
  border: none;
  color: #F6333F;
  font-size: 1.2rem;
  font-weight: 600;
}

code {
  color: #F6333F;
  white-space: pre-wrap;
  word-break: break-all;
}

pre {
  background-color: #f8f9fa;
  padding: 1rem;
  border-radius: 0.25rem;
}

</style>
