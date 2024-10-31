/**
 * File to set the required minimum password strength
 *
 * @package Protect Login
 */

jQuery( document ).ready(
	function ($) {
		$(
			"<button class='button button-primary' disabled id='password_too_short'>" +
			php_vars.password_too_short_text + "</button>"
		).insertBefore( ".submit" );

		$( "#password_too_short" ).css( 'display', 'none' );

		$( document ).on(
			'DOMSubtreeModified',
			'#pass-strength-result',
			function () {
				var strengthMeter    = $( this ).attr( 'class' );
				var allowedStrengths = php_vars.allowed_strengths;

				if (strengthMeter !== '') {
					if (allowedStrengths.includes( strengthMeter )) {
						$( "[name='submit']" ).css( 'display', 'inline' );
						$( '#createusersub' ).css( 'display', 'inline' );
						$( 'submit' ).onclick         = function () {
							$( 'your-profile' ).submit();
						};
						$( "#createusersub" ).onclick = function () {
							$( 'createuser' ).submit();
						};

						$( "#password_too_short" ).css( 'display', 'none' );
					} else {
						$( "[name='pw_weak']" ).prop( "checked", false );
						$( "[name='pw_weak']" ).css( 'visibility', 'hidden' );
						$( '#pw-weak-text-label' ).css( 'visibility', 'hidden' );
						$( '.pw-weak' ).css( 'visibility', 'hidden' );
						$( "#createusersub" ).css( 'display', 'none' );
						$( "[name='submit']" ).prop( "disabled", true );
						$( "[name='pw_weak']" ).prop( "checked", false );
						$( "[name='submit']" ).css( 'display', 'none' );
						$( 'submit' ).onclick         = function () {
							return false;
						};
						$( "#createusersub" ).onclick = function () {
							return false;
						};
						$( "#password_too_short" ).css( 'display', 'inline' );
					}
				}
			}
		);
	}
);