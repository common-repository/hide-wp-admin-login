=== Hide WP Admin Login ===
Contributors: AppAspect
Tags: Hide WP Admin Login, Custom Login URL, WordPress Login URL, Change Login URL
Requires at least: 5.6
Tested up to: 6.4
Requires PHP: 7.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Change WordPress wp-login.php URL to anything you want.

== Description ==
This plugin *Hide WP Admin Login* allows to change the default WordPress Admin URL from wp-login.php and wp-admin to anything you want. All original links turn the default theme to “404 Not Found” page without rename or change files in core, nor does it add rewrite rules. Secure your website in just minutes with the *Hide WP Admin Login* plugin. Protect your WordPress site against hacker bots and spammers. Deactivating this plugin brings your site back exactly to the state it was before.

== Installation ==
1. Go to Plugins › Add New.
2. Search for *Hide WP Admin Login*.
3. Look for this plugin, download and activate it.
4. The page will redirect you to the settings. Change your login url there.
5. You can change this option any time you want, just go back to Settings › Hide WP Admin Login.

== Frequently Asked Questions ==
= I forgot my login URL! =

Remove or rename the `hide-wp-admin-login` folder from your `plugins` folder and log in through wp-login.php and reinstall the plugin.

To check login URL, go to your MySQL database and look for the value of `hwal_page` in the options table.

= Registration and lost password URL =
There are two separate URL for that. example: /{your_custom_url}?action=register or /{your_custom_url}?action=lostpassword

NOTE: Replace your_custom_url without {}.

== Screenshots ==
1. Hide WP Admin Login Settings

== Changelog ==
= 1.0.0 =
* Initial release.

== Upgrade Notice ==
Automatic updates should work perfectly, but we still recommend you back up your site.

If you encounter issues with after an update, flush the permalinks by going to WordPress > Settings > Permalinks and hitting “Save.” That should return things to normal.