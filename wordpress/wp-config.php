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
define( 'DB_NAME', 'wordpress' );

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
define( 'AUTH_KEY',         'CfMGM*n[r=nMrMsi;*S_)s`-T4iwua`l;[EMq(-v{MHAktRdg78yL0}eI~5x&Ps7' );
define( 'SECURE_AUTH_KEY',  'YRC8|^]*m)iO)=Ba,ntkt$`^P~>d6arzo0+HBWxl/cfaaNrOX.(1VrUajx~f-@$3' );
define( 'LOGGED_IN_KEY',    'A65E79*^T:ogRyZL(SQH&8T~1Va>Wpi+I;}]:n4|Kc`Pz;}|C{.,]n|Xo*_kITg6' );
define( 'NONCE_KEY',        '@-g+pJ^Q:quIeg<Y(<-bI_ljDF}TsYPc};Zd_!m(I1][7Y_(y4*<1:;8g->~_CFb' );
define( 'AUTH_SALT',        'W b7{VIA#*4[v4DQ,)N]pztip-Y7ccayDxr 8bxZ>Ow/Hc{L^Ltrr2M~kqS&!i!l' );
define( 'SECURE_AUTH_SALT', 'PIL|MP.D!eCKY;Zo.i@[M`37_*KRU2]TYnmZcP?Lx[6B.6TZ(8lScjCqwc(F,/IZ' );
define( 'LOGGED_IN_SALT',   'lEFhJPix$rY%0#xA`98|-0FLQUb}XW5-:OpP>~G<|rwl+zD+D]qIZulFiv5WUR:b' );
define( 'NONCE_SALT',       '=J<K]VO~V9Q,iLecT&&U63V ?5UR+<Fq<,sEbk`P`8V]z(o_j]S bi?`+fIduMfQ' );

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
