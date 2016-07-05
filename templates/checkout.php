<?php
	if ( !defined( 'ABSPATH' ) ) die();
?>

<fieldset class="edd_braintree_dropin_fields">
	<div class="bt-drop-in-wrapper">
		<div id="bt-dropin"></div>
	</div>
	<input name="payment_method_nonce" type="hidden" value="">
</fieldset>

<script src="https://js.braintreegateway.com/v2/braintree.js"></script>
<script>
	var checkout = new Checkout({
		formID: 'edd_purchase_form'
	});

	var client_token = "<?php echo Braintree_ClientToken::generate(); ?>";
	braintree.setup(client_token, "dropin", {
		container: "bt-dropin",
		paymentMethodNonceReceived: function (event, nonce) {
			setTimeout(function () {
				$('input[name="payment_method_nonce"]').val(nonce);
			}, 500);
		}
	});
</script>