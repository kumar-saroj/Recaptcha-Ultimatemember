<?php
add_action( 'um_after_register_fields', 'um_recaptcha_add_captcha', 500 );
//add_action( 'um_after_login_fields', 'um_recaptcha_add_captcha', 500 );
//add_action( 'um_after_password_reset_fields', 'um_recaptcha_add_captcha', 500 );
function um_recaptcha_add_captcha($args)
{
    global $ultimatemember; 
    
	?>
        <div class="captchabox">			
        <script src="https://www.google.com/recaptcha/api.js"></script>
        <div class="g-recaptcha brochure__form__captcha" data-sitekey="6LeMGj4aAAAAANWnDgFqV9b-zW3cR5plx8ds-3fC"></div>
        <?php if ( UM()->form()->has_error( 'recaptcha' ) ) { ?>
	        <div class="um-field-error"><?php _e( UM()->form()->errors['recaptcha'] ); ?></div>
            <?php } ?>
		</div>
		
    <?php
}
function um_recaptcha_validate( $args ) {
    $your_secret = "6LeMGj4aAAAAAPw5243VeXm1p6PXgVMBRp0kY9Gz";

    $client_captcha_response = filter_input( INPUT_POST, 'g-recaptcha-response' );

	$user_ip = $_SERVER['REMOTE_ADDR'];

	$response = wp_remote_get( "https://www.google.com/recaptcha/api/siteverify?secret=$your_secret&response=$client_captcha_response&remoteip=$user_ip" );

	$error_codes = array(
		'missing-input-secret'   => __( 'The secret parameter is missing.', 'um-recaptcha' ),
		'invalid-input-secret'   => __( 'The secret parameter is invalid or malformed.', 'um-recaptcha' ),
		'missing-input-response' => __( 'Please confirm you are not a robot', 'um-recaptcha' ),
		'invalid-input-response' => __( 'The response parameter is invalid or malformed.', 'um-recaptcha' ),
		'bad-request'            => __( 'The request is invalid or malformed.', 'um-recaptcha' ),
		'timeout-or-duplicate'   => __( 'The response is no longer valid: either is too old or has been used previously.', 'um-recaptcha' ),
	);


	if ( is_array( $response ) ) {

		$result = json_decode( $response['body'] );

		if ( isset( $result->{'error-codes'} ) && ! $result->success ) {
			foreach ( $result->{'error-codes'} as $key => $error_code ) {
				UM()->form()->add_error( 'recaptcha', $error_codes[ $error_code ] );
			}
		}

	}
}
add_action( 'um_submit_form_errors_hook', 'um_recaptcha_validate', 20 );
?>
