const elixir = require('laravel-elixir');
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
    mix.sass('public.scss', 'resources/assets/css/public.css')

      .styles([
        modulesPath + 'font-awesome/css/font-awesome.css',
        modulesPath + 'sweetalert/dist/sweetalert.css',
        'public.css'
      ], 'public/css/public.css')

      .webpack('public.js')
      .webpack('components/PostsInfinite.js');

    mix.sass('app.scss', 'resources/assets/css/app.css')
   	    .styles([
          modulesPath + 'font-awesome/css/font-awesome.css',
   		    modulesPath + 'sweetalert/dist/sweetalert.css',
   			'app.css'
   		], 'public/css/app.css')

      .webpack('app.js')
      .webpack('components/Home.js')
      .webpack('components/CreateOutlet.js')
      .webpack('components/EditOutlet.js')
      .webpack('components/CreatePost.js')
      .webpack('components/CreateMerchant.js')
      .webpack('components/MerchantPosts.js')
      .webpack('components/OutletSettings.js')
      .webpack('components/CreateItemForReservation.js')
      .webpack('components/ItemsForReservation.js')
      .webpack('components/DashboardPosts.js', 'public/js/DashboardPosts.js')

      .styles([
          modulesPath + 'froala-editor/css/froala_editor.min.css',
          modulesPath + 'froala-editor/css/froala_style.min.css',
          modulesPath + 'froala-editor/css/plugins/table.min.css',
      ], 'public/css/editor.css')

      .scripts([
          modulesPath + 'froala-editor/js/froala_editor.min.js',
          modulesPath + 'froala-editor/js/plugins/table.min.js',
          modulesPath + 'froala-editor/js/plugins/lists.min.js',
          modulesPath + 'froala-editor/js/plugins/font_family.min.js',
          modulesPath + 'froala-editor/js/plugins/paragraph_format.min.js',
          'editor.js'
      ], 'public/js/editor.js')

      .styles([
          modulesPath + 'react-select/dist/react-select.css'
      ], 'public/css/select.css')

      .styles([
          '../js/intl-tel-input/build/css/intlTelInput.css'
      ], 'public/css/telephone.css')

      .scripts([
          'intl-tel-input/build/js/intlTelInput.js',
          'intl-tel-input/build/js/utils.js',
          'intlTelInput.js'
      ], 'public/js/telephone.js')

      .styles([
          'mobile.css'
      ], 'public/css/mobile.css')

      .styles([
        modulesPath  + 'react-datepicker/dist/react-datepicker.css',
      ], 'public/css/datepicker.css')

      .copy('node_modules/font-awesome/fonts/', 'public/build/fonts')
      .copy('node_modules/intl-tel-input/build/img', 'public/build/img')

      .version([
          // Front end
          'public/css/public.css',
          'public/js/public.js',
          'public/js/Home.js',

          // Back end
          'public/css/app.css',
          'public/js/app.js',
          'public/css/editor.css',
          'public/js/editor.js',
          'public/css/select.css',
          'public/css/telephone.css',
          'public/js/telephone.js',
          'public/js/CreateMerchant.js',
          'public/js/MerchantPosts.js',
          'public/js/CreateOutlet.js',
          'public/js/CreatePost.js',
          'public/js/DashboardPosts.js',
          'public/js/OutletSettings.js',
          'public/css/mobile.css',
          'public/css/datepicker.css',
          'public/js/CreateItemForReservation.js',
          'public/js/ItemsForReservation.js',
      ]);
});
