<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'smartfood' );

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
define( 'AUTH_KEY',         'vlP&ZM.mQp]/p(^dbLr]#4uLV|(}dAY1CQJ>d!U03VP{/TU]m,t7@d|pUixlGGIn' );
define( 'SECURE_AUTH_KEY',  '%[c>vwr=x]uYSC/R11 1:fhTw7 ,rrPlnAZjB0u2Te:f(ka.O3h=lX~`!ED@%bQL' );
define( 'LOGGED_IN_KEY',    'f^{Zq*vG(9Rq*c`]<|ft=:O56089h~;pt7-L1R&Zi8i;*?W/UZG|;QC+KC5l=-^j' );
define( 'NONCE_KEY',        '3Qk}J*h`lQcLg66jc?J8kSW`x&?wE3 bOb&UBtk^kH<wZ#kflpmI#*;J:k&xDfo1' );
define( 'AUTH_SALT',        'ZHnh=h-_!Rh_F6BXc4d}`{C)l`6AQ& JrS`OGyBeM eG0 [:`m{re{XTT1aXv8DS' );
define( 'SECURE_AUTH_SALT', '|--oKNhbO43a`JU^U_85>u6upMY0YTF(t71u$|pxUt2;w948.spu#RwukgDtPv3V' );
define( 'LOGGED_IN_SALT',   'b*i/>.dey2hEW<(5]xEl<cj<?:e6MWw3fxSiT[&:oao,WenEM178.(oT3X9-kC<t' );
define( 'NONCE_SALT',       'PaYwlHmxuDqc`4)-mi)uJ-Rmg{C{7BPZKTaG%j_oK?Bv5h[|E@6{+d|AWCYuj2Pc' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
