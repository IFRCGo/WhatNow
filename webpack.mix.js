const path = require('path')
const webpack = require('webpack')
const mix = require('laravel-mix')

if (mix.inProduction()) {
  mix.version()
  mix.extract([
    'vue',
    'vform',
    'axios',
    'vuex',
    'jquery',
    'popper.js',
    'vue-i18n',
    'vue-meta',
    'vuejs-noty',
    'vue-moment',
    'vue-google-auth',
    'vue-avatar',
    'vue-gravatar',
    'vue-router',
    'vue-select',
    'js-cookie',
    'bootstrap',
    'bootstrap-vue',
    'vuex-router-sync',
    'sweetalert2',
    'country-list',
    'countries-list',
    '@fortawesome/fontawesome',
    '@fortawesome/vue-fontawesome',
    'vue-simplemde'
  ])
  mix.disableNotifications()
}

mix.webpackConfig({
  plugins: [
    // new BundleAnalyzerPlugin(),
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery',
      'window.jQuery': 'jquery',
      Popper: ['popper.js', 'default']
    })
  ],
  resolve: {
    alias: {
      '~': path.join(__dirname, './resources/assets/js')
    }
  },
  module: {
    rules: [
      {
        test: /\.(pdf)$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: '[name]_[hash].[ext]',
              outputPath: 'pdf/'
            }
          }
        ]
      }
    ]
  }
})

mix
  .js('resources/assets/js/app.js', 'public/js').vue({ version: 2 })
  .sass('resources/assets/sass/app.scss', 'public/css')
  .sourceMaps()

