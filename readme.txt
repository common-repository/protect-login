=== Protect Login ===
Contributors: protectone, tidschi, krafit
Tags: login, security, authentication
Requires at least: 5.7
Tested up to: 6.6
Stable tag: 1.3.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Add an additional layer of protection to your WordPress login and make sure bad actors have a hard time guessing your user's login credentials.

== Description ==
Out of the box, WordPress allows unlimited attempts to log in. This opens up opportunities for attackers to crack passwords simply by trying over and over again. This kind of attack is called brute-force, and _Protect Login_ mitigates this by slowing down the login after a series of subsequent failed attempts.

But we did not stop there. We're also working on ways to improve the security of your WordPress passwords. Currently, we do this by allowing you to enforce a password policy to make sure your users don't use weak passwords for their accounts. 


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/protect-login` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress.
1. The default settings will be applied automatically. To change them, navigate to **Settings > Protect Login**


== Frequently Asked Questions ==

= Who are you folks? =

We're part of [group.one](https://www.group.one/en/wordpress), a group of European hosting companies. We're here to help you succeed online.

= Why did you build this plugin? =
We care about WordPress and keeping WordPress sites secure. So we decided it was time to take the code of the original Limit Login Attempts plugin and build on top of it.
We did this for you. Protect Login is 100% free and will not bother you with nasty upsells or scare marketing. You have better things to do, don't you?

= Why not reset failed attempts on a successful login? =

This is very much by design. Otherwise, you could simply brute force the "admin" password by logging in as your own user every 4th attempt.

= How do I know if my site is behind a reverse proxy? =

If you're not sure about this, chances are your site is not behind a reverse proxy. However, Protect Login's settings offer an option to activate proxy mode.
A reverse proxy is a server between the site and the Internet (perhaps handling caching or load-balancing). This makes getting the correct client IP to block slightly more complicated.

= Can I put my IP on an allowlist to avoid getting locked out? =

Yes, there is an allowlist tab in Protect Login's settings.

= I locked myself out while testing this plugin; what do I do? =

Either wait until your account/IP is unblocked, or if you have FTP or SSH access to the site, rename it "wp-content/plugins/protect-login" to deactivate the plugin.

= Do you support IPv6 addresses? =
Yes, if the webserver passes an IPv6 address to your WordPress installation, the plugin has no problems to handle IPv6 from 1.2.0.

== Changelog ==
= 1.3.0 =
* Count of currently locked-out address visible in "At a glance" Widget
* Fixed bug on activation plugin through wp-cli in a multisite environment
* Remote API support
* Improved multisite support

= 1.2.0 =
* IPv6 support
* Endpoints for WP-CLI
* Added filter for password strength
* "Settings" link in plugin overview
* Bugfix: string "password too short" erroneous appeared in Quick Draft widget, removed.

= 1.1.1 =
* Removed unused strings
* Added translator comments
* Restructured some strings for easier translations

= 1.1.0 =
* Tested with WordPress 6.6
* Added Multisite Support
* Added filters to set protection levels programmatically 
* Fixed issue with timestamps always using UTC

= 1.0.1 =
* Fixed minor bugs

= 1.0 =
* Initial version
* based on Limit Login Attempts 1.7.1 by Johan Eenfeldt

== Upgrade Notice ==
