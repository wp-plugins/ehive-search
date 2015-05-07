=== eHive Search ===
Contributors: vernonsystems 
Donate link:http://ehive.com/what_is_ehive
Tags: ehive, collection, museum, archive, history
Requires at least: 3.3.1
Tested up to: 4.2.2
Stable tag: 2.1.5
License: GPL2+
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin that give you the power to search eHive Objects from your WordPress website.

== Description ==

This plugin is part of a suite of plugins created by Vernon Systems Ltd., which give you the power to embed eHive functionality into your WordPress website.

This plugin gives you the ability to add eHive search functionality to your website. When added to your site the eHive Search plugin allows your site's visitors to search for eHive Objects. Search results can be displayed as a list view, lightbox view or both. You can optionally add an account and/or community filter in the eHive Access plugin's options page so that your site returns Object Records from the given account and/or community only.

You can configure the search options to display your choice of fields for the search results summary.

Before you install this plugin you will need to install the eHive Access plugin. 

<span style="text-decoration: underline;">**Get more from the eHive plugin suite**</span>

To enhance the experience you offer your users you can also install the eHive Object details plugin to allow your users to click all the way through to view the Object Records in detail. Furthermore, you can add the eHive Search widget and give your visitors the option to search eHive on every page, not just the page where you have added the plugin's shortcode.

While eHive search will function and return results without the eHive Object Details plugin, it is likely that you will want to install the eHive Object Details plugin so your users can click through and view an Object Record in detail.

Other plugins in the suite include:

* eHive Account Details - A plugin for displaying eHive account information.
* eHive Object Comments - Enables users to add comments to Object Records from your site.
* eHive Object Details - A plugin for displaying Object Record detail pages.
* eHive Objects Image Grid - Displays a grid of images from eHive filtered by certain criteria.
* eHive Objects Tag Cloud - Displays a tag cloud from eHive.
* eHive Objects Gallery widget - Provides a gallery of Object Records that can be placed in your sites widget areas. 
* eHive Object Tags widget - A widget that displays tags for an Object Record.
* eHive Objects Tag Cloud widget - Allows you to display a tag cloud in a widget area on your site.
* eHive Search widget - A widget plugin that provides access to eHive Search from a widget.

<div>
  <br />
</div>


== Installation ==
**Dependencies:**

* eHive Access plugin
* eHive Object Details plugin (optional but strongly advised)

This plugin requires the eHive Access plugin to be installed first. Please ensure you have installed and configured these plugins correctly first before installing this plugin.

There are three ways to install a plugin:

<span style="text-decoration: underline;">**Method 1**</span>


1.  Navigate to the plugins page by clicking the link in the WordPress admin menu.
2.  Click the "Add New" link
3.  Type the name of the plugin you want to install (i.e. "eHive Access plugin") into the search box
4.  Click the "Search plugins" button
5.  Locate the plugin you want to install from the search results
6.  Click the "Install Now" link and click "OK" on the popup confirmation window
7.  Click the "Activate plugin" link when the plugin installation has completed


<span style="text-decoration: underline;">**Method 2**</span>


1.  Download the plugin's .ZIP file.
2.  Navigate to the plugins page by clicking the link in the WordPress admin menu.
3.  Click the "Add New" link
4.  Click the "Upload" link
5.  Click the "Choose File" button and locate the .ZIP file you downloaded in step 1
6.  Click the "Install Now" button
7.  Click the "Activate plugin" link when the plugin installation has completed

<span style="text-decoration: underline;">**Method 3**</span>


1. Download the plugin's .ZIP file.
2. Unzip the contents into your WordPress sites plugin directory (<em>/wordpress/wp-contents/plugins</em>)
3. Navigate to the plugins page by clicking the link in the WordPress admin menu.
4. Click the "Activate plugin" link below the plugin's name

== Changelog ==
= 2.1.5 =
* Links to the fallback no image place holder changed to be relative to the plugin directory.
* Link to the powered by eHive image changed to be relative to the plugin directory.

= 2.1.4 =
* Bug fix, query string in search request encoded correctly for searches containing spaces and quotes.
* Added version control for plugin options. Defaulting of new options without changing existing options is now possible.
* Added uninstall script to remove options from the database when the plugin is deleted.  

= 2.1.3 =
* Forcing update of version number.

= 2.1.2 =
* Added option to search the private index for a Site type configured as Account.

= 2.1.1 =
* First stable release of the eHive Access plugin. 

== Upgrade Notice ==
= 2.1.5 =
* Links to the fallback no image place holder changed to be relative to the plugin directory.
* Link to the powered by eHive image changed to be relative to the plugin directory. 

= 2.1.4 =
* Bug fix, query string in search request encoded correctly for searches containing spaces and quotes.
* Added version control for plugin options. Defaulting of new options without changing existing options is now possible. 
* Added uninstall script to remove options from the database when the plugin is deleted. 

= 2.1.3 =
* Forcing update of version number.

= 2.1.2 =
* Added option to search the private index for a Site type configured as Account.

= 2.1.1 =
This is the first stable release of the eHive Access plugin.

== Screenshots ==

1. screenshot_1.png
2. screenshot_2.png

== Frequently Asked Questions ==

Q. What is eHive?

A. eHive is an online collections management software package. See more at <a href="http://ehive.com/what_is_ehive" target="_blank" title="what is ehive?">What is eHive?</a>

<div>
	<br />
</div>

Q. What do these plugins do?

A. The eHive plugin suite gives you the ability to provide eHive functionality to your site's visitors. This means that you can search and display eHive records, leave comments that are visible also in ehive and add and remove tags to records where the record owner has given permission to do so. You can filter the search results by account or community if you want to display only records from a particular source. We also provide plugins to do other nice things like display grids of interesting, popular or recent images added to eHive; display galleries of other objects by the same account as a record you are viewing etc.

<div>
	<br />
</div>

Q. How do I get an API Key?

A. To get an API Key you will first need an active eHive account. If you don't have one you can <a href="http://ehive.com/signup/" title="sign up for an ehive account">sign up</a> for an account for free. Once you have an account you can navigate to the "Edit My Profile > Api Keys" page and create a new Key.


