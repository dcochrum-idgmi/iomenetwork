$( function() {
	$( 'select' ).select2();
	$( '#flash-overlay-modal' ).modal();
	drewDataTable();
} );

function drewDataTable() {
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

    $( '#table abbr.time-diff' ).each( function( i, e ) {
        var $this = $(this),
            d = new Date($this.attr('title'));
        $this.attr('title', d.format('M j, Y g:i:sa ') + d.toTimeString().match(/\(.*\)/).join('').match(/\b([A-Z])/g).join(''));
    });
}