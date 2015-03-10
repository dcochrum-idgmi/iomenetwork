$( function() {
	$( 'select' ).select2();
	$( '#flash-overlay-modal' ).modal();
	IOMEsetModals();
} );

function IOMEsetModals() {
	$( 'a.iframe' ).each( function() {
		var $this = $( this ),
			href = $this.attr( 'href' );
		href = href + ((href.indexOf( '?' ) < 0) ? '?' : '&') + 'iframe';

		$this.colorbox(
			{
				href: href,
				iframe: true,
				width: '80%',
				height: '80%'
			} );
	} );
}