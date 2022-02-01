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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

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
define( 'AUTH_KEY',         '5l}/I=AG?}<AM;KZ2f7QEK}Zy7_hSBx2KK}ah[.avuvk(% }y3.T[DMjyZ71^.=*' );
define( 'SECURE_AUTH_KEY',  'BHi~1ed_{TTcY=NZwPJNo|aj*/eQ>C0v<[S<P5VW_V?FxW60J.G{wzR%Y_2f|%kZ' );
define( 'LOGGED_IN_KEY',    'y-,6OQr-:t!znVXX0av&4j9^iI<7[a@OQN`)E.F,n6zfa~T7F!rWZ?Xeu<6fg0$.' );
define( 'NONCE_KEY',        'CJ7%2#}p_P+dt&Lrj+wtRLU;K:{,&AWWw=qXIq7j5gQ@wJ6ri.xYs#Ny[a]!;[~V' );
define( 'AUTH_SALT',        'DE8LkdOR34)s)*yho%VQ1/_d)=)D(kPUV&JqIH8V|YEg@,:5~Kpl_sh|kVeTAaDL' );
define( 'SECURE_AUTH_SALT', '-;5ZQDs9Q|B-bw7(F(N 2e6Z_,5/9r2(#H3Q=D$)lD!3vw[Y|nN.gHep)m(j<!@>' );
define( 'LOGGED_IN_SALT',   'E6Nv>q%^1W7&n=Bbno)R}C7?UzA3?Vn!LH05T28k5b8DI_e{hLszIL2U)*Rf$-*|' );
define( 'NONCE_SALT',       ',F#YfM>xFcj&{uB L!0n:Ed$m<q3<>~@!8=Pm|Xm,F-O;*bU45YSWlyb0{t#QBu>' );

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
