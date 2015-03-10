$( function() {
	$( 'select' ).select2();

	$( 'form' ).submit( function( event ) {
		event.preventDefault();
		var $form = $( this );
		var $btn = $( 'input:submit, button.submit', $form ).button( 'loading' );
		$( '.form-group.has-error', $form ).removeClass( 'has-error' );
		$( 'label.error', $form ).remove();
		$( 'input#password_confirmation:hidden' ).val( $( 'input#password' ).val() );
		$.ajax( {
			type: $form.attr( 'method' ),
			url: $form.attr( 'action' ),
			data: $form.serialize(),
			dataType: 'json'
		} ).always( function( data, status ) {
			if( status == 'success' || status == 'nocontent' ) {
				parent.oTable.api().ajax.reload();
				parent.$.colorbox.close();
			} else {
				var resp = JSON.parse( data.responseText );
				$.each( resp, function( id, msgs ) {
					var $tgt = $( '#' + id, $form ),
						msg = '<label class="error" for="' + id + '">' + msgs[ 0 ] + '</label>';

					if( $tgt.is( '.form-control' ) )
						$( msg ).insertAfter( $tgt );
					else if( $tgt.parents( 'label' ).is( '[class*=inline]' ) )
						$( msg ).appendTo( $tgt.parents( 'label' ).parent() );
					else
						$( msg ).insertAfter( $tgt.parents( '.' + $tgt.attr( 'type' ) ) );

					$( '#' + id ).parents( '.form-group' ).addClass( 'has-error' );
				} );
				$btn.button( 'reset' );
			}
		} )
	} );

	$( '.close_popup' ).click( function( event ) {
		event.preventDefault();
		parent.$.colorbox.close();
	} );
} );
//# sourceMappingURL=modal.js.map