const Dotenv = require('dotenv-webpack');
const PurgecssPlugin = require('purgecss-webpack-plugin');
const ImageminPlugin = require('imagemin-webpack-plugin').default;
const CopyWebpackPlugin = require('copy-webpack-plugin');
const WorkboxPlugin = require('workbox-webpack-plugin');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer')
    .BundleAnalyzerPlugin;
const glob = require('glob-all');
let path = require('path');
const Assets = require('webpack-assets-manifest');
const plugins = [
    new Dotenv({
        path: '../.env'
    }),
];


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

if(process.env.NODE_ENV==='production'){


    plugins.push(

        new WorkboxPlugin.GenerateSW({
            manifestTransforms: [cdnTransform]
        })

        /*new WorkboxPlugin.InjectManifest({
            swSrc:'./src/js/sw.js',
            // these options encourage the ServiceWorkers to get in there fast
            // and not allow any straggling "old" SWs to hang around
           // ,


        })*/
    );


    plugins.push(new CopyWebpackPlugin({patterns:[
        {
            from: './src/js/main/images/',to:'images'
        },
    ]}));
    plugins.push(new ImageminPlugin({ test: /\.(jpe?g|png|gif|svg)$/i }));

    plugins.push(
        new PurgecssPlugin({
            whitelistPatterns:[/switch/],
            only:['bundle','vendor','index'],
            paths: glob.sync([
                path.join(__dirname, './public/index.html'),
                path.join(__dirname, './src/js/**/*.vue'),
                path.join(__dirname, './src/js/**/*.js'),
                path.join(__dirname, '../app/views/*.php'),
                path.join(__dirname, '../app/views/mobile/**/*.php'),
                path.join(__dirname, '../app/views/layouts/*.php'),
                path.join(__dirname, '../app/views/partials/footerMobile.php'),
                path.join(__dirname, '../app/views/partials/stats/*.php'),
                path.join(__dirname, '../app/widgets/**/*.php'),
            ])
        })
    );

    plugins.push(
        new PurgecssPlugin({
            only:['city'],
            paths: glob.sync([
                path.join(__dirname, './public/city.html'),
            ])
        })
    );

    plugins.push(
        new Assets({
            entrypoints:true,
            output:"assets.json",
            customize(entry) {



                if ( !(entry.key.toLowerCase().endsWith('.js') ||
                    entry.key.toLowerCase().endsWith('.css'))
                ) {
                    return false;
                }


                return {
                    key: `${entry.key}`,
                    value: `/mobile/${entry.value}`,
                };
            },
        })
    );
   //plugins.push(new BundleAnalyzerPlugin());

}




const config = {

    pages: {
        index: {
            // entry for the page
            entry: 'src/js/main/app.js',
            // the source template
            //template: 'public/index.html',
            // output as dist/index.html
            //filename: 'index.html',
            // when using title option,
            // template title tag needs to be <title><%= htmlWebpackPlugin.options.title %></title>
            title: 'Index Page',
            // chunks to include on this page, by default includes
            // extracted common chunks and vendor chunks.
            chunks: ['chunk-vendors', 'chunk-common', 'index']
        },
        city: {
            // entry for the page
            entry:'src/js/common/city.js',
            //template: 'public/city.html',
            // output as dist/index.html
            //filename: 'city.html',
            chunks: ['chunk-vendors', 'chunk-common', 'index']
        },
        test: {
            // entry for the page
            entry:'src/js/common/test.js',
        },

    },

/*    chainWebpack: config => {

    },
 */
    configureWebpack:{
        plugins:plugins,
        optimization:{
            splitChunks:{
                chunks: 'all'
            }
        }
    },
    devServer:{
        writeToDisk:true,
        compress: false,
        liveReload: false,
        hot:false,

       // host: "test.fahrplan-bus-bahn.de",
        //https: !(process.env.NODE_ENV==='production'),
        //key: (process.env.NODE_ENV==='production')?null:fs.readFileSync('/home/kai/ssl/test.key'),
        //cert: (process.env.NODE_ENV==='production')?null:fs.readFileSync('/home/kai/ssl/test.crt'),
        //key: fs.readFileSync('D:\\projects\\ssl\\test.fahrplan-bus-bahn.de.key'),
        //cert: fs.readFileSync('D:\\projects\\ssl\\test.fahrplan-bus-bahn.de.crt'),
    },
}


    config.outputDir = '../web/mobile';
    config.publicPath = '/mobile';


module.exports = config;
