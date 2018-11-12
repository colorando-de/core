var webpack = require('webpack');
var WebpackBar = require('webpackbar');
var Encore = require('@symfony/webpack-encore');
var path = require('path');

Encore

  .addPlugin(new WebpackBar({
    profile: Encore.isProduction() ? true:false,
    minimal: false
  }))

  .addPlugin(new webpack.ProvidePlugin({
    $bus: [path.resolve(__dirname, './assets/js/bus/'), 'default']
  }))

  .addLoader({
    test: /\.svg$/,
    loader: 'svgo-loader',
    options: {
      plugins: [
        {removeTitle: true},
        {removeDesc: true},
        {cleanupAttrs: true},
        {convertColors: {shorthex: true}},
        {convertPathData: false},
        {removeEmptyAttrs: true},
        {minifyStyles: true},
      ]
    }
  })

  .setOutputPath('public/assets/')
  .setPublicPath('/assets')
  .setManifestKeyPrefix('assets')
  .copyFiles({ from: './assets/static', to: '[path][name].[ext]' })

  
  .cleanupOutputBeforeBuild()
  .enableSingleRuntimeChunk()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())

  .addEntry('bolt', './assets/js/bolt.js')
  .addStyleEntry('theme-default', './assets/scss/themes/default.scss')
  .addStyleEntry('theme-light', './assets/scss/themes/light.scss')

  .splitEntryChunks()
  .autoProvidejQuery()
  .enableVueLoader()
  .enableSassLoader()
  .enablePostCssLoader()

;

module.exports =  Encore.getWebpackConfig();


