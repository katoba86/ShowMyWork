var path = require('path');
const PurgecssPlugin = require('purgecss-webpack-plugin');
const glob = require('glob-all');
const ImageminPlugin = require('imagemin-webpack-plugin').default;
const CopyWebpackPlugin = require('copy-webpack-plugin');
const Dotenv = require('dotenv-webpack');
const TerserPlugin = require('terser-webpack-plugin');
const WorkboxPlugin = require('workbox-webpack-plugin');
const Assets = require('webpack-assets-manifest');
const dot = require('dotenv').config({
    path:'../.env'
});
const fs = require('fs');

const cdnTransform = async (manifestEntries) => {
    const manifest = manifestEntries.map(entry => {
        // const cdnOrigin = '/mobile/';
        //if (entry.url.startsWith('/assets/')) {
        //   entry.url = cdnOrigin + entry.url;
        //}
        console.log(entry);
        return entry;
    });
    return {manifest, warnings: []};
};
var publicPath = "web";
var productionPlugins = [
    new Dotenv({
        path: '../.env'
    }),

    new CopyWebpackPlugin([
        {
            from: './src/images/',to:'images'
        },
    ]),

    new ImageminPlugin({ test: /\.(jpe?g|png|gif|svg)$/i }),
    new PurgecssPlugin({
        whitelistPatterns:[/switch/,/router-link/,/link-block/,/vdatetime/,/link-block-3/,/link-block-2/,/link-block-4/],
        paths: glob.sync([

            path.join(__dirname, './src/js/**/*.vue'),
            path.join(__dirname, './src/js/**/*.js'),
            path.join(__dirname, '../app/views/desktop/**/*.php'),
            path.join(__dirname, '../app/views/layouts/*.php'),
            path.join(__dirname, '../app/views/partials/**/*.php'),
            path.join(__dirname, '../app/widgets/**/*.php'),

        ])
    }),

    new WorkboxPlugin.GenerateSW({
        manifestTransforms: [cdnTransform]
    }),
    new Assets({
        entrypoints:true,
        output:"desktop_assets.json",
        customize(entry) {



            if ( !(entry.key.toLowerCase().endsWith('.js') ||
                entry.key.toLowerCase().endsWith('.css'))
            ) {
                return false;
            }


            return {
                key: `${entry.key}`,
                value: `/desktop/${entry.value}`,
            };
        },
    })

    /*new WorkboxPlugin.InjectManifest({
        swSrc:'./src/js/sw.js',
        // these options encourage the ServiceWorkers to get in there fast
        // and not allow any straggling "old" SWs to hang around
       // ,


    })*/


];
var developmentPlugins = [
    new Dotenv({
        path: '../.env'
    }),

    new CopyWebpackPlugin([
        {
            from: './src/images/',to:'images'
        },
        {
            from: './src/fonts/',to:'fonts'
        },
    ])



];



let config = {


    css: {
        extract: true,
        loaderOptions: {
        }
    },



    outputDir: '../'+publicPath,
    //  assetsDir: './images',
    filenameHashing:false,

    devServer:{
        //public:'172.16.238.10:2040',
        hot:true,
        //host:'localhost',
        port: 8080,
        disableHostCheck: true,
        writeToDisk: true,
        https: false,
    },
    pages: {
        desktop: {
            template: 'src/index.html',
            entry: 'src/js/app.js',
        },
        desktopresult: {
            entry: 'src/js/result.js'
        }
    },
    configureWebpack:{
        output: {
            libraryExport: 'default'
        },
        optimization:{

            minimizer: [new TerserPlugin({ sourceMap: false, terserOptions: { compress: { drop_console: true } } })],


            splitChunks: {

                minSize: 10000,
                maxSize: 250000,

                cacheGroups: {

                    dates: {
                        test: /[\\/]node_modules[\\/](luxon)[\\/]/,
                        name: "luxon"
                    },

                    /*commons: {
                        chunks: "initial",
                        minChunks: 2,
                        //name:'chunk'+process.env.VERSION,
                        maxInitialRequests: 5, // The default limit is too small to showcase the effect
                        minSize: 500, // This is example is too small to create commons chunks,
                    }, */
                    test: {
                        //minChunks: 2,
                        //test: /node_modules/,
                        test: /[\\/]node_modules[\\/](!luxon)(!moment)(!moment-timezone)[\\/]/,
                        //chunks: "initial",
                        name: "dstvendor",
                    }
                }
            },

            runtimeChunk: {
                name: "dst_manifest",
            },
        },
        // Merged into the final Webpack config
        plugins: productionPlugins,
    },
    productionSourceMap: false
};




let configDevelopment = {
    publicPath :'desktop',
    outputDir : '../'+publicPath+'/desktop/',

    css: {
        extract: true,
        loaderOptions: {
        }
    },


    //  assetsDir: './images',
    filenameHashing:false,

    devServer:{
        //public:'172.16.238.10:2040',
        hot:false,
        host:'test.fahrplan-bus-bahn.de',
        port: 8080,
        disableHostCheck: true,
        writeToDisk: true,
        https: false
       /* https: {
            cert: fs.readFileSync('../caddy/sslmac/_wildcard.fahrplan-bus-bahn.de.pem'),
            key: fs.readFileSync('../caddy/sslmac/_wildcard.fahrplan-bus-bahn.de-key.pem'),
        }, */
    },
    pages: {
        desktop: {
            template: 'src/index.html',
            entry: 'src/js/app.js',
        },
        desktopresult: {
            entry: 'src/js/result.js'
        }
    },
    configureWebpack:{
        output: {
            libraryExport: 'default'
        },
        optimization:{



        },
        // Merged into the final Webpack config
        plugins: developmentPlugins,
    },
    productionSourceMap: true
};

if(process.env.NODE_ENV !== 'production'){
    config.publicPath = 'desktop';
    config.outputDir = '../'+publicPath+'/desktop/'
}else{
    config.publicPath = 'desktop';
    config.outputDir = '../'+publicPath+'/desktop/'
}

console.log(process.env.NODE_ENV);

module.exports = (process.env.NODE_ENV === 'production')?config:configDevelopment;

