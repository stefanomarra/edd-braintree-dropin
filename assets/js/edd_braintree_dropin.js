var $ = jQuery.noConflict();

$(document).ready(function() {

	var client_token = braintree_config.client_token;
	braintree.setup(client_token, 'dropin', {
		container: 'edd_braintree_dropin_container',
		form: 'edd_purchase_form'
	});

	$(document).on('click', '#edd-purchase-button', function(e) {
		var purchase_form = document.getElementById('edd_purchase_form');

		console.log( purchase_form.checkValidity() );

		if ( typeof purchase_form.checkValidity === "function" && false === purchase_form.checkValidity() && $('[name="payment_method_nonce"]').val() != '' ) {
			e.preventDefault();
			return false;
		}
		else {
			var e = document.createEvent('Event');
			e.initEvent('submit', true, true);
			purchase_form.dispatchEvent(e);
		}

	});
});

