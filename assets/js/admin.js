jQuery(document).ready(function ($) {

    $('form[name="wp_smtp_form"] input[type="submit"]').on('click', function (event) {
            event.preventDefault();

            let submitButton = $(this);
            submitButton.val('Checking credentials...');
            let form = $(this).closest('form');
            let inputs = {};

            // Get all the inputs from the form
            $(this).parents('form').find('input[type="text"]').each(function (index, element) {

                    switch ($(element).attr('name')) {                    
                        case 'wp_smtp_host':
                            inputs.host = $(element).val();
                            break;
                        case 'wp_smtp_port':
                            inputs.port = $(element).val();
                            break;
                        case 'wp_smtp_username':
                            inputs.username = $(element).val();
                            break;
                    };
            });

            inputs.password = $('input[name="wp_smtp_password"]').val();
            inputs.smtpsecure = $('input[name="wp_smtp_smtpsecure"]:checked').val();
            inputs.smtpauth = $('input[name="wp_smtp_smtpauth"]:checked').val();

        const options = {
            method: 'POST',
            url: ajaxurl,
            data: {
                action: 'wp_smtp_save_settings',
                inputs: inputs,
                nonce: wp_smtp_admin_vars.nonce,
            },
            success: function (response) {
                if (response.success) {
                    form.submit();
                } else {
                    submitButton.val('Save Changes');
                    alert(response.data.error);
                }
            }
        };

        $.ajax(options);

    });
});