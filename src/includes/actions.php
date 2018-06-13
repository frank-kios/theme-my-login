<?php

/**
 * Theme My Login Action Functions
 *
 * @package Theme_My_Login
 * @subpackage Actions
 */

/**
 * Register default actions.
 *
 * @since 7.0
 */
function tml_register_default_actions() {

	// Login
	tml_register_action( 'login', array(
		'title'              => __( 'Log In' ),
		'slug'               => 'login',
		'handler'            => 'tml_login_handler',
		'show_on_forms'      => __( 'Log in' ),
		'show_nav_menu_item' => ! is_user_logged_in(),
	) );

	// Logout
	tml_register_action( 'logout', array(
		'title'              => __( 'Log Out' ),
		'slug'               => 'logout',
		'handler'            => 'tml_logout_handler',
		'show_on_forms'      => false,
		'show_in_widget'     => false,
		'show_nav_menu_item' => is_user_logged_in(),
	) );

	// Register
	tml_register_action( 'register', array(
		'title'              => __( 'Register' ),
		'slug'               => 'register',
		'handler'            => 'tml_registration_handler',
		'show_on_forms'      => (bool) get_option( 'users_can_register' ),
		'show_nav_menu_item' => ! is_user_logged_in(),
	) );

	// Lost Password
	tml_register_action( 'lostpassword', array(
		'title'             => __( 'Lost Password' ),
		'slug'              => 'lostpassword',
		'handler'           => 'tml_lost_password_handler',
		'network'           => true,
		'show_on_forms'     => __( 'Lost your password?' ),
		'show_in_nav_menus' => false,
	) );

	// Reset Password
	tml_register_action( 'resetpass', array(
		'title'             => __( 'Reset Password' ),
		'slug'              => 'resetpass',
		'handler'           => 'tml_password_reset_handler',
		'network'           => true,
		'show_on_forms'     => false,
		'show_in_widget'    => false,
		'show_in_nav_menus' => false,
	) );

	// Confirm Action (Data Requests)
	tml_register_action( 'confirmaction', array(
		'title'             => __( 'Your Data Request', 'theme-my-login' ),
		'slug'              => 'confirmaction',
		'handler'           => 'tml_confirmaction_handler',
		'show_on_forms'     => false,
		'show_in_widget'    => false,
		'show_in_nav_menus' => false,
	) );
}

/**
 * Register an action.
 *
 * @since 7.0
 *
 * @param string|Theme_My_Login_Action $action The action name or object.
 * @param array                        $args {
 *     Optional. An array of arguments for registering an action.
 * }
 * @return Theme_My_Login_Action The action object.
 */
function tml_register_action( $action, $args = array() ) {

	if ( ! $action instanceof Theme_My_Login_Action ) {
		$action = new Theme_My_Login_Action( $action, $args );
	}

	if ( $slug = get_site_option( 'tml_' . $action->get_name() . '_slug' ) ) {
		$action->set_slug( $slug );
	}

	return theme_my_login()->register_action( $action );
}

/**
 * Unregister an action.
 *
 * @since 7.0
 *
 * @param string|Theme_My_Login_Action $action The action name or object.
 */
function tml_unregister_action( $action ) {
	theme_my_login()->unregister_action( $action );
}

/**
 * Get an action.
 *
 * @since 7.0
 *
 * @param string|Theme_My_Login_Action $action Optional. The action name or object.
 * @return Theme_My_Login_Action|bool The action object if it exists or false otherwise.
 */
function tml_get_action( $action = '' ) {

	if ( $action instanceof Theme_My_Login_Action ) {
		return $action;
	}

	if ( empty( $action ) ) {
		if ( ! $action = get_query_var( 'action' ) ) {
			$action = tml_get_request_value( 'action' );
		}
	}

	return theme_my_login()->get_action( $action );
}

/**
 * Get all actions.
 *
 * @since 7.0
 *
 * @return array The actions.
 */
function tml_get_actions() {
	return theme_my_login()->get_actions();
}

/**
 * Determine if an action exists.
 *
 * @since 7.0
 *
 * @param string $action The action name.
 * @return bool True if the action exists or false otherwise.
 */
function tml_action_exists( $action ) {
	$exists = array_key_exists( $action, tml_get_actions() );

	/**
	 * Filter whether an action exists or not.
	 *
	 * @since 7.0
	 *
	 * @param bool   $exists Whether the action exists or not.
	 * @param string $action The action name.
	 */
	return apply_filters( 'tml_action_exists', $exists, $action );
}

/**
 * Determine if a TML action is being requested.
 *
 * @since 7.0
 *
 * @param string $action Optional. The action to check.
 * @return bool Is a TML action being requested?
 */
function tml_is_action( $action = '' ) {
	$is_action      = false;
	$current_action = tml_get_action();

	if ( $current_action && array_key_exists( $current_action->get_name(), tml_get_actions() ) ) {
		if ( empty( $action ) ) {
			$is_action = true;
		} elseif ( $action == $current_action->get_name() ) {
			$is_action = true;
		}
	}

	/**
	 * Filter whether a TML action is being requested or not.
	 *
	 * @since 7.0
	 *
	 * @param bool   $is_action Whether a TML action is being requested or not.
	 * @param string $action    The action name.
	 */
	return apply_filters( 'tml_is_action', $is_action, $action );
}

/**
 * Get the action title.
 *
 * @since 7.0
 *
 * @param string|Theme_My_Login_Action $action The action name or object.
 * @return string The action title.
 */
function tml_get_action_title( $action = '' ) {

	if ( ! $action = tml_get_action( $action ) ) {
		return;
	}

	$title = $action->get_title();

	/**
	 * Filter the action title.
	 *
	 * @since 7.0
	 *
	 * @param string $title The action title.
	 * @param string $name  The action name.
	 */
	return apply_filters( 'tml_get_action_title', $title, $action->get_name() );
}

/**
 * Get the action slug.
 *
 * @since 7.0
 *
 * @param string|Theme_My_Login_Action $action The action name or object.
 * @return string The action slug.
 */
function tml_get_action_slug( $action = '' ) {

	if ( ! $action = tml_get_action( $action ) ) {
		return;
	}

	$slug = $action->get_slug();

	/**
	 * Filter the action slug.
	 *
	 * @since 7.0
	 *
	 * @param string $slug The action slug.
	 * @param string $name The action name.
	 */
	return apply_filters( 'tml_get_action_slug', $slug, $action->get_name() );
}

/**
 * Get the action URL.
 *
 * @since 7.0
 *
 * @param string|Theme_My_Login_Action $action  The action name or object.
 * @param string                       $scheme  The URL scheme.
 * @param bool                         $network Whether to retrieve the URL for the current network or current blog.
 * @return string The action URL.
 */
function tml_get_action_url( $action = '', $scheme = 'login', $network = null ) {

	if ( ! $action = tml_get_action( $action ) ) {
		return;
	}

	$url = $action->get_url( $scheme, $network );

	/**
	 * Filter the action URL.
	 *
	 * @since 7.0
	 *
	 * @param string $url     The action URL.
	 * @param string $name    The action name.
	 * @param string $scheme  The URL scheme.
	 * @param bool   $network Whether to retrieve the URL for the current network or current blog.
	 */
	return apply_filters( 'tml_get_action_url', $url, $action->get_name(), $scheme, $network );
}

/**
 * Determine if an action has an associated page.
 *
 * @since 7.0.1
 *
 * @param string|Theme_My_Login_Action $action The action name of object.
 * @return bool|WP_Post The page object if one is found, false otherwise.
 */
function tml_action_has_page( $action = '' ) {

	if ( ! $action = tml_get_action( $action ) ) {
		return false;
	}

	if ( ! $page = get_page_by_path( tml_get_action_slug( $action ) ) ) {
		return false;
	}

	return $page;
}

/**
 * Handle an action.
 *
 * @since 7.0
 */
function tml_action_handler() {
	if ( ! tml_is_action() ) {
		return;
	}

	// Redirect to https login if forced to use SSL
	if ( force_ssl_admin() && ! is_ssl() ) {
		if ( 0 === strpos($_SERVER['REQUEST_URI'], 'http') ) {
			wp_safe_redirect( set_url_scheme( $_SERVER['REQUEST_URI'], 'https' ) );
			exit();
		} else {
			wp_safe_redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			exit();
		}
	}

	nocache_headers();

	tml_get_action()->handle();
}

/**
 * Handle the 'login' action.
 *
 * @since 7.0
 */
function tml_login_handler() {

	$errors = new WP_Error;

	// Set a cookie now to see if they are supported by the browser.
	$secure = ( 'https' === parse_url( wp_login_url(), PHP_URL_SCHEME ) );
	if ( ! isset( $_COOKIE[ TEST_COOKIE ] ) ) {
		setcookie( TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN, $secure );
		if ( SITECOOKIEPATH != COOKIEPATH ) {
			setcookie( TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN, $secure );
		}
	}

	// If the user wants ssl but the session is not ssl, force a secure cookie.
	if ( ! empty( $_POST['log'] ) && ! force_ssl_admin() ) {
		$user_name = sanitize_user( $_POST['log'] );
		$user      = get_user_by( 'login', $user_name );

		if ( ! $user && strpos( $user_name, '@' ) ) {
			$user = get_user_by( 'email', $user_name );
		}

		if ( $user ) {
			if ( get_user_option( 'use_ssl', $user->ID ) ) {
				force_ssl_admin( true );
			}
		}
	}

	if ( ! empty( $_REQUEST['redirect_to'] ) ) {
		$redirect_to = $_REQUEST['redirect_to'];
		// Redirect to https if user wants ssl
		if ( force_ssl_admin() && false !== strpos( $redirect_to, 'wp-admin' ) ) {
			$redirect_to = preg_replace( '|^http://|', 'https://', $redirect_to );
		}
	} else {
		$redirect_to = admin_url();
	}

	$reauth = empty( $_REQUEST['reauth'] ) ? false : true;

	if ( isset( $_POST['log'] ) || isset( $_GET['testcookie'] ) ) {

		$user = wp_signon( array(), force_ssl_admin() );

		$redirect_to = apply_filters( 'login_redirect', $redirect_to, isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '', $user );

		if ( ! is_wp_error( $user ) && empty( $_COOKIE[ LOGGED_IN_COOKIE ] ) ) {
			$redirect_to = add_query_arg( array(
				'testcookie' => 1,
				'redirect_to' => $redirect_to
			) );
			wp_redirect( $redirect_to );
			exit;
		}

		if ( empty( $_COOKIE[ LOGGED_IN_COOKIE ] ) ) {
			if ( headers_sent() ) {
				$user = new WP_Error( 'test_cookie', sprintf(
						__( '<strong>ERROR</strong>: Cookies are blocked due to unexpected output. For help, please see <a href="%1$s">this documentation</a> or try the <a href="%2$s">support forums</a>.' ),
						__( 'https://codex.wordpress.org/Cookies' ),
						__( 'https://wordpress.org/support/' )
					)
				);
			} elseif ( isset( $_GET['testcookie'] ) ) {
				// If cookies are disabled we can't log in even with a valid user+pass
				$user = new WP_Error( 'test_cookie', sprintf(
						__( '<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href="%s">enable cookies</a> to use WordPress.' ),
						__( 'https://codex.wordpress.org/Cookies' )
					)
				);
			}
		} else {
			$user = wp_get_current_user();
		}

		if ( ! is_wp_error( $user ) && ! $reauth ) {

			if ( ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' || $redirect_to == admin_url() ) ) {

				// If the user doesn't belong to a blog, send them to user admin. If the user can't edit posts, send them to their profile.
				if ( is_multisite() && ! get_active_blog_for_user( $user->ID ) && ! is_super_admin( $user->ID ) ) {
					$redirect_to = user_admin_url();

				} elseif ( is_multisite() && ! $user->has_cap( 'read' ) ) {
					$redirect_to = get_dashboard_url( $user->ID );

				} elseif ( ! $user->has_cap( 'edit_posts' ) ) {
					$redirect_to = $user->has_cap( 'read' ) ? admin_url( 'profile.php' ) : home_url();
				}

				wp_redirect( $redirect_to );
				exit;
			}

			wp_safe_redirect( $redirect_to );
			exit;
		}

		$errors = $user;
	}

	// Clear errors if loggedout is set.
	if ( ! empty( $_GET['loggedout'] ) || $reauth ) {
		$errors = new WP_Error;
	}

	// Some parts of this script use the main login form to display a message
	if ( isset( $_GET['loggedout'] ) && true == $_GET['loggedout'] ) {
		$errors->add( 'loggedout', __( 'You are now logged out.' ), 'message' );

	} elseif ( isset( $_GET['registration'] ) && 'disabled' == $_GET['registration'] ) {
		$errors->add( 'registerdisabled', __( 'User registration is currently not allowed.' ) );

	} elseif ( isset( $_GET['checkemail'] ) && 'confirm' == $_GET['checkemail'] ) {
		$errors->add( 'confirm', __( 'Check your email for the confirmation link.' ), 'message' );

	} elseif ( isset( $_GET['checkemail'] ) && 'newpass' == $_GET['checkemail'] ) {
		$errors->add( 'newpass', __( 'Check your email for your new password.' ), 'message' );

	} elseif ( isset( $_GET['checkemail'] ) && 'registered' == $_GET['checkemail'] ) {
		if ( tml_allow_user_passwords() ) {
			$errors->add( 'registered', __( 'Registration complete. You may now log in.', 'theme-my-login' ), 'message' );
		} else {
			$errors->add( 'registered', __( 'Registration complete. Please check your email.' ), 'message' );
		}

	} elseif ( isset( $_GET['resetpass'] ) && 'complete' == $_GET['resetpass'] ) {
		$errors->add( 'password_reset', __( 'Your password has been reset.' ), 'message' );

	} elseif ( strpos( $redirect_to, 'about.php?updated' ) ) {
		$errors->add( 'updated', __( '<strong>You have successfully updated WordPress!</strong> Please log back in to see what&#8217;s new.' ), 'message' );
	}

	$errors = apply_filters( 'wp_login_errors', $errors, $redirect_to );

	tml_set_errors( $errors );

	// Clear any stale cookies.
	if ( $reauth ) {
		wp_clear_auth_cookie();
	}
}

/**
 * Handle the 'logout' action.
 *
 * @since 7.0
 */
function tml_logout_handler() {

	check_admin_referer( 'log-out' );

	$user = wp_get_current_user();

	wp_logout();

	if ( ! empty( $_REQUEST['redirect_to'] ) ) {
		$redirect_to = $requested_redirect_to = $_REQUEST['redirect_to'];
	} else {
		$redirect_to = site_url( 'wp-login.php?loggedout=true' );
		$requested_redirect_to = '';
	}

	$redirect_to = apply_filters( 'logout_redirect', $redirect_to, $requested_redirect_to, $user );
	wp_safe_redirect( $redirect_to );
	exit;
}

/**
 * Handle the 'register' action.
 *
 * @since 7.0
 */
function tml_registration_handler() {

	if ( is_multisite() ) {
		wp_redirect( apply_filters( 'wp_signup_location', network_site_url( 'wp-signup.php' ) ) );
		exit;
	}

	if ( ! get_option( 'users_can_register' ) ) {
		wp_redirect( site_url( 'wp-login.php?registration=disabled' ) );
		exit;
	}

	if ( tml_is_post_request() ) {
		$user_login = isset( $_POST['user_login'] ) ? $_POST['user_login'] : '';
		$user_email = isset( $_POST['user_email'] ) ? $_POST['user_email'] : '';
		$errors = register_new_user( $user_login, $user_email );
		if ( ! is_wp_error( $errors ) ) {
			$redirect_to = ! empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : site_url( 'wp-login.php?checkemail=registered' );
			wp_safe_redirect( $redirect_to );
			exit;
		} else {
			tml_set_errors( $errors );
		}
	}
}

/**
 * Handle the 'lostpassword' action.
 *
 * @since 7.0
 */
function tml_lost_password_handler() {

	if ( tml_is_post_request() ) {
		$errors = tml_retrieve_password();
		if ( ! is_wp_error( $errors ) ) {
			$redirect_to = ! empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : site_url( 'wp-login.php?checkemail=confirm' );
			wp_safe_redirect( $redirect_to );
			exit;
		} else {
			tml_set_errors( $errors );
		}
	}

	if ( isset( $_REQUEST['error'] ) ) {
		if ( 'invalidkey' == $_REQUEST['error'] ) {
			tml_add_error( 'invalidkey', __( 'Your password reset link appears to be invalid. Please request a new link below.' ) );
		} elseif ( 'expiredkey' == $_REQUEST['error'] ) {
			tml_add_error( 'expiredkey', __( 'Your password reset link has expired. Please request a new link below.' ) );
		}
	}
}

/**
 * Handle the 'resetpass' action.
 *
 * @since 7.0
 */
function tml_password_reset_handler() {

	list( $rp_path ) = explode( '?', wp_unslash( $_SERVER['REQUEST_URI'] ) );
	$rp_cookie = 'wp-resetpass-' . COOKIEHASH;

	if ( isset( $_GET['key'] ) ) {
		$value = sprintf( '%s:%s', wp_unslash( $_GET['login'] ), wp_unslash( $_GET['key'] ) );
		setcookie( $rp_cookie, $value, 0, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
		wp_safe_redirect( remove_query_arg( array( 'key', 'login' ) ) );
		exit;
	}

	if ( isset( $_COOKIE[ $rp_cookie ] ) && 0 < strpos( $_COOKIE[ $rp_cookie ], ':' ) ) {
		list( $rp_login, $rp_key ) = explode( ':', wp_unslash( $_COOKIE[ $rp_cookie ] ), 2 );
		$user = check_password_reset_key( $rp_key, $rp_login );
		if ( isset( $_POST['pass1'] ) && ! hash_equals( $rp_key, $_POST['rp_key'] ) ) {
			$user = false;
		}
	} else {
		$user = false;
	}

	if ( ! $user || is_wp_error( $user ) ) {
		setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
		if ( $user && $user->get_error_code() === 'expired_key' ) {
			wp_redirect( site_url( 'wp-login.php?action=lostpassword&error=expiredkey' ) );
		} else {
			wp_redirect( site_url( 'wp-login.php?action=lostpassword&error=invalidkey' ) );
		}
		exit;
	}

	$errors = new WP_Error;

	if ( isset( $_POST['pass1'] ) && $_POST['pass1'] != $_POST['pass2'] ) {
		$errors->add( 'password_reset_mismatch', __( 'The passwords do not match.' ) );
	}

	do_action( 'validate_password_reset', $errors, $user );

	if ( ( ! $errors->get_error_code() ) && isset( $_POST['pass1'] ) && ! empty( $_POST['pass1'] ) ) {
		reset_password( $user, $_POST['pass1'] );
		setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
		wp_redirect( site_url( 'wp-login.php?resetpass=complete' ) );
		exit;
	} else {
		tml_set_errors( $errors );
	}
}

/**
 * Handle the 'confirmaction' action.
 *
 * @since 7.0
 */
function tml_confirmaction_handler() {
	if ( ! isset( $_GET['request_id'] ) ) {
		wp_die( __( 'Invalid request.' ) );
	}

	$request_id = (int) $_GET['request_id'];

	if ( isset( $_GET['confirm_key'] ) ) {
		$key    = sanitize_text_field( wp_unslash( $_GET['confirm_key'] ) );
		$result = wp_validate_user_request_key( $request_id, $key );
	} else {
		$result = new WP_Error( 'invalid_key', __( 'Invalid key' ) );
	}

	if ( is_wp_error( $result ) ) {
		wp_die( $result );
	}

	/** This action is documented in wp-login.php */
	do_action( 'user_request_action_confirmed', $request_id );
}
