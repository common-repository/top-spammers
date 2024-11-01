<?php
/*
Plugin Name: Block Top Spammers
Plugin URI: http://ten-fingers-and-a-brain.com/wordpress-plugins/top-spammers/
Version: 0.5
Description: Block Top Spammers displays a <a href="edit-comments.php?page=top-spammers">list of your top spammers' IP addresses</a>, based on all comments in your database that are marked as spam. It also generates a blacklist for your .htaccess file to block those spammers from your website entirely, thus taking load off the server. &mdash; You will need another plugin (like <a href="http://akismet.com/">Akismet</a>) to identify the spam.
Author: Martin Lormes
Author URI: http://ten-fingers-and-a-brain.com/
Text Domain: top-spammers
*/
/**
 * Wordpress Plugin "Block Top Spammers" ({@link http://ten-fingers-and-a-brain.com/wordpress-plugins/top-spammers/ Plugin Homepage})
 *
 * Block Top Spammers displays a list of your top spammers' IP addresses, based on all comments in your database that are marked as spam.
 * It also generates a blacklist for your .htaccess file to block those spammers from your website entirely, thus taking load off the server.
 * – You will need another plugin (like {@link http://akismet.com/ Akismet}) to identify the spam.
 *
 * @package TopSpammers
 * @category tfnabWordpressPlugins
 * @version 0.5 / SVN: $Id: top-spammers.php 221017 2010-03-23 19:50:38Z tfnab $
 * @author Martin Lormes {@link http://ten-fingers-and-a-brain.com/}
 */
/*
Copyright (c) 2009-2010 Martin Lormes

This program is free software; you can redistribute it and/or modify it under 
the terms of the GNU General Public License as published by the Free Software 
Foundation; either version 3 of the License, or (at your option) any later 
version.

This program is distributed in the hope that it will be useful, but WITHOUT 
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with 
this program. If not, see <http://www.gnu.org/licenses/>.
*/
/**
 * plugin version
 *
 * used to identify the 'format' of the blacklist and update it when the plugin has been updated
 * – e.g. set autoload to no as of the 0.4 version
 *
 * @since 0.4
 */
define ( 'TOP_SPAMMERS_VERSION', '0.4' );

if ( is_admin () )
{
  /**
   * load the plugin class file and initialize the plugin
   * @since 0.4
   */
  require_once ( dirname ( __FILE__ ) . '/top-spammers-wp.php' );
  pluginTopSpammers::init();
}
