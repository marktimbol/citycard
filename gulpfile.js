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

      .styles([
        modulesPath + 'froala-editor/css/froala_editor.min.css',
        modulesPath + 'froala-editor/css/froala_style.min.css',
        modulesPath + 'froala-editor/css/plugins/table.min.css',
      ], 'public/css/editor.css')

      .scripts([
        modulesPath + 'froala-editor/js/froala_editor.min.js',
        modulesPath + 'froala-editor/js/plugins/table.min.js',
        modulesPath + 'froala-editor/js/plugins/lists.min.js',
        modulesPath + 'froala-editor/js/plugins/paragraph_format.min.js',
        'editor.js'
      ], 'public/js/editor.js')

      .copy('node_modules/font-awesome/fonts/', 'public/build/fonts')

    	.version([
        'public/css/app.css',
        'public/js/app.js',
    		'public/css/editor.css',
    		'public/js/editor.js'
    	]);
});
