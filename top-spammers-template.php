<?php
/**
 * Wordpress Plugin "Block Top Spammers" ({@link http://ten-fingers-and-a-brain.com/wordpress-plugins/top-spammers/ Plugin Homepage})
 * admin-page template file
 *
 * Block Top Spammers displays a list of your top spammers' IP addresses, based on all comments in your database that are marked as spam.
 * It also generates a blacklist for your .htaccess file to block those spammers from your website entirely, thus taking load off the server.
 * – You will need another plugin (like {@link http://akismet.com/ Akismet}) to identify the spam.
 *
 * @package TopSpammers
 * @category tfnabWordpressPlugins
 * @version 0.5 / SVN: $Id: top-spammers-template.php 221017 2010-03-23 19:50:38Z tfnab $
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
?>
<div class="wrap">
  <div id="icon-edit-comments" class="icon32"><br /></div>
  <h2><?php _e ( 'Block Top Spammers' , 'top-spammers' ); ?></h2>
  <?php if ( empty ( $ips ) AND empty ( $blacklist ) ) : ?>
    <p><?php _e ( 'Lucky you: no spam.' , 'top-spammers' ); ?></p>
  <?php else : ?>
    <p><?php _e ( 'These are your top spammers\' IP addresses.' , 'top-spammers' ); ?></p>
    <div id="col-container">
      
      <div id="col-right">
        <h3><?php printf ( __ ( 'The top %d' , 'top-spammers' ), $display_top ); ?></h3>
        <?php if ( empty ( $ips ) ) : ?>
          <p><?php _e ( 'Lucky you: no new spam.' , 'top-spammers' ); ?></p>
        <?php else : ?>
          <p><?php printf ( __ ( 'You have spam from a total of %d different IP addresses.', 'top-spammers' ), count ( $ips ) ); ?></p>
          <form id="top-spammers-form" action="" method="post">
            <?php wp_nonce_field ( 'top-spammers-purge-spam' ); ?>
            <input type="hidden" name="action" value="top-spammers-purge-spam" />
            <div class="tablenav">
              <div class="alignleft actions">
                <input type="submit" value="<?php _e ( 'Delete spam comments from selected ip addresses, and add ip addresses to blacklist' , 'top-spammers' ); ?>" class="button-secondary" />
              </div>
            </div>
            <div class="clear"></div>
            <table class="widefat fixed">
              <thead>
                <tr>
                  <th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox"></th>
                  <th scope="col" id="ip" class="manage-column"><a title="<?php _e ( 'order by IP address' , 'top-spammers' ); ?>" href="edit-comments.php?page=top-spammers&amp;orderby=ip"><?php _e ( 'IP address' , 'top-spammers' ); ?></a><?php if ( isset ( $_GET['orderby'] ) && 'ip' == $_GET['orderby'] ) echo ' &#x25b2;'; // 2191 ?> (<?php _e ( 'Host name' , 'top-spammers' ); ?>)</th>
                  <th scope="col" id="count" class="manage-column num" style="width:80px;"><a title="<?php _e ( 'order by count (descending)' , 'top-spammers' ); ?>" href="edit-comments.php?page=top-spammers"><?php _e ( 'count' , 'top-spammers' ); ?></a><?php if ( !isset ( $_GET['orderby'] ) ) echo ' &#x25bc;'; // 2193 ?></th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th scope="col" class="manage-column column-cb check-column" style=""><input type="checkbox"></th>
                  <th scope="col" class="manage-column"><a title="<?php _e ( 'order by IP address' , 'top-spammers' ); ?>" href="edit-comments.php?page=top-spammers&amp;orderby=ip"><?php _e ( 'IP address' , 'top-spammers' ); ?></a><?php if ( isset ( $_GET['orderby'] ) && 'ip' == $_GET['orderby'] ) echo ' &#x25b2;'; // 2191 ?> (<?php _e ( 'Host name' , 'top-spammers' ); ?>)</th>
                  <th scope="col" class="manage-column num"><a title="<?php _e ( 'order by count (descending)' , 'top-spammers' ); ?>" href="edit-comments.php?page=top-spammers"><?php _e ( 'count' , 'top-spammers' ); ?></a><?php if ( !isset ( $_GET['orderby'] ) ) echo ' &#x25bc;'; // 2193 ?></th>
                </tr>
              </tfoot>
              <tbody>
                <?php foreach ( array_slice ( $ips, 0, $display_top ) as $ip ) : ?>
                  <tr>
                    <th scope="row" class="check-column">
                      <?php if ( $_SERVER['REMOTE_ADDR'] == $ip['ip'] ) : ?>
                        &nbsp; <a href="#footnote-1" title="<?php _e ( 'This is your IP. Don\'t lock yourself out!', 'top-spammers' ); ?>">1)</a>
                      <?php elseif ( in_array ( $ip['ip'], $ham ) ) : ?>
                        &nbsp; <a href="#footnote-2" title="<?php _e ( 'There\'s ham from the same IP. You shouldn\'t block readers, just bots!', 'top-spammers' ); ?>">2)</a>
                      <?php elseif ( in_array ( $ip['ip'], $ham_pending ) ) : ?>
                        &nbsp; <a href="#footnote-3" title="<?php _e ( 'There are unapproved comments from the same IP. I recomment reviewing them first. You shouldn\'t block readers, just bots!', 'top-spammers' ); ?>">3)</a>
                      <?php else : ?>
                        <input name="delete_ip[]" value="<?php echo $ip['ip']; ?>" type="checkbox" id="top-spammers-ip-<?php echo str_replace ( array ( '.', ':', ), '-', $ip['ip'] ); ?>" />
                      <?php endif; ?>
                    </th>
                    <td>
                      <label for="top-spammers-ip-<?php echo str_replace ( array ( '.', ':', ), '-', $ip['ip'] ); ?>"><?php echo $ip['ip']; ?></label>
                      <?php if ( $display_treshold_ptr <= $ip['count'] ) echo ' (' . gethostbyaddr ( $ip['ip'] ) . ')'; ?>
                      <div class="row-actions">
                        <a title="<?php echo sprintf ( __( 'View all the spam comments from %s', 'top-spammers' ), $ip['ip'] ); ?>" href="edit-comments.php?comment_status=spam&amp;s=<?php echo $ip['ip']; ?>"><?php _e( 'View spam', 'top-spammers' ); ?></a>
                        <?php if ( in_array ( $ip['ip'], $ham ) ) : ?>
                          <span class="top-spammers-approved">| <a title="<?php echo sprintf ( __( 'View all the approved comments from %s', 'top-spammers' ), $ip['ip'] ); ?>" href="edit-comments.php?comment_status=approved&amp;s=<?php echo $ip['ip']; ?>"><?php _e( 'View ham', 'top-spammers' ); ?></a></span>
                        <?php elseif ( in_array ( $ip['ip'], $ham_pending ) ) : ?>
                          <span class="top-spammers-pending">| <a title="<?php echo sprintf ( __( 'View unapproved comments from %s', 'top-spammers' ), $ip['ip'] ); ?>" href="edit-comments.php?comment_status=moderated&amp;s=<?php echo $ip['ip']; ?>"><?php _e( 'View pending', 'top-spammers' ); ?></a></span>
                        <?php endif; ?>
                        <span class="top-spammers-arin">| <a title="<?php echo sprintf ( __( 'Lookup %s in the ARIN WHOIS Database', 'top-spammers' ), $ip['ip'] ); ?>" href="<?php echo sprintf ( __( 'http://ws.arin.net/cgi-bin/whois.pl?queryinput=%s', 'top-spammers' ), $ip['ip'] ); ?>"><?php _e( 'ARIN', 'top-spammers' ); ?></a></span>
                        <span class="top-spammers-ripe">| <a title="<?php echo sprintf ( __( 'Lookup %s in the RIPE Database', 'top-spammers' ), $ip['ip'] ); ?>" href="<?php echo sprintf ( __( 'http://www.db.ripe.net/whois?searchtext=%s', 'top-spammers' ), $ip['ip'] ); ?>"><?php _e( 'RIPE', 'top-spammers' ); ?></a></span>
                        <span class="top-spammers-apnic">| <a title="<?php echo sprintf ( __( 'Lookup %s in the APNIC Whois Database', 'top-spammers' ), $ip['ip'] ); ?>" href="<?php echo sprintf ( __( 'http://wq.apnic.net/apnic-bin/whois.pl?searchtext=%s', 'top-spammers' ), $ip['ip'] ); ?>"><?php _e( 'APNIC', 'top-spammers' ); ?></a></span>
                        <span class="top-spammers-lacnic">| <a title="<?php echo sprintf ( __( 'Lookup %s in the LACNIC WHOIS', 'top-spammers' ), $ip['ip'] ); ?>" href="<?php echo sprintf ( __( 'http://lacnic.net/cgi-bin/lacnic/whois?query=%s', 'top-spammers' ), $ip['ip'] ); ?>"><?php _e( 'LACNIC', 'top-spammers' ); ?></a></span>
                        <span class="top-spammers-afrinic">| <a title="<?php echo sprintf ( __( 'Lookup %s in the AfriNIC Whois Database', 'top-spammers' ), $ip['ip'] ); ?>" href="<?php echo sprintf ( __( 'http://whois.afrinic.net/cgi-bin/whois?searchtext=%s', 'top-spammers' ), $ip['ip'] ); ?>"><?php _e( 'AfriNIC', 'top-spammers' ); ?></a></span>
                        </div>
                    </td>
                    <td align="right" style="padding-right:20px;"><?php echo $ip['count']; ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <div class="tablenav">
              <div class="alignleft actions">
                <input type="submit" value="<?php _e ( 'Delete spam comments from selected ip addresses, and add ip addresses to blacklist' , 'top-spammers' ); ?>" class="button-secondary" />
              </div>
            </div>
          </form>
          <p id="footnote-1"><small>1) <?php _e ( 'This is your IP. Don\'t lock yourself out!', 'top-spammers' ); ?></small></p>
          <p id="footnote-2"><small>2) <?php _e ( 'There\'s ham from the same IP. You shouldn\'t block readers, just bots!', 'top-spammers' ); ?></small></p>
          <p id="footnote-3"><small>3) <?php _e ( 'There are unapproved comments from the same IP. I recomment reviewing them first. You shouldn\'t block readers, just bots!', 'top-spammers' ); ?></small></p>
          <script type="text/javascript">
            //<![CDATA[
            document.getElementById('top-spammers-form').onsubmit=function(){return confirm('<?php _e ( 'You are about to delete spam comments from the selected ip addresses.\\n\\\'Cancel\\\' to stop, \\\'OK\\\' to delete.' , 'top-spammers' ); ?>');};
            //]]>
          </script>
        <?php endif; ?>
      </div>
      
      <div id="col-left">
        <h3><?php _e ( 'Your blacklist (rules for .htaccess)' , 'top-spammers' ); ?></h3>
        <?php if ( empty ( $blacklist ) ) : ?>
          <p><?php _e ( 'You haven\'t blacklisted anyone, yet.' , 'top-spammers' ); ?></p>
        <?php else : ?>
          <?php if ( $htaccess_uptodate ) : ?>
            <p style="color:#090;"><?php _e ( 'Your .htaccess file is up to date.' , 'top-spammers' ); // todo: show blacklist ?></p>
          <?php else : ?>
            <?php if ( $htaccess_updated ) : ?>
              <p style="color:#090;"><?php _e ( 'Your .htaccess file has been updated.' , 'top-spammers' ); // todo: show blacklist ?></p>
            <?php else : ?>
          <p style="color:#c00;"><?php _e ( 'Your .htaccess file is not writable. Please update it manually!' , 'top-spammers' ); ?></p>
          <form><p><textarea rows="15" class="large-text readonly" name="rules" id="rules" readonly="readonly" onfocus="this.select();"><?php
            echo "# BEGIN top-spammers\n";
            foreach ( $tsrules as $row ) echo "$row\n";
            echo "# END top-spammers\n";
          ?></textarea></p></form>
          <div class="form-wrap">
            <p><?php _e ( '<strong>Instructions:</strong> copy these lines to your .htaccess-file and those IP addresses will be blocked from accessing your site entirely, probably taking some load off your server.' , 'top-spammers' ); ?></p>
          </div>
            <?php endif; ?>
          <?php endif; ?>
          <br/><br/>
          <form id="top-spammers-reset-form" action="" method="post">
            <?php wp_nonce_field ( 'top-spammers-reset-blacklist' ); ?>
            <input type="hidden" name="action" value="top-spammers-reset-blacklist" />
            <input type="submit" value="<?php _e ( 'Reset blacklist (delete all ip addresses)' , 'top-spammers' ); ?>" class="button-secondary" />
          </form>
          <script type="text/javascript">
            //<![CDATA[
            document.getElementById('top-spammers-reset-form').onsubmit=function(){return confirm('<?php _e ( 'You are about to reset your blacklist.\\n\\\'Cancel\\\' to stop, \\\'OK\\\' to reset.' , 'top-spammers' ); ?>');};
            //]]>
          </script>
        <?php endif; ?>
      </div>
      
    </div>
  <?php endif; ?>
  <p class="clear"></p>
</div>
