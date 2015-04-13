$(function () {
    var $conf = $('input#password_confirmation, input#secret_confirmation');
    if ($conf.length) {
        $('input#password, input#secret').password();
        $conf.each( function( i, e ) {
            var $hid_conf = $(this).clone();
            $hid_conf.attr('type', 'hidden');
            $conf.parents('.form-group').replaceWith($hid_conf);
        });

        $('form').submit(function (event) {
            var $form = $(this);
            $('input:submit, button.submit', $form).button('loading');
            $('input#password_confirmation:hidden').val($('input#password').val());
            $('input#secret_confirmation:hidden').val($('input#secret').val());
        });
    }
});