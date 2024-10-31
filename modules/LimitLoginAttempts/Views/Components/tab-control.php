<?php
/**
 * Header of tab panel of Protect Login's settings page
 *
 * @package Protect Login
 */

/**
 * Prints the tabbar of the options page
 *
 * @param string $page Menu Slug.
 * @param string $active_tab    Tab to highlight.
 *
 * @return void
 */
function protect_login_print_tab_header( string $page, string $active_tab = 'tab1' ) {
	$admin_url = admin_url( $page . '?page=' . PROTECT_LOGIN_SLUG . '&tab=' );
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline">
			<?php echo esc_html__( 'Protect Login', 'protect-login' ); ?>
		</h1>
		<hr class="wp-header-end">
		<h2 class="nav-tab-wrapper">
			<a
				href="<?php echo esc_url( $admin_url . 'tab1' ); ?>"
				class="nav-tab <?php echo 'tab1' === $active_tab ? esc_html( 'nav-tab-active' ) : ''; ?> "
			>
				<?php echo esc_html__( 'Brute Force Protection', 'protect-login' ); ?>
			</a>

			<a
				href="<?php echo esc_url( $admin_url . 'tab2' ); ?>"
				class="nav-tab <?php echo 'tab2' === $active_tab ? esc_html( 'nav-tab-active' ) : ''; ?> "
			>
				<?php echo esc_html__( 'Password', 'protect-login' ); ?>
			</a>

			<a
				href="<?php echo esc_url( $admin_url . 'tab3' ); ?>"
				class="nav-tab <?php echo 'tab3' === $active_tab ? esc_html( 'nav-tab-active' ) : ''; ?> "
			>
				<?php echo esc_html__( 'Blocklist', 'protect-login' ); ?>
			</a>

			<a
				href="<?php echo esc_url( $admin_url . 'tab4' ); ?>"
				class="nav-tab <?php echo 'tab4' === $active_tab ? esc_html( 'nav-tab-active' ) : ''; ?> "
			>
				<?php echo esc_html__( 'Allowlist', 'protect-login' ); ?>
			</a>

			<a
				href="<?php echo esc_url( $admin_url . 'tab5' ); ?>"
				class="nav-tab <?php echo 'tab5' === $active_tab ? esc_html( 'nav-tab-active' ) : ''; ?> "
			>
				<?php echo esc_html__( 'Blocked IP addresses', 'protect-login' ); ?>
			</a>

			<a
					href="<?php echo esc_url( $admin_url . 'tab-remoteapi' ); ?>"
					class="nav-tab <?php echo 'tab-remoteapi' === $active_tab ? esc_html( 'nav-tab-active' ) : ''; ?> "
			>
				<?php echo esc_html__( 'Remote API', 'protect-login' ); ?>
			</a>
		</h2>
	<div class="tab-content">
	</div>


	<?php
}

/**
 * Prints the footer of the options page
 *
 * @return void
 */
function protect_login_print_tab_footer() {
	echo '</div></div>';
}
