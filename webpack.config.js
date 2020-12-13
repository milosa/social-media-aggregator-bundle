var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath(Encore.isProduction() ? 'Resources/public/build/' : 'Resources/public/build/dev/')
    .setPublicPath(Encore.isProduction() ? '/bundles/milosasocialmediaaggregator/build/' : '/bundles/milosasocialmediaaggregator/build/dev')
    .setManifestKeyPrefix('/bundles/milosasocialmediaaggregator/build/')
    .addEntry('app', './Resources/assets/js/app.js')
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableSassLoader()
    .enableReactPreset()
    .enableSourceMaps(!Encore.isProduction())
    .cleanupOutputBeforeBuild()
    .configureBabel(function(babelConfig) {
    })
    .disableSingleRuntimeChunk()
;

module.exports = Encore.getWebpackConfig();
