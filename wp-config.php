<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'horsly_dev');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'ViRucides00');

/** MySQL hostname */
define('DB_HOST', 'localhost:8889');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('WP_ALLOW_REPAIR', true);

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '._EHc+G|pES>Gh;16MOTU#o 9+>Z^:~g;6:=6FAS;T)-:#v[<t7;q,tWcrio8nn=');
define('SECURE_AUTH_KEY',  '8ilD(mA=rW-?HodS&$}*2).#(hb-WA~g`@|8d@6x,p.LsN5eNd6`5IdH+RPo*n++');
define('LOGGED_IN_KEY',    'QtqqeOc.<[?S[7D]X?2g)(K,ZBS=4E#?;+y8.F>*smZT+D!W`]Xv+eDw;+p_33D4');
define('NONCE_KEY',        's#p)*+&EuG:Ls[<k|J[@(WP-&<^i=|-kidvN,mML|^b-Wm-|2QylF?Aj~)P$t^Ly');
define('AUTH_SALT',        '+[dCqSetdhF8 Ve%PPkTHo$2B*OH$:0?m? v=#kT-c?XgD2V-Bh5t ,fD!_Y^]m8');
define('SECURE_AUTH_SALT', '`]g%&]]0S/,Z@nz5y>>~eEXF$V]A-q()u2jFL1JPcd;W>Y9+kTx3^ym9=JvPWI*5');
define('LOGGED_IN_SALT',   'MwK&$yZN+9aJ([{@tfa|T-R$ZeS83+RtY~:rd>49Z`L]#{iA_0=Lsb-=, FXU.8g');
define('NONCE_SALT',       'wI,85SZ|>>jnSUX=`&sB),4sfjot=l+f>~:+WzkEI!3Ls]<)W$+Ft%pr-a^pd)CX');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
