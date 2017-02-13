const elixir = require('laravel-elixir');
const modulesPath = '../../../node_modules/';

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
			'app.css',
		], 'public/css/app.css')

		.webpack('app.js')
		.webpack('components/Authentication.js')
		// Outlet
		.webpack('components/OutletSettings.js')
		.webpack('components/Outlet/EditOutlet.js')
		.webpack('components/Outlet/CreateOutlet.js')
		.webpack('components/Outlet/UpdateOutletAddress.js')
		// Post
		.webpack('components/CreatePost.js')
		.webpack('components/Post/EditPost.js')
		.webpack('components/DashboardPosts.js')
		// Merchant
		.webpack('components/MerchantPosts.js')
		.webpack('components/ExploreMerchants.js')
		.webpack('components/Merchant/CreateMerchant.js')
		// Items for reservations
		.webpack('components/ItemsForReservation.js')
		.webpack('components/CreateItemForReservation.js')
		// Search Results
		.webpack('components/Search/SearchResults.js')

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

		.scripts([
			'/lib/BlurredImageEffect.js'
		], 'public/js/BlurredImageEffect.js')

		.copy('node_modules/font-awesome/fonts/', 'public/build/fonts')
		.copy('node_modules/intl-tel-input/build/img', 'public/build/img')

		.version([
			// Front end
			'public/css/public.css',
			'public/js/public.js',
			'public/js/Authentication.js',
			// Back end
			'public/css/app.css',
			'public/js/app.js',
			// Merchant
			'public/js/CreateMerchant.js',
			'public/js/MerchantPosts.js',
			// Outlet
			'public/js/CreateOutlet.js',
			'public/js/EditOutlet.js',
			'public/js/OutletSettings.js',
			'public/js/UpdateOutletAddress.js',
			// Search Results
			'public/js/SearchResults.js',
			// Post
			'public/js/CreatePost.js',
			'public/js/EditPost.js',
			'public/js/DashboardPosts.js',
			'public/js/PostsInfinite.js',
			// Item for reservations
			'public/js/CreateItemForReservation.js',
			'public/js/ItemsForReservation.js',
			// Explore Merchants
			'public/js/ExploreMerchants.js',
			// Blurred image
			'public/js/BlurredImageEffect.js',
			// Res
			'public/css/mobile.css',
			'public/css/datepicker.css',		
			'public/css/editor.css',
			'public/js/editor.js',
			'public/css/select.css',
			'public/css/telephone.css',
			'public/js/telephone.js',
		]);
});