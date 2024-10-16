<template>
  <div class="container-fluid">
  <div class="row">

    <div class="doc-contents sidebar col-2 col-md-3 col-xl-2 pt-3">
      <b-nav vertical v-b-scrollspy>
        <b-nav-item href="#introduction">{{ $t('documentation.sidebar.introduction') }}</b-nav-item>
        <b-nav-item href="#base-url">{{ $t('documentation.sidebar.base_url') }}</b-nav-item>
        <b-nav-item href="#changelog">{{ $t('documentation.sidebar.changelog') }}</b-nav-item>
        <b-nav-item href="#authentication">{{ $t('documentation.sidebar.authentication') }}</b-nav-item>
        <b-nav-item href="#response-codes">{{ $t('documentation.sidebar.response_codes') }}</b-nav-item>
        <b-nav-item href="#emergency-alert-feed">{{ $t('documentation.sidebar.emergency_alert_feed') }}</b-nav-item>
        <b-nav-item href="#cap-alert-document">{{ $t('documentation.sidebar.cap_alert_document') }}</b-nav-item>
        <b-nav-item href="#what-now">{{ $t('documentation.sidebar.what_now') }}</b-nav-item>
      </b-nav>
    </div>

    <div class="doc-body bg-white col-12 col-md-9 col-xl-10 p-3 p-md-5">
      <h1>{{ $t('documentation.heading') }}</h1>
      <section>
        <h2 id="introduction">{{ $t('documentation.steps.one.heading') }}</h2>

        <p class="lead">{{ $t('documentation.steps.one.body') }}</p>

        <b-card :title="$t('documentation.steps.one.support_card.title')" bg-variant="light">
          <p class="card-text">{{ $t('documentation.steps.one.support_card.body') }}</p>
        </b-card>

        <h2 id="base-url">{{ $t('documentation.steps.two.heading') }}</h2>

        <p>{{ $t('documentation.steps.two.body') }}</p>

        <pre>https://api.preparecenter.org/v1</pre>
      </section>
      <section>
        <h2 id="changelog">{{ $t('documentation.steps.three.heading') }}</h2>
        <div class="table-responsive">
        <table class="table">
          <thead>
          <tr>
            <th scope="col">{{ $t('documentation.steps.three.table.headers.version') }}</th>
            <th scope="col">{{ $t('documentation.steps.three.table.headers.release') }}</th>
            <th scope="col">{{ $t('documentation.steps.three.table.headers.release_date') }}</th>
            <th scope="col">{{ $t('documentation.steps.three.table.headers.detail') }}</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td>1.0</td>
            <td>1.2.0</td>
            <td>17 Feb 2020</td>
            <td>The <code>forecast</code> property was renamed to <code>seasonalForecast</code> and the <code>midTerm</code> property was renamed to <code>mitigation</code></td>
          </tr>
          <tr>
            <td>1.0</td>
            <td>0.8.0</td>
            <td>14 Nov 2016</td>
            <td>Updated What Now attribution structure</td>
          </tr>
          <tr>
            <td>1.0</td>
            <td>0.6.0</td>
            <td>10 Oct 2016</td>
            <td>Public release</td>
          </tr>
          </tbody>
        </table>
        </div>
      </section>

      <section>
        <h2 id="authentication">{{ $t('documentation.steps.four.heading') }}</h2>

        <i18n path="documentation.steps.four.body" tag="p" class="u-text-wrap">
          <router-link :to="{ path: 'applications' }">{{ $t('documentation.steps.four.create_app_cta') }}</router-link>
        </i18n>

        <pre>x-api-key: your_application_api_key</pre>
      </section>

      <section>
        <h2 id="response-codes">{{ $t('documentation.steps.five.heading') }}</h2>
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

      <section>
        <h2 id="emergency-alert-feed">{{ $t('documentation.steps.six.heading') }}</h2>

        <b-card :title="$t('documentation.steps.six.card.title')" bg-variant="light">
          <i18n path="documentation.steps.six.card.body" tag="p" class="card-text">
            <a href="https://en.wikipedia.org/wiki/Common_Alerting_Protocol">{{ $t('documentation.steps.six.card.cta') }}</a>
          </i18n>
        </b-card>

        <p>{{ $t('documentation.steps.six.intro') }}</p>

        <p><strong>{{ $t('documentation.steps.six.endpoint') }}</strong></p>

        <pre>GET /alerts/rss</pre>

        <p>{{ $t('documentation.steps.six.feed_intro') }}
        <code>{country_code}</code>:<p>

        <p><strong>{{ $t('documentation.steps.six.endpoint') }}</strong></p>

        <pre>GET /org/{country_code}/alerts/rss</pre>

        <i18n path="documentation.steps.six.example" tag="p">
          <code>{country_code}</code>
        </i18n>

        <pre>GET /org/nzl/alerts/rss</pre>

      </section>

      <section>
        <h2 id="cap-alert-document">{{ $t('documentation.steps.seven.heading') }}</h2>

        <p>{{ $t('documentation.steps.seven.body') }}</p>

        <p><strong>{{ $t('documentation.steps.seven.endpoint') }}</strong></p>

        <pre>GET /alerts/cap12/{Y-m-d}/{organisation_urn}/{alert_uid}.xml</pre>

        <p><strong>{{ $t('documentation.steps.seven.example_response') }}</strong></p>

        <pre class="pre-scrollable"><code><span class="p">{</span><span
                class="w">
  </span><span class="nt">"data"</span><span class="p">:</span><span class="w"> </span><span class="p">[</span><span
                class="w">
    </span><span class="p">{</span><span class="w">
      </span><span class="nt">"id"</span><span class="p">:</span><span class="w"> </span><span
                class="s2">"1"</span><span class="p">,</span><span class="w">
      </span><span class="nt">"countryCode"</span><span class="p">:</span><span class="w"> </span><span
                class="s2">"CAN"</span><span class="p">,</span><span class="w">
      </span><span class="nt">"eventType"</span><span class="p">:</span><span class="w"> </span><span class="s2">"General"</span><span
                class="p">,</span><span class="w">
      </span><span class="nt">"webUrl"</span><span class="p">:</span><span class="w"> </span><span class="s2">"http://link.to/more/information"</span><span
                class="p">,</span><span class="w">
      </span><span class="nt">"attribution"</span><span class="p">:</span><span class="w"> </span><span
                class="p">{</span><span class="w">
        </span><span class="nt">"url"</span><span class="p">:</span><span class="w"> </span><span class="s2">"http://link.to/source/attribution"</span><span
                class="p">,</span><span class="w">
        </span><span class="nt">"imageUrl"</span><span class="p">:</span><span class="w"> </span><span class="s2">"http://link.to/source/attribution/image.jpg"</span><span
                class="p">,</span><span class="w">
        </span><span class="nt">"translations"</span><span class="p">:</span><span class="w"> </span><span
                class="p">{</span><span class="w">
          </span><span class="nt">"en"</span><span class="p">:</span><span class="w"> </span><span
                class="p">{</span><span class="w">
            </span><span class="nt">"name"</span><span class="p">:</span><span class="w"> </span><span class="s2">"The Canadian Red Cross Society"</span><span
                class="p">,</span><span class="w">
            </span><span class="nt">"attributionMessage"</span><span class="p">:</span><span class="w"> </span><span
                class="s2">"A message from the Canadian Red Cross Society"</span><span class="w">
          </span><span class="p">},</span><span class="w">
          </span><span class="nt">"fr"</span><span class="p">:</span><span class="w"> </span><span
                class="p">{</span><span class="w">
            </span><span class="nt">"name"</span><span class="p">:</span><span class="w"> </span><span class="s2">"La Société canadienne de la Croix-Rouge"</span><span
                class="p">,</span><span class="w">
            </span><span class="nt">"attributionMessage"</span><span class="p">:</span><span class="w"> </span><span
                class="s2">"Un message de la Société canadienne de la Croix-Rouge"</span><span class="w">
          </span><span class="p">}</span><span class="w">
        </span><span class="p">}</span><span class="w">
      </span><span class="p">},</span><span class="w">
      </span><span class="nt">"translations"</span><span class="p">:</span><span class="w"> </span><span
                class="p">[</span><span class="w">
        </span><span class="p">{</span><span class="w">
          </span><span class="nt">"lang"</span><span class="p">:</span><span class="w"> </span><span
                class="s2">"en"</span><span class="p">,</span><span class="w">
          </span><span class="nt">"title"</span><span class="p">:</span><span class="w"> </span><span class="s2">"Earthquake"</span><span
                class="p">,</span><span class="w">
          </span><span class="nt">"description"</span><span class="p">:</span><span class="w"> </span><span class="s2">"Example description of an Earthquake"</span><span
                class="p">,</span><span class="w">
          </span><span class="nt">"mitigation"</span><span class="p">:</span><span class="w"> </span><span
                class="p">[</span><span class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
          </span><span class="p">],</span><span class="w">
          </span><span class="nt">"seasonalForecast"</span><span class="p">:</span><span class="w"> </span><span
                class="p">[</span><span class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
		  </span><span class="p">],</span><span class="w">
          </span><span class="nt">"warning"</span><span class="p">:</span><span class="w"> </span><span
                class="p">[</span><span class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
	      </span><span class="p">],</span><span class="w">
          </span><span class="nt">"watch"</span><span class="p">:</span><span class="w"> </span><span class="p">[</span><span
                class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
          </span><span class="p">],</span><span class="w">
          </span><span class="nt">"immediate"</span><span class="p">:</span><span class="w"> </span><span
                class="p">[</span><span class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
          </span><span class="p">],</span><span class="w">
          </span><span class="nt">"recover"</span><span class="p">:</span><span class="w"> </span><span
                class="p">[</span><span class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
          </span><span class="p">]</span><span class="w">
        </span><span class="p">},</span><span class="w">
        </span><span class="p">{</span><span class="w">
          </span><span class="nt">"lang"</span><span class="p">:</span><span class="w"> </span><span
                class="s2">"fr"</span><span class="p">,</span><span class="w">
          </span><span class="nt">"title"</span><span class="p">:</span><span class="w"> </span><span class="s2">"Tremblement de terre"</span><span
                class="p">,</span><span class="w">
          </span><span class="nt">"description"</span><span class="p">:</span><span class="w"> </span><span class="s2">"Ceci est une description d'un tremblement de terre"</span><span
                class="p">,</span><span class="w">
          </span><span class="nt">"mitigation"</span><span class="p">:</span><span class="w"> </span><span
                class="p">[</span><span class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
          </span><span class="p">],</span><span class="w">
          </span><span class="nt">"seasonalForecast"</span><span class="p">:</span><span class="w"> </span><span
                class="p">[</span><span class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
          </span><span class="p">],</span><span class="w">
          </span><span class="nt">"warning"</span><span class="p">:</span><span class="w"> </span><span
                class="p">[</span><span class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
          </span><span class="p">],</span><span class="w">
          </span><span class="nt">"watch"</span><span class="p">:</span><span class="w"> </span><span class="p">[</span><span
                class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
          </span><span class="p">],</span><span class="w">
          </span><span class="nt">"immediate"</span><span class="p">:</span><span class="w"> </span><span
                class="p">[</span><span class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
          </span><span class="p">],</span><span class="w">
          </span><span class="nt">"recover"</span><span class="p">:</span><span class="w"> </span><span
                class="p">[</span><span class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
          </span><span class="p">]</span><span class="w">
        </span><span class="p">}</span><span class="w">
      </span><span class="p">],</span><span class="w">
      </span><span class="nt">"createdAt"</span><span class="p">:</span><span class="w"> </span><span class="s2">"2016-07-16T12:24:48+00:00"</span><span
                class="p">,</span><span class="w">
      </span><span class="nt">"updatedAt"</span><span class="p">:</span><span class="w"> </span><span class="s2">"2016-08-24T13:26:41+00:00"</span><span
                class="w">
    </span><span class="p">}</span><span class="w">
  </span><span class="p">]</span><span class="w">
</span><span class="p">}</span><span class="w">
</span></code></pre>

      </section>

      <section>
        <h2 id="what-now">{{ $t('documentation.steps.eight.heading') }}</h2>

        <p class="u-text-wrap">{{ $t('documentation.steps.eight.body') }}</p>

        <ul>
          <li>mitigation</li>
          <li>seasonalForecast</li>
          <li>warning</li>
          <li>watch</li>
          <li>immediate</li>
          <li>recover</li>
        </ul>

        <p><strong>{{ $t('documentation.steps.eight.endpoint') }}</strong></p>

        <pre>GET /org/{country_code}/whatnow?eventType={eventType}</pre>

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
            <td>Optional event type filter. Multiple event types may be separated using a comma e.g. <code
                    class="highlighter-rouge">?eventType=earthquake,hurricane</code></td>
          </tr>
          </tbody>
        </table>
        </div>

        <p><strong>{{ $t('documentation.steps.eight.example_response') }}</strong></p>

        <pre class="pre-scrollable"><code><span class="p">{</span><span class="w">
  </span><span class="nt">"data"</span><span class="p">:</span><span class="w"> </span><span class="p">[</span><span
                class="w">
    </span><span class="p">{</span><span class="w">
      </span><span class="nt">"id"</span><span class="p">:</span><span class="w"> </span><span
                class="s2">"1"</span><span class="p">,</span><span class="w">
      </span><span class="nt">"countryCode"</span><span class="p">:</span><span class="w"> </span><span
                class="s2">"CAN"</span><span class="p">,</span><span class="w">
      </span><span class="nt">"eventType"</span><span class="p">:</span><span class="w"> </span><span class="s2">"General"</span><span
                class="p">,</span><span class="w">
      </span><span class="nt">"webUrl"</span><span class="p">:</span><span class="w"> </span><span class="s2">"http://link.to/more/information"</span><span
                class="p">,</span><span class="w">
      </span><span class="nt">"regionName"</span><span class="p">:</span><span class="w"> </span><span class="s2">"North"</span><span
                class="p">,</span><span class="w">
      </span><span class="nt">"attribution"</span><span class="p">:</span><span class="w"> </span><span
                class="p">{</span><span class="w">
        </span><span class="nt">"url"</span><span class="p">:</span><span class="w"> </span><span class="s2">"http://link.to/source/attribution"</span><span
                class="p">,</span><span class="w">
        </span><span class="nt">"imageUrl"</span><span class="p">:</span><span class="w"> </span><span class="s2">"http://link.to/source/attribution/image.jpg"</span><span
                class="p">,</span><span class="w">
        </span><span class="nt">"translations"</span><span class="p">:</span><span class="w"> </span><span
                class="p">{</span><span class="w">
          </span><span class="nt">"en"</span><span class="p">:</span><span class="w"> </span><span
                class="p">{</span><span class="w">
            </span><span class="nt">"name"</span><span class="p">:</span><span class="w"> </span><span class="s2">"The Canadian Red Cross Society"</span><span
                class="p">,</span><span class="w">
            </span><span class="nt">"attributionMessage"</span><span class="p">:</span><span class="w"> </span><span
                class="s2">"A message from the Canadian Red Cross Society"</span><span class="w">
          </span><span class="p">},</span><span class="w">
          </span><span class="nt">"fr"</span><span class="p">:</span><span class="w"> </span><span
                class="p">{</span><span class="w">
            </span><span class="nt">"name"</span><span class="p">:</span><span class="w"> </span><span class="s2">"La Société canadienne de la Croix-Rouge"</span><span
                class="p">,</span><span class="w">
            </span><span class="nt">"attributionMessage"</span><span class="p">:</span><span class="w"> </span><span
                class="s2">"Un message de la Société canadienne de la Croix-Rouge"</span><span class="w">
          </span><span class="p">}</span><span class="w">
        </span><span class="p">}</span><span class="w">
      </span><span class="p">},</span><span class="w">
      </span><span class="nt">"translations"</span><span class="p">:</span><span class="w"> </span><span
                class="p">{</span><span class="w">
        </span><span class="nt">"en"</span><span class="p">:</span><span class="w"> </span><span class="p">{</span><span class="w">
          </span><span class="nt">"lang"</span><span class="p">:</span><span class="w"> </span><span
                class="s2">"en"</span><span class="p">,</span><span class="w">
          </span><span class="nt">"title"</span><span class="p">:</span><span class="w"> </span><span class="s2">"Earthquake"</span><span
                class="p">,</span><span class="w">
          </span><span class="nt">"description"</span><span class="p">:</span><span class="w"> </span><span class="s2">"Example description of an Earthquake"</span><span
                class="p">,</span><span class="w">
          </span><span class="nt">"mitigation"</span><span class="p">:</span><span class="w"> </span><span
                class="p">[</span><span class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
          </span><span class="p">],</span><span class="w">
          </span><span class="nt">"seasonalForecast"</span><span class="p">:</span><span class="w"> </span><span
                class="p">[</span><span class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
		  </span><span class="p">],</span><span class="w">
          </span><span class="nt">"warning"</span><span class="p">:</span><span class="w"> </span><span
                class="p">[</span><span class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
	      </span><span class="p">],</span><span class="w">
          </span><span class="nt">"watch"</span><span class="p">:</span><span class="w"> </span><span class="p">[</span><span
                class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
          </span><span class="p">],</span><span class="w">
          </span><span class="nt">"immediate"</span><span class="p">:</span><span class="w"> </span><span
                class="p">[</span><span class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
          </span><span class="p">],</span><span class="w">
          </span><span class="nt">"recover"</span><span class="p">:</span><span class="w"> </span><span
                class="p">[</span><span class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
          </span><span class="p">]</span><span class="w">
        </span><span class="p">},</span><span class="w">
        </span><span class="nt">"fr"</span><span class="p">:</span><span class="w"> </span><span class="p">{</span><span class="w">
          </span><span class="nt">"lang"</span><span class="p">:</span><span class="w"> </span><span
                class="s2">"fr"</span><span class="p">,</span><span class="w">
          </span><span class="nt">"title"</span><span class="p">:</span><span class="w"> </span><span class="s2">"Tremblement de terre"</span><span
                class="p">,</span><span class="w">
          </span><span class="nt">"description"</span><span class="p">:</span><span class="w"> </span><span class="s2">"Ceci est une description d'un tremblement de terre"</span><span
                class="p">,</span><span class="w">
          </span><span class="nt">"mitigation"</span><span class="p">:</span><span class="w"> </span><span
                class="p">[</span><span class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
          </span><span class="p">],</span><span class="w">
          </span><span class="nt">"seasonalForecast"</span><span class="p">:</span><span class="w"> </span><span
                class="p">[</span><span class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
          </span><span class="p">],</span><span class="w">
          </span><span class="nt">"warning"</span><span class="p">:</span><span class="w"> </span><span
                class="p">[</span><span class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
          </span><span class="p">],</span><span class="w">
          </span><span class="nt">"watch"</span><span class="p">:</span><span class="w"> </span><span class="p">[</span><span
                class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
          </span><span class="p">],</span><span class="w">
          </span><span class="nt">"immediate"</span><span class="p">:</span><span class="w"> </span><span
                class="p">[</span><span class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
          </span><span class="p">],</span><span class="w">
          </span><span class="nt">"recover"</span><span class="p">:</span><span class="w"> </span><span
                class="p">[</span><span class="w">
            </span><span class="s2">"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</span><span
                class="w">
          </span><span class="p">]</span><span class="w">
        </span><span class="p">}</span><span class="w">
      </span><span class="p">},</span><span class="w">
      </span><span class="nt">"createdAt"</span><span class="p">:</span><span class="w"> </span><span class="s2">"2016-07-16T12:24:48+00:00"</span><span
                class="p">,</span><span class="w">
      </span><span class="nt">"updatedAt"</span><span class="p">:</span><span class="w"> </span><span class="s2">"2016-08-24T13:26:41+00:00"</span><span
                class="w">
    </span><span class="p">}</span><span class="w">
  </span><span class="p">]</span><span class="w">
</span><span class="p">}</span><span class="w">
</span></code></pre>

        <p><strong>{{ $t('documentation.steps.eight.response_properties') }}</strong></p>

        <b-card bg-variant="light">
          <i18n path="documentation.steps.eight.noteDetails" tag="p" class="card-text">
            <strong>{{ $t('documentation.steps.eight.note') }}</strong>
            <code>data</code>
          </i18n>
        </b-card>

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
            <td>Name of the regional area the event type applies to, default is "National"</td>
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

          <i18n path="documentation.steps.eight.stucture_attribute_translation" tag="p">
            <code>translation</code>
          </i18n>

        <div class="table-responsive">
        <table class="table">
          <thead>
          <tr>
            <th>Property</th>
            <th>Data type</th>
            <th>Description</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td>name</td>
            <td>string</td>
            <td>Name of issuing organisation</td>
          </tr>
          <tr>
            <td>attributionMessage</td>
            <td>string</td>
            <td>Attribution message for this information</td>
          </tr>
          </tbody>
        </table>
        </div>

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
            <td>id</td>
            <td>string</td>
            <td>id of the translation</td>
          </tr>
          <tr>
            <td>lang</td>
            <td>string</td>
            <td>Language code of the translation (ISO 639-1)</td>
          </tr>
          <tr>
            <td>title</td>
            <td>string</td>
            <td>Title of the information</td>
          </tr>
          <tr>
            <td>description</td>
            <td>string</td>
            <td>Brief description of the information</td>
          </tr>
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
          </tbody>
        </table>
        </div>

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
  loading: false
}
</script>
