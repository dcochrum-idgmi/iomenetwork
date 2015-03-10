$( function() {
	var $conf = $( 'input#password_confirmation' );
	if( $conf.length ) {
		$( 'input#password' ).password();
		var $hid_conf = $conf.clone();
		$hid_conf.attr( 'type', 'hidden' );
		$conf.parents( '.form-group' ).replaceWith( $hid_conf );
	}
} );