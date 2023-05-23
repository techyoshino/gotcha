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
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'gotcha' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
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
define( 'AUTH_KEY',         'Zfe`sQ_f4o0%Ur<QqS.Ow?ybP1NS@QMVbf{`5tBR$O!GuH`e[ BVo@SHWE,n2Pl!' );
define( 'SECURE_AUTH_KEY',  'oZEKj$eOu%j66scb<H`R3(u8XiY0HWQ>rH!/L]TW#@]Z |ZqO-fLm`&x~P+Nf2E5' );
define( 'LOGGED_IN_KEY',    'WJ9?yw:6[g1FW1 D[P^b>!Einst<yDT2t5XI.#M8M97vXmWmN+wvW=itvkJmi Mf' );
define( 'NONCE_KEY',        'UUV?~W>|hXypNw(N&?cGa$:cX%|bc]SHoWCS2qsTFSWPlMC cUD4:yKrbg-+X}lN' );
define( 'AUTH_SALT',        'l,aX{B]giho^~o)4%-cE{^ccwEwbyKex)6jk%t,=`;O| M}C]->W!*Z @u5D/nZ=' );
define( 'SECURE_AUTH_SALT', '?Amp-m!=p8 JmM7?Rec=+(_e|,!e}mE7!1ET`PvV:?{Lx!@UF&^.a8sC%lLCxFiW' );
define( 'LOGGED_IN_SALT',   'Ez<8QCu!VQgwMnN)9Y>L~Yl1/~TQFR2|=5OY[T8k6]`w>wxhbC[, ]:d4++aB*&8' );
define( 'NONCE_SALT',       'iG u2q}w|L=d`:yJ~};7AqMxL<J`Zl(J6Z#Y!;&bC19WE?eq55[9pDw#/h=F;OB0' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp636b81';

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
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
