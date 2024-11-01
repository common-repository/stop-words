<?php
/**
 * @package StopWords
 */
/*
Plugin Name: Stop Words
Plugin URI: http://stop-words.obelchenko.ru
Description: Проверка наличия запрещенных слов в постах. Когда совпадение найдено, сообщение не публикуется. Check for the presence of prohibited words in posts. When a match is found, the post is not published.
Version: 1.1
Author: Mikhail Obelchenko
Author URI: http://obelchenko.ru/
*/

/*
Copyright Mikhail Obelchenko (e-mail: master@obelchenko.ru)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

*/

if ( !function_exists( 'add_action' ) ) {
	echo 'Плагин нельзя запускать напрямую!';
	exit;
}

define( 'STOPWORDS_NAME', 'Stop Words' );
define( 'STOPWORDS_VERSION', '3.2' );
define( 'STOPWORDS_MINIMUM_WP_VERSION', '5.0' );
define( 'STOPWORDS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'STOPWORDS_DELETE_LIMIT', 100000 );
define( 'STOPWORDS_PLUGIN_BASENAME', plugin_basename(__FILE__));
define( 'STOPWORDS_ICON_URL', plugin_dir_url(__FILE__)."/icon.png");
define( 'STOPWORDS_IMG_URL', plugin_dir_url(__FILE__)."/img.png");


register_activation_hook(   __FILE__, array( 'CStopWords', 'on_activation' ) );
register_uninstall_hook(    __FILE__, array( 'CStopWords', 'on_uninstall' ) );
add_action( 'plugins_loaded', array( 'CStopWords', 'init' ) );

require_once( STOPWORDS_PLUGIN_DIR . 'class.stop-words.php' );

?>