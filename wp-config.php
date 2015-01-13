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
define('DB_NAME', 'photo2');

/** MySQL database username */
define('DB_USER', 'hyeddana');

/** MySQL database password */
define('DB_PASSWORD', 'wierdness2');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '>56+#=j(EI0|vnL/@9){+0SokP&NV#Z{+azy0.4zt@v8BmA.#~CK+{^Xm2R+  ^Y');
define('SECURE_AUTH_KEY',  'qh. @id^#/Fn_GIM|G|0P-S+CO,Q4~Ac6@JIh|BR*^B/[I` 0CHejx(R$^2IZ0xi');
define('LOGGED_IN_KEY',    'a|?/W)[* G#dkjOi-(@Z 4g{LMOf}w2*4cA-|i]^|T/&a#;+8@;T-;HRtEtgJ8p$');
define('NONCE_KEY',        'tc|IV5Z+%+/?(nI-sbTNwiuk!XVA.sto+ciX;oid,bUKT7*+pBq2{s/G8g2IvEE,');
define('AUTH_SALT',        'At-^pnZKpa](o386iFE;~[E*/;O>C8-fQbC0;o{#SS)^FE4z8f*CJDvHZ%V`ADoj');
define('SECURE_AUTH_SALT', 'Ci%^Q-VP-9:c@kFYm#tAn-|X%xyoYFVg|1JK%ug?_p4U+hoET{2w{yVMMBcqrn7q');
define('LOGGED_IN_SALT',   'B+^R7ba[}nyc*k97pOB9hkGi?KNl|Ic8LZN3|-Hg!9&Xm!9Uz+WM ]zpPpFT=V~I');
define('NONCE_SALT',       '+36M5dZI,j>;t&M_}iXZM|3@pOb!SCl--2|Wj2Zc>2H-,g3D8jHsCnT#ZaoW*`-m');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
