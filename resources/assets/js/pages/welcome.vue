<template>
  <div>
    <navbar></navbar>

    <section class="illustrated-row bg-white d-flex align-items-center full-height container-fluid pr-5 pl-5">
      <b-row class="w-100">
        <b-col md="9" lg="7" xl="6">
          <h2>
            {{ $t('landing.strapline')}}
          </h2>
          <h3 class="mb-5">
            {{ $t('landing.strapline_part_2')}}
          </h3>

          <b-tabs class="styled-tabs styled-tabs--header" v-model="activeTab">
            <b-tab :title="$t('register_form.developer')" active>
              <p>
                {{ $t('landing.developer.strap_para_one')}}
              </p>
              <p>
                {{ $t('landing.developer.strap_para_two')}}
              </p>
            </b-tab>
            <b-tab :title="$t('register_form.national_society')">
              <p>
                {{ $t('landing.national_society.strap_para_one')}}
              </p>
              <p>
                {{ $t('landing.national_society.strap_para_two')}}
              </p>
            </b-tab>
          </b-tabs>

          <b-button
            size="lg"
            variant="dark"
            :to="{ name: 'register', params: { userType: activeTab === 0 ? 'developer' : 'nationalSociety' } }"
            class="text-uppercase mr-2 mb-4 rtl-float-right">
            {{ $t('get_started') }}
          </b-button>

          <b-button size="lg" variant="dark"
            to="#learnMore"
            class="text-uppercase mb-4 rtl-float-right">
            {{ $t('common.learn_more')}}
          </b-button>
        </b-col>
      </b-row>
    </section>

    <section class="use-case-section bg-dark text-white text-center container-fluid" id="learnMore">
      <h2 class="mb-5">{{ $t('landing.use_cases.heading') }}</h2>
      <b-row class="flex-wrap use-case-row">
        <b-col md>
          <a href="https://www.preparecenter.org/activities/whatnow-service" target="_blank" rel="noreferrer nofollow" class="u-link u-link--white">
            <img role="presentation" class="case-card" :src="src('handsIcon')" :srcSet="srcSet('handsIcon')">
            <h3 class="case-card__heading mb-3">
              {{ $t('landing.use_cases.first_heading') }}
            </h3>
          </a>
        </b-col>

        <b-col md >
          <a href="https://www.preparecenter.org/resources/whatnow-service-use-case-google-safety-tips" target="_blank" rel="noreferrer nofollow" class="u-link u-link--white">
            <img role="presentation" class="case-card" src="../../img/landing_page/learn_more/use_cases.svg">
            <h3 class="case-card__heading mb-3">
              {{ $t('landing.use_cases.second_heading') }}
            </h3>
          </a>
        </b-col>

        <b-col md >
          <a href="https://www.preparecenter.org/toolkit/whatnow-service-toolkit" target="_blank" rel="noreferrer nofollow" class="u-link u-link--white">
            <img role="presentation" class="case-card" src="../../img/landing_page/learn_more/implementation_toolkit.svg">
            <h3 class="case-card__heading mb-3">
              {{ $t('landing.use_cases.third_heading') }}
            </h3>
          </a>
        </b-col>

        <b-col md >
          <a href="https://www.youtube.com/watch?v=Tc0P1kX5xJA" target="_blank" rel="noreferrer nofollow" class="u-link u-link--white">
            <img role="presentation" class="case-card" src="../../img/landing_page/learn_more/whatnow_video.svg">
            <h3 class="case-card__heading mb-3">
              {{ $t('landing.use_cases.fourth_heading') }}
            </h3>
          </a>
        </b-col>
      </b-row>
    </section>

    <section class="container-fluid p-5 bg-white">
      <h2 class="mb-5 mt-5 text-center">{{ $t('landing.example_alert.heading') }}</h2>

      <b-row class="example-panel">
        <b-col md="5" class="pr-0">
          <div class="example-panel__left h-100 py-5">

            <b-tabs class="styled-tabs">
              <b-tab :title="$t('landing.example_alert.alert_preview')">
                <div class="example-panel__inner pl-5 pr-5">
                  <div class="fake-alert mx-auto mt-4 px-3 pt-4">
                    <img :src="src('coldIcon')" :srcset="srcSet('coldIcon')" class="alert-icon alert-icon--small embed-responsive embed-responsive-1by1 mb-2"/>

                    <span class="fake-alert__timestamp">{{ $t('landing.fake_alert_time') }} <br/><span class="fake-alert__timestamp__exact">{{ $t('landing.fake_alert_time_exact') }}</span></span>
                    <h4 class="mt-2">{{ $t('landing.fake_alert_heading') }}</h4>

                    <p class="fake-alert__content">
                      {{ $t('landing.example_alert.example_content') }}
                    </p>

                    <p class="fake-alert__content fake-alert__content__location_header">
                      {{ $t('landing.example_alert.example_content_location_header') }}
                    </p>

                    <p class="fake-alert__content">
                      {{ $t('landing.example_alert.example_content_location') }}
                    </p>

                  </div>
                </div>

              </b-tab>
              <b-tab :title="$t('landing.example_alert.what_now_info')" active>
                <div class="example-panel__inner pl-5 pr-5 mx-auto mt-4">
                  <div class="whatnow-card orange">
                    <div class="whatnow-card-header">
                      <img :src="src('coldIcon')" :srcset="srcSet('coldIcon')" class="alert-icon alert-icon--small embed-responsive embed-responsive-1by1 mb-2"/>
                      <h6>{{$t('landing.example_whatnow.alert_from')}} <br/>{{$t('landing.example_whatnow.more_info')}}: <a href="hhttp://www.redcross.org" target="_blank">http://www.redcross.org</a></h6>
                    </div>
                    <h4 class="mb-3">{{ $t('landing.fake_alert_heading') }}</h4>
                    <p>{{$t('landing.example_whatnow.heading')}}</p>

                    <p>
                      {{$t('landing.example_whatnow.copy')}}:
                    </p>

                    <h5 class="my-3">{{$t('landing.example_whatnow.what_now')}}</h5>
                    <ol class="whatnow-card__list">
                      <li>{{$t('landing.example_whatnow.step_1')}}</li>
                      <li>{{$t('landing.example_whatnow.step_2')}}</li>
                      <li>{{$t('landing.example_whatnow.step_3')}}</li>
                      <li>{{$t('landing.example_whatnow.step_4')}}</li>
                      <li>{{$t('landing.example_whatnow.step_5')}}</li>
                    </ol>
                  </div>

                </div>
              </b-tab>
            </b-tabs>
          </div>
        </b-col>

        <b-col md="7" class="pl-0">
          <div class="example-panel__right h-100 pb-4 pt-3 example-panel__inner pl-5 pr-5">
            <p>
              {{ $t('landing.example_alert.example_alert_request') }}
            </p>
            <pre class="styled-pre pre-scrollable mb-2">
              <span>{</span>
              <span>    "data": {</span>
              <span>        "identifier": "urn:oid:2.49.1.174.2018.524",</span>
              <span>        "sender": "The_Americian_Red_Cross",</span>
              <span>        "sent": "2018-01-29T04:37:49+00:00",</span>
              <span>        "status": "Actual",</span>
              <span>        "msg_type": "Alert",</span>
              <span>        "scope": "Public",</span>
              <span>        "info": {</span>
              <span>            "language": "en",</span>
              <span>            "category": "Geo",</span>
              <span>            "event": "Extreme Cold Warning",</span>
              <span>            "urgency": "Immediate",</span>
              <span>            "severity": "Extreme",</span>
              <span>            "certainty": "Immediate",</span>
              <span>            "effective": "2018-01-29T03:00:00+00:00",</span>
              <span>            "onset": "2018-01-29T03:00:00+00:00",</span>
              <span>            "expires": "2018-04-03T13:37:29+00:00",</span>
              <span>            "sender_name": "The American Red Cross",</span>
              <span>            "headline": "Weather Advisory# 3 FINAL as of 11 AM, Jan. 29, 2018For Tail-End of A Cold Front ",</span>
              <span>            "description": "Weather Advisory# 3 FINAL as of 11 AM, Jan. 29, 2018\nFor Tail-End of A Cold Front \n\nThe effect of the Tail-End of A Cold Front has weakened. Improved weather conditions will be experienced over Nome. However, isolated rain showers or thunderstorms, especially during the afternoon or evening, should be expected in these areas due to the west.\n\nWith this development, this is the final advisory for this weather system.",</span>
              <span>            "area": {</span>
              <span>                "area_desc": "Weather Advisory# 3 FINAL as of 11 AM, Jan. 29, 2018\nFor Tail-End of A Cold Front ",</span>
              <span>                "polygon": {</span>
              <span>                    "type": "Feature",</span>
              <span>                    "geometry": {</span>
              <span>                        "type": "Polygon",</span>
              <span>                        "coordinates": [</span>
              <span>                              [</span>
              <span>                                [</span>
              <span>                                  -165.5584716796875,</span>
              <span>                                  64.9188850328549</span>
              <span>                                ],</span>
              <span>                                [</span>
              <span>                                  -166.06933593749997,</span>
              <span>                                  64.81389502168176</span>
              <span>                                ],</span>
              <span>                                [</span>
              <span>                                  -166.28082275390625,</span>
              <span>                                  64.5212788590022</span>
              <span>                                ],</span>
              <span>                                [</span>
              <span>                                  -166.0528564453125,</span>
              <span>                                  64.3042040393387</span>
              <span>                                ],</span>
              <span>                                [</span>
              <span>                                  -165.02563476562497,</span>
              <span>                                  64.28514307507955</span>
              <span>                                ],</span>
              <span>                                [</span>
              <span>                                  -164.5916748046875,</span>
              <span>                                  64.42659403500944</span>
              <span>                                ],</span>
              <span>                                [</span>
              <span>                                  -164.5751953125,</span>
              <span>                                  64.74953235864658</span>
              <span>                                ],</span>
              <span>                                [</span>
              <span>                                  -164.94049072265625,</span>
              <span>                                  64.91306300771964</span>
              <span>                                ],</span>
              <span>                                [</span>
              <span>                                  -165.5584716796875,</span>
              <span>                                  64.9188850328549</span>
              <span>                                ]</span>
              <span>                              ]</span>
              <span>                            ]</span>
              <span>                        ]</span>
              <span>                    }</span>
              <span>                }</span>
              <span>            }</span>
              <span>        },</span>
              <span>        "cap_url": "http://localhost/v1/alerts/cap12/2018-01-29/urn:oid:2.49.1.174/urn:oid:2.49.1.174.2018.524.xml",</span>
              <span>        "organisation": {</span>
              <span>            "country": "USA",</span>
              <span>            "name": "The American Red Cross",</span>
              <span>            "oid_code": "urn:oid:2.49.1.174"</span>
              <span>        }</span>
              <span>    }</span>
              <span>}</span>
            </pre>

            <p>
              {{ $t('landing.example_alert.example_whatnow_request') }}
            </p>
            <pre class="styled-pre pre-scrollable mb-2">
              <span>{</span>
              <span>    "data": {</span>
              <span>        "id": "234",</span>
              <span>        "countryCode": "USA",</span>
              <span>        "eventType": "Extreme Cold",</span>
              <span>        "attribution": {</span>
              <span>            "name": "American Red Cross",</span>
              <span>            "countryCode": "USA",</span>
              <span>            "url": "http://www.redcross.org",</span>
              <span>            "translations": {</span>
              <span>                "en": {</span>
              <span>                    "languageCode": "en",</span>
              <span>                    "name": "International Federation of Red Cross and Red Crescent Societies",</span>
              <span>                    "attributionMessage": "Key Messages from International Federation of Red Cross and Red Crescent Societies"</span>
              <span>                }</span>
              <span>            }</span>
              <span>        },</span>
              <span>        "translations": {</span>
              <span>            "en": {</span>
              <span>                "id": "483",</span>
              <span>                "lang": "en",</span>
              <span>                "webUrl": "",</span>
              <span>                "title": "Key Messages for Extreme Cold",</span>
              <span>                "description": "These are actions to take to reduce risk and protect you and your household from extreme cold.",</span>
              <span>                "published": true,</span>
              <span>                "createdAt": "2017-02-17T17:38:40+00:00",</span>
              <span>                "stages": {</span>
              <span>                    "mitigation": [</span>
              <span>                        "Know your area's risks for extreme weather conditions",</span>
              <span>                        "Know your nearest points of medical support",</span>
              <span>                        "Prepare your home. Check on pipes and have safe heating sources.",</span>
              <span>                        "Prepare your vehicle with necessary emergency items and fuel"</span>
              <span>                    ],</span>
              <span>                    "seasonalForecast": [</span>
              <span>                        "Prepare your home. Check on pipes and have safe heating sources.",</span>
              <span>                        "Prepare your vehicle with necessary emergency items and fuel",</span>
              <span>                        "Stay hydrated with warm fluids",</span>
              <span>                        "Check on neighbors, friends, and those at risk"</span>
              <span>                    ],</span>
              <span>                    "warning": [</span>
              <span>                        "Protect your home. Check on pipes and use heating sources safely",</span>
              <span>                        "Stay hydrated with warm fluids",</span>
              <span>                        "Protect yourself with warm clothing and cover exposed skin",</span>
              <span>                        "Be aware and cautious of icy conditions, especially while driving",</span>
              <span>                        "Check on neighbors, friends, and those at risk"</span>
              <span>                    ],</span>
              <span>                    "watch": [</span>
              <span>                        "Protect your home. Check on pipes and use heating sources safely.",</span>
              <span>                        "Stay hydrated with warm fluids",</span>
              <span>                        "Protect yourself with warm clothing and cover exposed skin",</span>
              <span>                        "Be aware and cautious of icy conditions, especially while driving",</span>
              <span>                        "Check on neighbors, friends, and those at risk"</span>
              <span>                    ],</span>
              <span>                    "immediate": [</span>
              <span>                        "Protect your home. Check on pipes and use heating sources safely",</span>
              <span>                        "Stay hydrated with warm fluids",</span>
              <span>                        "Stay inside.",</span>
              <span>                        "Be aware and cautious of icy conditions, especially while driving",</span>
              <span>                        "Know and watch for signs of hypothermia and frostbite",</span>
              <span>                        "Check on neighbors, friends, and those at risk"</span>
              <span>                    ],</span>
              <span>                    "recover": [</span>
              <span>                        "Be aware and cautious of icy conditions, especially while driving",</span>
              <span>                        "Be aware of power outages",</span>
              <span>                        "Be aware of flooding caused by snow melting"</span>
              <span>                    ]</span>
              <span>                }</span>
              <span>            }</span>
              <span>        }</span>
              <span>    }</span>
              <span>}</span>
            </pre>
          </div>
        </b-col>
      </b-row>
    </section>

    <section class="cta-row text-center bg-danger text-white container-fluid pt-5 pb-5">
      <h2 class="cta-row__heading mt-3 mb-4">{{ $t('landing.build.heading') }}</h2>

      <b-button size="lg" variant="light" :to="{ name: 'get_started' }" class="text-uppercase px-5">
        {{ $t('get_started') }}
      </b-button>
    </section>

    <section class="img-row text-center text-white container-fluid pt-5 pb-5">
      <h2 class="img-row__heading mt-5">{{ $t('landing.data_preview.heading') }}</h2>
      <p class="img-row__copy mb-4">
        {{ $t('landing.data_preview.sub_heading') }}
      </p>

      <img class="mx-auto d-block mb-3 img-fluid" :srcSet="srcSet('dataPreview')" :src="src('dataPreview')">

      <b-button size="lg" variant="light"
      :to="user ? { name: 'view-whatnow', params: { } } : { name: 'register', params: { } }"
      class="text-uppercase mb-4 px-5">
        {{ $t('landing.view_message_library')}}
      </b-button>
    </section>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Navbar from '~/components/Navbar'

export default {
  layout: 'default',

  metaInfo () {
    return { title: this.$t('home.home') }
  },

  computed: mapGetters({
    authenticated: 'auth/check',
    user: 'auth/user'
  }),

  data () {
    return {
      title: window.config.appName,
      activeTab: 0
    }
  },
  components: {
    Navbar
  }
}
</script>
