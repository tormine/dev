=== Secure Invites ===
Contributors: mrwiblog
Donate link: http://www.stillbreathing.co.uk/donate/
Tags: buddypress, invite, invitation, secure, lock, email, signup, registration
Requires at least: 2.7
Tested up to: 3.5.1
Stable tag: 1.2.5

Secure Invites is a Wordpress plugin that allows you to only allow invited people to sign up.

== Description ==

This plugin stops access to your signup page, except where the visitor has been invited and clicked the link in their invitation email. Your users invite people, and you can see who has sent the most invitations, and how many resulting signups have occurred. Other features:

* Restrict the ability to invite people to users who have been registered only for a certain number of days or more
* View the number of invites sent and resulting signups per month
* View the users who have sent the most invites, and the number of resulting signups
* Browse all invitations sent (auto paginated)
* Change the default email text
* Set after how many days an invitation will expire
* Works with different locations of signup page (default: /wp-signup.php)
* Set the message to show if someone tries to sign up with no valid invitation
* Turn off security on signup form, allowing anyone to sign up (this does not affect the ability to invite people)
* Show an invitation form on the dashboard
* Show best inviters on your site with a shortcode
* Set any number of special codes with which people can sign up without being invited
* View reports on the number of people who have signed up with each code

This plugin is based on the invitation plugin by kt (Gord) from http://www.ikazoku.com.

There are a few extra features introduced in version 0.9:

= Introducer points system =

Each time somebody (Person A) invites a friend (Person B), and Person B signs up, Person A is awarded 5 points. If Person B invites someone (Person C) and they sign up, Person A is awarded 2 points. And if Person C invites someone (Person D) and they sign up, Person A is awarded 1 point (and, of course, Person B is awarded 2 points). Using this pyramid-like system you can see who is inviting not just the most people, but the best kind of people.

The introducer points are stored in the users meta table with the key `secure_invite_points`. You can retrieve a persons points using this function: `get_usermeta( [user ID], "secure_invite_points" )` substituting [user ID] for their actual user ID.

= Bulk deletion of invites =

As an administrator you can now select multiple invitations and delete them. This is much better than the one-at-a-time deletion method in previous versions.

= Shortcodes =

There are three shortcodes available:

1) By using the [inviteform] shortcode you can place an invitation form on any post or page.

2) Using [bestinviters] will show a list of the top 6 inviters by points, with the points they have currently got.

3) Using [myinviter] will show the diplay name for the person who invited the current user. Using [myinviter id="123"] will show the display name for the person who invited the user with ID 123.

= Automatic BuddyPress theme integration =

If your BuddyPress theme is the default theme (for BuddyPress version 1.2 or above), or a child of the default theme, or uses the same template hooks as the default theme, you can put the invitation form in the following places by just ticking the right box in your settings screen:

* Before any list of members
* After any list of members
* At the top of every page
* Before your site homepage
* After your site homepage
* At the top of the default sidebar
* At the bottom of the default sidebar

The invitation form is hidden by default, and is shown by clicking an "Invite a friend" button which makes the form slide into view.

= Preset settings =

If you want to quickly set up invitations without messing with lots of settings, there are now four presets you can use:

* Anyone can join with or without an invitation, and all users can invite as many people as they like
* Signup is just for invited people, and all users can invite as many people as they like
* Signup is just for invited people, and all users who have been registered for 30 days or more can invite as many people as they like
* Signup is just for invited people, and all users who have been registered for 30 days or more can invite up to 10 people

Or you can use your own custom settings just as before.

= Overriding of special users =

Sometimes you want particular users to be able to invite more friends than the default, or perhaps you want to stop a particular user from inviting anybody at all. Now you can, by searching for the user and changing their own individual settings. You san set whether they are allowed to send invitations at all, and of so how many (either a limited number or unlimited).

== Installation ==

For standard WordPress: just install the plugin as normal from the plugin repository.

For Wordpress MU and WordPress MultiSite: there are two options. Either place the plugin in your /wp-content/mu-plugins/ directory (*not* /wp-content/plugins/), this method requires no activation. Or install the plugin as normal from the plugin repository and enable it for all sites in your network.

To enable the template form in your template page you should call the secure\_invite\_form() function like this:

&lt;php? secure\_invite\_form(); ?&gt;

There are three optional parameters in this function, they are:

1. The CSS class to be applied to the form
2. The message to be shown when the invitation has been successfully sent (by default this is '&lt;p class=&quot;success&quot;&gt;Thanks, your invitation has been sent&lt;/p&gt;')
3. The message to be shown when the invitation could not be sent (by default this is '&lt;p class=&quot;error&quot;&gt;Sorry, your invitation could not be sent&lt;/p&gt;')

So to set the CSS class of the form to 'inviteform', and the success message to 'Yay!' and the error message to 'Oops!' you would use this:

&lt;php? secure\_invite\_form( 'inviteform', 'Yay!', 'Oops!' ); ?&gt;

In addition, you can now automatically show the invitation form in your BuddyPress theme (if it is a child of the 1.2 or greater default theme, or it has all the same template hooks). Just tick the right boxes in the admin settings screen for where you want the invitation form to appear in your theme.

== Frequently Asked Questions ==

= Why did you write this plugin? =

To scratch my own itch when developing [Wibsite.com](http://wibsite.com "The worlds most popular Wibsite"). Hopefully this plugin helps other developers too.

= Does this plugin work with BuddyPress? =

Yes, several users have reported it works fine. You just need to change the URL of the signup form from the default (wp-signup.php) to the BuddyPress page so the plugin knows which URL to secure. In addition, you can now automatically show the invitation form in your BuddyPress theme (if it is a child of the 1.2 or greater default theme, or it has all the same template hooks). Just tick the right boxes in the admin settings screen for where you want the invitation form to appear in your theme.

An invitation form can be easily put into your template page, look at the Installation details for more information.

== Screenshots ==

1. The users invitation form
2. The admin reports
3. The admin settings page

== Upgrade Notice ==

Pleae make sure you are always using the latest version of the plugin.

== Changelog ==

= 1.2.5 (2013/05/29) =

* Fixed incorrect use of $wpdb->prepare() method (thanks to @maximinime for reporting this: http://wordpress.org/support/topic/missing-argument-2-for-wpdb)
* Fixed incorrect casing of $current_user->id - changed to $current_user->ID

= 1.2.4 (2011/09/13) =

* Fixed incorrect response code (http://wordpress.org/support/topic/plugin-secure-invites-plugin-generates-false-500-errors)
* Fixed bug with custom registration URL (http://wordpress.org/support/topic/plugin-secure-invites-invitation-link-does-not-honor-https-setting)

= 1.2.3 (2011/09/13) =

* Fixed bug with pagination links (http://wordpress.org/support/topic/invitation-list-pages-not-found)

= 1.2.2 (2011/08/17) =

* Fixed bug with deleting multiple invites

= 1.2.1 (2011/06/03) =

* Fixed bug with best inviters invite rate

= 1.2 (2011/04/12) =

* Fixed bug with secure_invite_user_invites_remaining() function

= 1.1.5 (2011/04/03) =

* Fixed bug where users could still send invites after they had run out of invites to send
* Added function to reset a users invite settings to the global default
* System now shows reason why a user cannot send invites

= 1.1.4.1 (2011/03/02) =

* Fixed session_start() bug

= 1.1.4 (2011/03/02) =

* Added ability to increase number of available invites for all users who have had an invite limit set
* Put reason for invite button not showing on the main settings page (this was a regularly-reported bug)
* Fixed bug which showed invite reports to non-admin users
* Fixed divide by zero bug when viewing an individual users invite report

= 1.1.3 (2010/12/27) =

* Added [bestinviters] shortcode
* Added [myinviter] shortcode
* Fixed bugs with database collation which led to empty reports
* Made support for different versions of WordPress more robust
* Added support for invitation codes, so people can register without being invited as long as they have a valid code
* Added reports for invitation codes

= 1.1.2 (2010/11/02) =

* Allowed site administrators to invite no matter what the other settings are
* Added new preset for only allowing administrators to invite people
* Fixed bug with is_user_logged_in() function
* Added facility for administrators to send bulk invites

= 1.1.1 (2010/10/10) =

Fixed security problem for logged-in users
Added [inviteform] shortcode

= 1.1 (2010/10/08) =

Fixed BuddyPress redirect bug (thanks to Alessandro: http://www.ilbigliettino.com/)
Added mini admin report in a dashboard widget
Added invite form in a dashboard widget

= 1.0.6 (2010/09/08) =

Fixed bug for new users invite lockout

= 1.0.5 (2010/09/03) =

Fixed incorrect registration links for standard WordPress and BuddyPress sites

= 1.0.4 (2010/09/02) =

Fixed BuddyPress registration bug (thanks to Patrick Neyman: http://patrickneyman.com/). Made the default settings more sensible. Added proper notification for why the invitation button can be disabled.

= 1.0.3 (2010/08/10) =

Fixed bug with secure_invite_user_can_invite() when time limit is 0

= 1.0.2 (2010/07/31) =

Fixed bug with is_site_admin()/is_super_admin()

= 1.0.1 =

Fixed bug in MultiSite and MU admin.

= 1.0 =

Made the plugin work with standard (i.e. not MU or MultiSite) WordPress.

= 0.9.9 (2010/07/11) =

Fixed bug allowing an email address to be repeatedly invited (thanks to Chestnut from http://blog.bng.net for reporting this bug)

= 0.9.8 (2010/07/04) =

Fixed bug reported here: http://wordpress.org/support/topic/411404

= 0.9.7 =

Compatibility with WP 3.0, fixed small bugs

= 0.9.6 =

Updated plugin URI

= 0.9.5 =

Removed Plugin Register

= 0.9.4 =

Fixed conflict bug with group invite screen

= 0.9.3 =

Fixed bug with automatic BuddyPress integration options

= 0.9.2 =

Fixed bug with template tag form (thanks to Rune from http://vixenmagazine.no)

= 0.9.1 =

Added Plugin Register code

= 0.9 =

Added automatic BuddyPress theme integration, added inviter points system, added bulk deletion of invites, added settings presets, added override for special users, fixed bugs

= 0.8.5 =

Added registration email URL to settings

= 0.8.4 =

Allowed multiple registration URLs to be protected, changed email headers to fix from address bug

= 0.8.3 =

Added a support link and donate button

= 0.8.1 =

Added HTML comments for reasons why a user cannot send invites to help with troubleshooting

= 0.8 =

Added stripslashes() to fix display errors (thanks to Mark from http://of-cour.se/ for reporting that)

= 0.7 =

Disabled invite form if site registrations have been disabled (thanks to Mark of http://of-cour.se/ for the suggestion)

= 0.6 =

Added limit to the number of invitations a user can send, added secure\_invite\_form() function for display in a theme page, added deletion of invites for site admins, cleaned up the architecture

== To-do ==

Next on the list for this plugin is the ability to invite multiple people at the same time (with the same message).

Then, adding the ability for site admins to only allow hand-picked users to send invitations (thanks to Tuomas for that suggestion here: http://www.stillbreathing.co.uk/blog/2009/01/14/wordpress-mu-plugin-secure-invites/#comment-24240).