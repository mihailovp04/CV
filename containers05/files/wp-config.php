<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'wordpress' );

/** Database password */
define( 'DB_PASSWORD', 'wordpress' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '.BK9=h7u3_L{~10/;U=.OpYU&/wih4[1-+x@5E~[Fx.b%]|j-.t?f~W[16TS^X;2' );
define( 'SECURE_AUTH_KEY',  'fSj0.p+yYi|mzoo/oE}!vbK>DTcxUE-w3#s$E{]?FGQci4>%&bL{<,I/[cYPR(?I' );
define( 'LOGGED_IN_KEY',    '!e{kM5.8sjnzfjK 7`ehNh*G_C]fWEm5?~gpW#z*jSX|)/}yAxfwkagLhAQs uNQ' );
define( 'NONCE_KEY',        '>`groQ@Lp-I2.~Jj*M-P8AJwD-oi7L;fa**YE}!:z8l&p%@9UW%N?&`P`f.Dp`@K' );
define( 'AUTH_SALT',        '+Lva)DxGL!Ynoj#~:^9{SEFZkT)+i57j.vPIVJP_0z6v5S[-n!xaLJ,JSMjjbP>$' );
define( 'SECURE_AUTH_SALT', '[9DL1X`}GEXkGdW,K*pcF#Zj&Sm%]B c4kh98n3oK{?[h{(Wp`w`J%?JNAn5t};o' );
define( 'LOGGED_IN_SALT',   'PeuPVmMWD/@qx/]tfY0t.+7A;U2+X[6u(mE6|.bgo!&irYyp|}FplmdTQ4TIUT74' );
define( 'NONCE_SALT',       'AXv{a8#M2ivv_g^i)9OcOQ!o17Y?=}tCaNMYl3PhAB/KZ^7eq]ENkVe]3{<`5CAd' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';