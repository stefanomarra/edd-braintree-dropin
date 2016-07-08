var $ = jQuery.noConflict();

$(document).ready(function() {

	var edd_braintree_dropin_checkout;
	var client_token = braintree_config.client_token;
	braintree.setup(client_token, 'dropin', {
		container: 'edd_braintree_dropin_container',
		form: 'edd_purchase_form',
		onReady: function(integration) {
			edd_braintree_dropin_checkout = integration;
		},
		onPaymentMethodReceived: function(payload) {
			$('#edd_purchase_form').append('<input type="hidden" name="payment_method_nonce" value="' + payload.nonce + '" />');

			setTimeout(function() {
				$('#edd-purchase-button').click();
			}, 1000);
		}
	});

	$(document).on('click', '#edd-purchase-button', function(e) {
		var purchase_form = document.getElementById('edd_purchase_form');

		if ( $('[name="payment_method_nonce"]').length == 0 ) {
			e.preventDefault();
			var e = document.createEvent('Event');
			e.initEvent('submit', true, true);
			purchase_form.dispatchEvent(e);
		}
	});

	$(document).ajaxComplete(function( event, xhr, settings ) {
		if ( $(xhr.responseText).find('#edd_error_nonce_invalid').length > 0 ) {
			$('#edd_error_nonce_invalid').remove();
			if ( $('.edd_errors .edd_error').length == 0 ) {
				$('.edd_errors').remove();
			}
		}
	});

});

