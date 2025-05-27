{{-- Global configuration object --}}
@php
$config = [
    'appName' => config('app.name'),
    'appEnv' => config('app.env'),
    'locale' => $locale = app()->getLocale(),
    'locales' => config('app.locales'),
    'translations' => json_decode(file_get_contents(resource_path("lang/{$locale}.json")), true),
    'terms' => [
        'version' => $terms_version
    ],
    'google' => [
        'client_id' => config('services.google.client_id'),
    ],
    'google_analytics' => [
        'tracker_id' => config('services.google_analytics.tracker_id'),
    ],
    'sentry_dsn' => config('services.sentry_dsn')
];
@endphp
<script>window.config = @json($config);</script>
<script>

    (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0]
        if (d.getElementById(id)) return
        js = d.createElement(s);
        js.id = id
        js.src = '//connect.facebook.net/en_US/sdk.js'
        fjs.parentNode.insertBefore(js, fjs)
    }(document, 'script', 'facebook-jssdk'))
</script>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{{ config('services.google_analytics.tracker_id') }}');</script>
<!-- End Google Tag Manager -->

{{-- Polyfill some features via polyfill.io --}}
@php
$polyfills = [
    'Promise',
    'Object.assign',
    'Object.values',
    'Array.prototype.find',
    'Array.prototype.findIndex',
    'Array.prototype.includes',
    'String.prototype.includes',
    'String.prototype.startsWith',
    'String.prototype.endsWith',
];
@endphp
<script src="https://cdn.polyfill.io/v2/polyfill.min.js?features={{ implode(',', $polyfills) }}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_web_api_key') }}"></script>

{{-- Load the application scripts --}}
@if (app()->isLocal())
  <script src="{{ mix('js/app.js') }}"></script>
@else
  <script src="{{ mix('js/manifest.js') }}"></script>
  <script src="{{ mix('js/vendor.js') }}"></script>
  <script src="{{ mix('js/app.js') }}"></script>
@endif