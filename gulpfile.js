DISABLE_NOTIFIER = true;
var elixir = require( 'laravel-elixir' );

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir( function( mix ) {
	//	Set base source and public folders
	var src = 'resources/assets/',
		pub = 'public/assets/';
	//	Compile custom Bootstrap and other base styles
	mix.less( 'base.less', src + 'css' )
		//	Combine all layout styles into layout.css
		.styles( '*.css', pub + 'css/layout.css', src + 'css/layout' )
		//	Combine all modal styles into modal.css
		//	.styles('*.css', pub + 'css/modal.css', src + 'css/modal');
		//	Combine all base styles into base.css (including custom Bootstrap and base LESS)
		.styles( [ 'before_base/*.css', 'base.css', 'after_base/*.css' ], pub + 'css/base.css', src + 'css' )
		//	Copy minified Bootstrap JS
		.copy( src + 'bootstrap/dist/js/bootstrap.min.js', src + 'js/bootstrap.min.js' )
		//	Combine dataTables JS into dataTables.js to app JS folder
		//.scripts( [ 'jquery.dataTables.min.js', 'jquery.dataTables-bootstrap.js', 'jquery.dataTables-pipeline.js' ], src + 'js/common/dataTables.js', src + 'js/common/dataTables' );
		//	Combine all JS in common dir to common.js
	//mix.scripts( '*.js', pub + 'js/common.js', src + 'js/common' )
		.scripts( [ 'dataTables/jquery.dataTables.min.js', 'dataTables/jquery.dataTables-bootstrap.js', 'dataTables/jquery.dataTables-pipeline.js', '*' ], pub + 'js/common.js', src + 'js/common' )
		//	Combine all JS in app dir to app.js
		.scripts( '*.js', pub + 'js/app.js', src + 'js/app' )
		//	Combine all JS in modal dir to modal.js
		.scripts( '*.js', pub + 'js/modal.js', src + 'js/modal' )
		//	Copy css images folder, fonts, images, and base js folder
		.copy( src + 'css/images', pub + 'css' )// CSS images might need to be in the base css folder, so we also have a nested images folder for css/images
		.copy( src + 'favicon', 'public' )
		.copy( src + 'fonts', pub + 'fonts' )
		.copy( src + 'images', pub + 'images' )
		.copy( src + 'js/base', pub + 'js' );
} );
