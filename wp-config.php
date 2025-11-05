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
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'vB,Hz NPaJCMI]S~Tf|R^YQ0h#t-7.Nu}tBQ@UQlFwnmK,P^V[za!c][GWJL>9rZ' );
define( 'SECURE_AUTH_KEY',  '%3_;sGqDA%N+wkPs^_o+NdF)D8u@`pEwvr_b^e8^^A%K+op&f*79-v+c k[cp5Gr' );
define( 'LOGGED_IN_KEY',    '3ACTB<e>?HA/pv&4;f}|P6-rbktMhZ3<{xKp>%3IA!kY_.-RqG6(0b>nmtL@b|xk' );
define( 'NONCE_KEY',        '^~UNcsXjR#LcAY Wx|twB@Hh=/R d%6GNiWwtZ<b&/z5@~YvtR(R;y3OTr9^bVWS' );
define( 'AUTH_SALT',        '[nA?1Vuyxh5Z> mE11tFGGS`7W]Gv56TGe&y{VQ(r2(KuDpE;W:;.TLKEQyGGxIW' );
define( 'SECURE_AUTH_SALT', ']Lg6%M8Yx3g-kI%QR,B:^E@DdE^m@-=+/h;SAZeHH..o*}(WLcV-/9][{UcO&MBP' );
define( 'LOGGED_IN_SALT',   '|jpH/lPH[c FR&7DmW {MTU+Q0)|m8^9Pel>CL=0fSPCD:r.x4H~FC?Q#)8:n3U5' );
define( 'NONCE_SALT',       'lQ#l{]jS;4z7ggw`w%xd5h,QVIUQAx4{oCrT3Y&utX*Z!^MG~{*54^X/OJ4{OC`o' );

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
