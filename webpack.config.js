var Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    //.addEntry('app', './assets/js/app.js')
    //.addEntry('page1', './assets/js/page1.js')
    //.addEntry('page2', './assets/js/page2.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: 3
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()

    // uncomment if you use API Platform Admin (composer req api-admin)
    .enableReactPreset()
    //.addEntry('admin', './assets/js/admin.js')
    .addEntry('payment', './assets/js/modules/payment/Payment.js')
    .addEntry('global', './assets/sass/global.scss')
    .addEntry('register', './assets/js/modules/registration/Registration.js')
    .addEntry('reset', './assets/js/modules/registration/Reset.js')
    .addEntry('resetemail', './assets/js/modules/registration/ResetEmail.js')
    .addEntry('profil', './assets/js/modules/profil/Profil.js')
    .addEntry('chat', './assets/js/modules/messaging/Chat.js')
    .addEntry('nav', './assets/js/common/Nav.js')
    .addEntry('footer', './assets/js/common/Footer.js')
    .addEntry('viewer', './assets/js/common/ImageViewer.js')
    .addEntry('dashboard', './assets/js/modules/dashboard/Dashboard.js')
    .addEntry('shop', './assets/js/modules/shop/Shop.js')
    .addEntry('toasts', './assets/js/common/Toasts.js')
    .addEntry('editProfile', './assets/js/modules/profil/EditProfil.js')
    .addEntry('contact', './assets/js/modules/contact/Contact.js')
    .addEntry('favorite', './assets/js/modules/favorite/Favorite.js')
    .addEntry('group', './assets/js/modules/group/Group.js')
    .addEntry('diary', './assets/js/modules/diary/Diary.js')
    .addEntry('testimony', './assets/js/modules/testimony/Testimony.js')
    .addEntry('visit', './assets/js/modules/visit/Visit.js')
    .addEntry('conversation', './assets/js/modules/conversation/Conversation.js')
    .addEntry('flower', './assets/js/modules/flower/Flower.js')
    .addEntry('cookies', './assets/js/common/Cookies.js')
;

module.exports = Encore.getWebpackConfig();
