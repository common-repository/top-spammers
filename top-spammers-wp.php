<?php
/**
 * Wordpress Plugin "Block Top Spammers" ({@link http://ten-fingers-and-a-brain.com/wordpress-plugins/top-spammers/ Plugin Homepage})
 * class file (the WP-version)
 *
 * Block Top Spammers displays a list of your top spammers' IP addresses, based on all comments in your database that are marked as spam.
 * It also generates a blacklist for your .htaccess file to block those spammers from your website entirely, thus taking load off the server.
 * – You will need another plugin (like {@link http://akismet.com/ Akismet}) to identify the spam.
 *
 * @package TopSpammers
 * @subpackage WP
 * @category tfnabWordpressPlugins
 * @version 0.5 / SVN: $Id: top-spammers-wp.php 221017 2010-03-23 19:50:38Z tfnab $
 * @since 0.4
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
 * Wordpress Plugin "Block Top Spammers" ({@link http://ten-fingers-and-a-brain.com/wordpress-plugins/top-spammers/ Plugin Homepage})
 * – this class is merely a wrapper for the plugin's (static) functions (the WP-version)
 *
 * Block Top Spammers displays a list of your top spammers' IP addresses, based on all comments in your database that are marked as spam.
 * It also generates a blacklist for your .htaccess file to block those spammers from your website entirely, thus taking load off the server.
 * – You will need another plugin (like {@link http://akismet.com/ Akismet}) to identify the spam.
 *
 * @package TopSpammers
 * @subpackage WP
 * @category tfnabWordpressPlugins
 * @version 0.5 / SVN: $Id: top-spammers-wp.php 221017 2010-03-23 19:50:38Z tfnab $
 * @author Martin Lormes {@link http://ten-fingers-and-a-brain.com/}
 *
 * @todo users need to be able to remove a single address from the blacklist
 *
 * @todo actually write to .htaccess
 * @todo block entire subnets
 *
 *
 */
class pluginTopSpammers
{
  /** loads the plugin */
  function init ()
  {
    // i18n/l10n
    load_plugin_textdomain ( 'top-spammers', '', basename ( dirname ( __FILE__ ) ) );
    
    // add plugin page to admin menu
    add_action ( 'admin_menu', array ( __CLASS__, 'add_admin_menu_page' ) );
  }
  
  /** adds plugin page to admin menu; put it right underneath 'Dashboard' */
  function add_admin_menu_page ()
  {
    $page = add_comments_page ( __ ( 'Block Top Spammers', 'top-spammers' ), __ ( 'Block Top Spammers', 'top-spammers' ), 'edit_files', 'top-spammers', array ( __CLASS__, 'admin_menu_page' ) );
  }
  
  /** the plugin page and all the actual code – old php style spaghetti code */
  function admin_menu_page ()
  {
    // make $wpdb available in the method context
    global $wpdb;
    
    // get blacklist, initialize to empty array
    $blacklist = get_option ( 'top-spammers_blacklist' );
    if ( empty ( $blacklist ) ) $blacklist = array ();
    
    // check blacklist 'format'
    $blacklist_version = get_option ( 'top-spammers_version' );
    if ( !$blacklist_version OR TOP_SPAMMERS_VERSION != $blacklist_version ) // blacklist needs an update
    {
      delete_option ( 'top-spammers_blacklist' );
      add_option ( 'top-spammers_blacklist', $blacklist, '', 'no' );
      update_option ( 'top-spammers_version', TOP_SPAMMERS_VERSION );
    }
    
    // user action: reset blacklist
    if ( isset ( $_POST['action'] ) AND 'top-spammers-reset-blacklist' == $_POST['action'] )
    {
      check_admin_referer ( 'top-spammers-reset-blacklist' );
      $blacklist = array ();
      update_option ( 'top-spammers_blacklist', $blacklist );
    }
    
    // user action: purge spam
    if ( isset ( $_POST['action'] ) AND 'top-spammers-purge-spam' == $_POST['action'] AND isset ( $_POST['delete_ip'] ) )
    {
      check_admin_referer ( 'top-spammers-purge-spam' );
      
      // silently prevent the user from blocking themselves by not allowing them to blacklist their own/current ip address
      // NOTE: spam originating from that IP is not purged, either
      $delete_ip = array_diff ( $_POST['delete_ip'], array ( $_SERVER['REMOTE_ADDR'] ) );
      
      // merge blacklist and selected ip addresses
      $blacklist = array_unique ( array_merge ( $blacklist, $delete_ip ) );
      usort ( $blacklist, array ( __CLASS__, 'orderByIp' ) );
      update_option ( 'top-spammers_blacklist', $blacklist );
      
      // actually purge spam originating from selected ip addresses
      $deletelist = '("' . implode ( '","', $delete_ip ) . '")';
      
      // purge commentmeta table
      if ( isset ( $wpdb->commentmeta ) )
      {
        $deletemetaqry = "DELETE FROM $wpdb->commentmeta WHERE comment_id IN ( SELECT comment_ID FROM $wpdb->comments WHERE comment_approved = 'spam' AND comment_author_IP IN $deletelist )";
        $wpdb->query ( $deletemetaqry );
      }
      
      $deleteqry = "DELETE FROM $wpdb->comments WHERE comment_approved = 'spam' AND comment_author_IP IN $deletelist";
      $num_deleted = $wpdb->query ( $deleteqry );
      
      echo '<div id="message" class="updated fade"><p><strong>' . sprintf ( __ ( '%d spam deleted' , 'top-spammers' ), $num_deleted ) . '</strong></p></div>';
      
    }
    
    // where is .htaccess ??
    $htaccess = get_home_path () . '.htaccess';
    
    $tsrules = array ();
    
    $tsrules[] = "<Files wp-comments-post.php>";
    $tsrules[] = "Order allow,deny";
    $tsrules[] = "allow from all";
    $tsrules[] = "";
    foreach ( $blacklist as $ip ) $tsrules[] = "deny from $ip";
    $tsrules[] = "";
    $tsrules[] = "ErrorDocument 403 '<title>Access denied!</title><style><!-- p,address{margin-left:3em;} span{font-size:smaller;} --></style><h1>Access denied!</h1><p>Your IP address has been blacklisted because a larger number of spam comments originated from the same source.</p><p>Please refer to <a href=http://ten-fingers-and-a-brain.com/top-spammers.html>ten-fingers-and-a-brain.com/top-spammers.html</a> for a more detailed explanation.</p><h2>Error 403</h2><address><span>top-spammers/0.5</span></address>'";
    $tsrules[] = "</Files>";
    
    $tsoldrules = extract_from_markers ( $htaccess, 'top-spammers' );
    
    if ( $tsoldrules != $tsrules )
    {
      $htaccess_uptodate = false;
      // write to .htaccess
      $htaccess_updated = insert_with_markers ( $htaccess, 'top-spammers', $tsrules );
    }
    else $htaccess_uptodate = true;
    
    // get all spammers' ip addresses, count them, order: top spammers first; as an assoc array
    $qry = "SELECT comment_author_IP AS ip, COUNT(*) AS count FROM $wpdb->comments WHERE comment_approved = 'spam' GROUP BY ip ORDER BY count DESC";
    $ips = $wpdb->get_results ( $qry, ARRAY_A );
    
    // get all hammers' ip addresses
    $ham = $wpdb->get_col ( "SELECT comment_author_IP AS ip FROM $wpdb->comments WHERE comment_approved = '1' GROUP BY ip" );
    $ham_pending = $wpdb->get_col ( "SELECT comment_author_IP AS ip FROM $wpdb->comments WHERE comment_approved = '0' GROUP BY ip" );
    
    // users are allowed to order by ip
    if ( is_array ( $ips ) && isset ( $_GET['orderby'] ) && 'ip' == $_GET['orderby'] ) usort ( $ips, array ( __CLASS__, 'orderByIpArray' ) );
    
    // some display settings
    // these will eventually be configurable and stored in wp_options
    $display_top = 25; // don't show all spammers, only the top x
    $display_treshold_ptr = 5; // lookup the hostname only if there are x (or more) spam comments from that ip
    
    // load admin-page template file
    include ( dirname ( __FILE__ ) . '/top-spammers-template.php' );
  }
  
  /** @since 0.4.1 */
  function orderByIpArray ( $a, $b )
  {
    return self::orderByIp ( $a['ip'], $b['ip'] );
  }
  
  /** @since 0.5 */
  function orderByIp ( $a, $b )
  {
    if ( self::isIPv6 ( $a ) && self::isIPv6 ( $b ) ) return strnatcmp ( self::normalizeIPv6 ( $a, true ), self::normalizeIPv6 ( $b, true ) );
    if ( self::isIPv6 ( $a ) ) return 1;
    if ( self::isIPv6 ( $b ) ) return -1;
    return strnatcmp ( $a, $b );
  } // function orderByIp
  
  /** @since 0.5 */
  function isIPv6 ( $a )
  {
    return false !== strpos ( $a, ':' );
  } // function isIPv6
  
  /** @since 0.5 */
  function normalizeIPv6 ( $a, $decimal = false )
  {
    $a = explode ( ':', $a );
    foreach ( $a as &$x ) if ( '' == $x )
    {
      $x = str_repeat ( ':', 8 - count ( $a ) );
      break;
    }
    $a = explode ( ':', implode ( ':', $a ) );
    foreach ( $a as &$x ) if ( '' == $x ) $x = '0'; else if ( $decimal ) $x = hexdec ( $x );
    return implode ( ':', $a );
  } // function normalizeIPv6
  
} // class pluginTopSpammers
