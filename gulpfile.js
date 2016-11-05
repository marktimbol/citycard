const elixir = require('laravel-elixir');
const bowersPath = '../../../bower_components/';
const modulesPath = '../../../node_modules/';

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(mix => {
    mix.sass('app.scss', 'resources/assets/css/app.css')
   		.styles([
        modulesPath + 'font-awesome/css/font-awesome.css',
   			modulesPath + 'sweetalert/dist/sweetalert.css',
   			'app.css'
   		], 'public/css/app.css')

    	.webpack('app.js')

      .copy('node_modules/font-awesome/fonts/', 'public/build/fonts')

    	.version([
    		'public/css/app.css',
    		'public/js/app.js'
    	]);
});
