jQuery(document).ready(function ($) {
    /** global variables */
    var invalidForm = true;

    var checkoutType = $("button.pay-with-mula-button").data("checkout-type");

    var fields = [
        {'name': 'MSISDN', 'help': 'include your country code, excluding the `+` or `brackets`'}, 
        {'name': 'currencyCode', 'help': 'the currency you would like to use'},
        {'name': 'customerEmail', 'help': 'provide an active email, to receive invoices & receipts'}, 
        {'name': 'customerLastName', 'help': 'provide your last name'}, 
        {'name': 'customerFirstName', 'help': 'provide your first name'}, 
    ];
    /** global variables */

    if (typeof MulaCheckout !== 'undefined') {
        MulaCheckout.addPayWithMulaButton({
            checkoutType: checkoutType,
            className: "pay-with-mula-button",
        });
    }

    $(".checkout-form-field").on('keyup change', function() {
        var fieldValue = $(this).val();
        var fieldName = $(this).attr('name');
        var helpTextElement = $(this).siblings("[id$='Help']");

        if (fieldValue === "" || fieldValue === null) {
            switch (fieldName) {
                case 'MSISDN':
                    helpTextElement.text('provide a valid phone number');
                    break;
                case 'customerEmail':
                    helpTextElement.text('provide a valid email address');
                    break;
                case 'customerLastName':
                    helpTextElement.text('provide a valid last name');
                    break;
                case 'customerFirstName':
                    helpTextElement.text('provide a valid first name');
                    break;
                default:
                    break;
            }

            helpTextElement.addClass('errored-field');
            helpTextElement.removeClass('valid-field');
        } else {
            var iterator = 0;
            while (iterator < fields.length) {
                if (fieldName == fields[iterator]['name']) {
                    helpTextElement.text(fields[iterator]['help']);
                }
                iterator++;
            }

            helpTextElement.addClass('valid-field');
            helpTextElement.removeClass('errored-field');
        }
    });

    $("#mula-checkout-form").on('submit', function(event) {
        event.preventDefault();

        var iterator = 0;
        var requestObj = {'action': 'handle_mula_checkout_request'};
        while(iterator < fields.length) {
            var field = fields[iterator]['name'];
            requestObj[field] = $("#"+field).val();
            iterator++;
        }

        $.ajax({
              type: "POST",
              dataType: 'json',
              data: requestObj,
              url: MULA_PLUGIN_AJAX_OBJ.AJAX_URL,
              success: function(response) {
                    MulaCheckout.renderMulaCheckout({
                        checkoutType: checkoutType,
                        merchantProperties: response
                    });
              },
              error: function(error) {
                    console.log(error.responseText);
              },
        });
    });
});