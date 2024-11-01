=== Block Top Spammers ===
Contributors: tfnab
Donate link: http://ten-fingers-and-a-brain.com/donate/
Tags: comments, spam, akismet, htaccess
Requires at least: 2.8
Tested up to: 2.9
Stable tag: 0.5

Block Top Spammers displays a list of your top spammers' IP addresses. It also generates a blacklist for your .htaccess file.

== Description ==

*Block Top Spammers* displays a list of your top spammers' IP addresses, based on all comments in your database that are marked as spam. It also generates a blacklist for your `.htaccess` file to block those spammers from your website entirely, thus taking load off the server. – You will need another plugin (like [Akismet](http://akismet.com/)) to identify the spam.

PHP 5 and Apache required.

== Installation ==

1. Upload the entire `top-spammers` directory to the `/wp-content/plugins` directory
1. Activate *Block Top Spammers* through the 'Plugins' menu in WordPress
1. Make sure you have another plugin installed and activated to identify the spam (if you don't, take a look at [Akismet](http://akismet.com/))

== Frequently Asked Questions ==

= Why do I have to edit .htaccess manually? =

There's two reasons for this:

1. Allowing Wordpress to write to `.htaccess` as it does when you change the permalink structure would break many of my Wordpress installations. Since I wrote this plugin first and foremost for my own use I would have never benefitted from this functionality.
1. I actually had the plugin update `.htaccess` automatically. But then I blocked myself from my test site... Blocking an IP address through `.htaccess` is quite a harsh measure. It doesn't only prevent bots at that IP from posting comments, it prevents users who happen to use that same IP from accessing the blog. – I intend to automatically write to `.htaccess` in a future release, with a few security mechanisms added...

= Will the plugin work with older versions of Wordpress? =

It might. **But I don't support it!**

I have written the plugin for and tested it with blogs running the 2.8 version of Wordpress. It should probably work fine with any 2.7 version. With 2.6 the admin page looks odd and checking/unchecking all table rows is not working.

== Screenshots ==

1. Block Top Spammers displays a list of spammers' IP addresses and generates a blacklist for your `.htaccess` file

== Changelog ==

= 0.5 =

* blocking access to `wp-comments-post.php` only
* error document gives a short explanation why access was denied, links to http://ten-fingers-and-a-brain.com/top-spammers.html
* admin page moved to the Comments section of the admin menu
* IPv6 compatibility
* Notice errors fixed
* automatic updates of .htaccess (when writable)

= 0.4.3 =

* links to whois (ARIN, RIPE, APNIC, LACNIC, AfriNIC)
* can't blacklist an IP that has comments pending moderation (likely ham because Akismet hasn't caught them)
* links to ham or moderation queue for IP addresses with ham or comments pending moderation
* layout improvements

= 0.4.2 =

* link from Block Top Spammers page to edit-comments.php to display the spam comments from a certain IP
* better support for WP 2.9 and newer: when deleting comments the commentmeta table gets purged, too

= 0.4.1 =

* plugin now officially licensed under GPLv3
* list of top spammers can be ordered by IP address
* show no. of different (unique) spammers' IP addresses
* admins can't lock themselves out, i.e. their own client IP can't be blacklisted
* can't blacklist an IP that also has approved comments (ham)

= 0.4 =

* separated the code into three .php files
  * top-spammers.php the main plugin file, always loaded, contains minimal code to reduce loading time
  * top-spammers-wp.php the plugin class file, contains all the original plugin code, only loaded in backend
  * top-spammers-template.php the admin-page template file, contains just the admin-page html code, only loaded when admin-page is displayed
* blacklist not autoloaded to reduce loading time
* i18n
* l10n for: de_DE (language file by Martin Lormes)

= 0.3 =

* initial public release

== Upgrade Notice ==

= 0.5 =

Blocking access to `wp-comments-post.php` only, and automatic updates of .htaccess

= 0.4.3 =

Links to whois services of the five Regional Internet registries, layout improvements, can't block an IP that has comments pending moderation

= 0.4.2 =

Supports WordPress 2.9 commentmeta feature, i.e. that table gets purged, too, to avoid orphaned commentmeta entries

== Roadmap ==

= 0.6 (April 2010) =

* edit blacklist (i.e. remove individual addresses, manually add addresses and/or subnets)
* manually override .htaccess location
* customize 403 error shown to blocked spammers

= 1.0 (May 2010) =

* Multi-Site support (WordPress 3.0, maybe WPMU 2.8/2.9)
