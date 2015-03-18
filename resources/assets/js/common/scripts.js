$(function () {
    var $conf = $('input#password_confirmation');
    if ($conf.length) {
        $('input#password').password();
        var $hid_conf = $conf.clone();
        $hid_conf.attr('type', 'hidden');
        $conf.parents('.form-group').replaceWith($hid_conf);

        $('form').submit(function (event) {
            var $form = $(this);
            $('input:submit, button.submit', $form).button('loading');
            $('input#password_confirmation:hidden').val($('input#password').val());
        });
    }
});