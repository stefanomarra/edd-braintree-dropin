var $ = jQuery.noConflict();

function params_unserialize(p){
	var ret = {},
		seg = p.replace(/^\?/,'').split('&'),
		len = seg.length, i = 0, s;
	for (;i<len;i++) {
		if (!seg[i]) { continue; }
		s = seg[i].split('=');
		ret[s[0]] = s[1];
	}
	return ret;
}

$(document).ready(function() {

	var initBraintree = function() {

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
	};

	if ( $('#edd_braintree_dropin_container').length > 0 ) {
		initBraintree();
	}

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

		var data = { action : '' };
		if ( typeof settings.data !== 'undefined' ) {
			// Parse ajax data
			data = params_unserialize(settings.data);
		}

		// If ajax is loading a payment gateway, init braintree dropin container if found
		if ( data.action == 'edd_load_gateway' ) {

			setTimeout(function() {

				if ( $('#edd_braintree_dropin_container').length > 0 ) {
					initBraintree();
				}
			}, 300);
		}

		// Handle invalid nonce ajax response
		if ( $(xhr.responseText).find('#edd_error_nonce_invalid').length > 0 ) {

			$('#edd_error_nonce_invalid').remove();
			if ( $('.edd_errors .edd_error').length == 0 ) {
				$('.edd_errors').remove();
			}
		}
	});

});

