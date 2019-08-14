// Require path.
const path = require( 'path' );

const MiniCssExtractPlugin = require("mini-css-extract-plugin");
 
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');
const UglifyJsPlugin = require("uglifyjs-webpack-plugin");

const glob = require("glob");
var SuppressEntryChunksPlugin = require('./SuppressEntryChunksPlugin');

const publicPath = '/';
const uglifyJS = require("uglify-js");
const CleanCSS = require('clean-css');

var debug =  process.env.NODE_ENV !== 'production';

//merge js files into one
const MergeIntoSingleFilePlugin = require('webpack-merge-and-include-globally');

//watch files
const WatchExternalFilesPlugin = require('webpack-watch-files-plugin').default;

// Configuration object.
const config = {
	// Create the entry points.
	// One for frontend and one for the admin area.
	entry: {
		// frontend and admin will replace the [name] portion of the output config below.
		//admin: ['./assets_src/backend/js/admin-index.js', './assets/backend/css/extra.css'],
                admin: ['./assets_src/backend/js/admin-index.js'
                    ,'./assets_src/backend/css/style2.scss'
                    ,'./assets_src/backend/css/style.css'
                ],
                
                //fm_init:['./assets_src/backend/js/fm_init.js']
                 
	},

	// Create the output files.
	// One for each of our entry points.
	output: {
		// [name] allows for the entry object keys to be used as file names.
		filename: 'backend/js/[name].js',
		// Specify the path to the JS files.
		path: path.resolve( __dirname, 'assets' ),
                
                publicPath: publicPath,
	},

	// Setup a loader to transpile down the latest and great JavaScript so older browsers
	// can understand it.
	module: {
		rules: [
			{
				// Look for any .js files.
				test: /\.js$/,
				// Exclude the node_modules folder.
				exclude: /node_modules/,
				// Use babel loader to transpile the JS files.
				loader: 'babel-loader'
			},
                         {
                            test:/\.(s*)css$/,
                            use: [
                              {
                                loader: MiniCssExtractPlugin.loader,
                                
                              },
                              'css-loader',
                              'sass-loader'
                            ],
                          },
		]
	},
     stats: {
            colors: true
        },
        devtool: debug ? 'inline-source-map' : false,
     
      
           optimization: {
           	minimize: false,
            minimizer: [
              new UglifyJsPlugin({
                /* Enable file caching. Default path to cache directory: node_modules/.cache/uglifyjs-webpack-plugin. */
                cache: true,
                /* Use multi-process parallel running to improve the build speed. */
                parallel: true, 
                /* Use source maps to map error message locations to modules (this slows down the compilation). If you use your own minify function please read the minify section for handling source maps correctly. */
                sourceMap: true
              }),
              new OptimizeCSSAssetsPlugin({})
            ]
          },   
         plugins: [
         	new FixStyleOnlyEntriesPlugin(),
            new MiniCssExtractPlugin({
              // Options similar to the same options in webpackOptions.output
              // both options are optional
               filename: "backend/css/[name].css",
            }),
            // don't output the css.js and index.js bundles
    		new SuppressEntryChunksPlugin(['admin', 'fm_init']),
    		new MergeIntoSingleFilePlugin({
			 
			      //also possible:

			       files:{
			         'backend/js/admin.js':[
			                                'assets_src/backend/js/flmkbp-back-fm.js',
                                                        'assets_src/backend/js/flmkbp-back-backup.js',
                                                        'assets_src/backend/js/flmkbp-back-settings.js',
			                                'assets_src/backend/js/zgfm-back-helper.js'
			                            ],
                                 'backend/js/fm_init.js':[
			                                'assets_src/backend/js/fm_init.js'
			                            ],
                                 'backend/js/global-mod-backup.js':[
			                                'assets_src/backend/js/global-mod-backup.js'
			                            ],                   
			         /*  'backend/css/admin.css':[
			                    'assets_src/backend/css/style2.scss',
			                    'assets_src/backend/css/style.css'
			         ]*/
			       },
			       transform:{
			         'backend/js/admin.js': code =>  (process.env.NODE_ENV === 'production') ? uglifyJS.minify(code).code :code,
                                 'backend/js/fm_init.js': code =>  (process.env.NODE_ENV === 'production') ? uglifyJS.minify(code).code :code,
                                 'backend/js/global-mod-backup.js': code =>  (process.env.NODE_ENV === 'production') ? uglifyJS.minify(code).code :code,
			         //'style.css': code => (process.env.NODE_ENV === 'production')  ?new CleanCSS({}).minify(code).styles:code,
			       },

			      //hash:true,
			    }, filesMap =>{
			      console.log("generated files: ",filesMap)
			    }),
    		new WatchExternalFilesPlugin({
			      files: [
			        //'./build/app/js/**/*.js',
			        //'./example/*.js',
			        './assets_src/**/**/*.js',
			      ],
			    
			    })
          ]
}

// Export the config object.
module.exports = config;