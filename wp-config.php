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
define( 'DB_NAME', 'wp_practice' );

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
define( 'AUTH_KEY',         'M%4R[ntWaJ{=c:0[/tjS<6j5!T/{#}ZrJPPR.$g@DnRchIu~{/85VpWa8Z*2wpcg' );
define( 'SECURE_AUTH_KEY',  '_wp5]E)wop8XAY;5_*`1]e,m~94C*:Y&Z{5%Ap#OZvi/kHn9TL!!WtTuTwg_?LI7' );
define( 'LOGGED_IN_KEY',    'Dc^q;}Er;&H6bfd>IG?)V_@OP)M[TvjUbz+v@lQHr_2a8UU_0pRf# LMJtn1IKt]' );
define( 'NONCE_KEY',        'I-*h$M:()Zq5YH{F2>Sf-PS#/VgC>uE&0[3=-kFx*3Ay.|i`uT=Gor%jc0gxVKV&' );
define( 'AUTH_SALT',        'hs<)U*N~_A]-?.KFYD[|.2m}vPZE/y Dn>HT7+z!qO;;!z9b,d{EJ+y*^#SrvKRT' );
define( 'SECURE_AUTH_SALT', 'YKk<k]u2xej)ONR)7`s)1ifi +F_Ubc,bgnTJ>Ws|L/cUmBnm[.}krkWuC$4t|l$' );
define( 'LOGGED_IN_SALT',   'z>Jho[sp&aXqi^Y!Azj.5}ZZ (uo4vW?v;m]=Y7:)ca<whT-xM[>Yoaye%z+*NEZ' );
define( 'NONCE_SALT',       't*[T{ Zh1vI=k(5W19!@c6C]Mk(/v,N203<SbtYq70tZZ&:i;4D0D%`q=0=4d[g)' );

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
