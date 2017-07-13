=== Gridgets ===
Contributors: Catapult_Themes
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2FEVLGX5Q4CVE
Tags: builder, grid, layout, page, page builder, widget, widgets, customizer, customizer builder, builder, content composer, layout builder, drag and drop builder, frontend builder, frontend editor, layout builder plugin, template Builder, theme builder, visual builder, visual composer, visual editor, website builder
Requires at least: 4.4
Tested up to: 4.6.1
Stable tag: 1.2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Grids for widgets

== Description ==

Gridgets is the only native WordPress way to build pages in the style of Visual Composer, SiteOrigin Page Builder and others. Create widget areas for pages, assign a grid layout, and drop widgets in. Standard WordPress functionality to create engaging page layouts.

Take a look at [the demo](http://getgridgets.com/).

Advantages include:

* Use any WordPress widget as content within your grid
* No third party interfaces
* Inherits your theme styles
* No shortcodes littering your content if you decide to switch it off
* Drag and drop
* Edit and view your content in the Customizer
* Maintains sidebars even when you switch themes
* Fully extendible by anyone who can build a widget
* Easier for you; easier for your clients

= Demo =

[Demo](http://getgridgets.com/).

= How does it work? =

The Gridgets plugin allows you to create widget areas specific to individual pages.

You can define styles for each widget area, such as a column layout, background color or image, padding and margins. You can then add widgets, just as you would any standard widget area. Gridgets will order the widgets within the widget area according to the layout - if you've got two widgets in a two-column layout, Gridgets will place those widgets next to each other in two equally sized columns. You can re-order the widgets within each widget area just by dragging and dropping - standard widget behaviour.

And because this is all standard WordPress behaviour, you can preview your Gridgets in the Customizer before making any changes live. We think this makes Gridgets the only live, WYSIWYG page builder that uses only standard WordPress behaviour and functionality.

[youtube https://www.youtube.com/watch?v=yxa4IKc4wCU]

== Installation ==

= Automatic installation =

Log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type "Gridgets" and click Search Plugins. Install the plugin by simply clicking "Install Now".


== Frequently Asked Questions ==

= Where can I find the documentation? =

All the documentation is [here](http://getgridgets.com/documentation/).

= What happens when I switch themes? =

Currently, all the widget areas you've created using Gridgets will still be registered. However, their widgets will be 'inactive'. This means you can retrieve widgets you used with your previous theme and add them back to your sidebars. Just go to Appearance > Widgets in your dashboard and drag the inactive widgets to the sidebars.

= What themes can I use this on? =

You can use this plugin on any theme. It inherits the theme's styles without imposing any of its own (apart from one or two classes for layouts). However, some themes are better suited than others for the kind of page-builder style layouts that you might want to produce. For instance, we suggest you might want to use a theme with a page template that will allow you a single column layout with no sidebar.

Please note that some custom styling might be necessary on your theme. If you see any discrepancies or have any questions, just post a thread on the support forum. We're happy to help out and your feedback will help us develop the plugin in the future.

= I deleted a widget area but I can still see its widgets =

Sometimes, if you delete a widget area WordPress will try to assign its widgets to another widget area. Have a look at adjacent widget areas to track down any widgets.


== Screenshots ==

1. Example page layout using Gridgets
1. View of page in Customizer Panel


== Changelog ==

= 1.2.2 - 20/11/2016 =

* Fixed: syntax error in delete_gridget

= 1.2.1 - 5/10/2016 =

* Added: new layout options
* Fixed: added :not selector to maintain padding in full width grids
* Fixed: syntax error with missing $post ID
* Fixed: incorrect single column

= 1.2.0 - 5/10/2016 =

* Added: drag and drop widget areas
* Added: border styles on widget areas
* Fixed: padding on nested container elements

= 1.1.0 - 30/09/2016 =

* Added: optional content width setting

= 1.0.0 - 24/09/2016 =

* Initial commit


== Upgrade Notice ==

Nothing here
