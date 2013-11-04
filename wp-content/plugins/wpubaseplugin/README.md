Base Plugin
=================

A Framework to create your WordPress plugin.

How to start a new plugin with it :
---

* Choose a plugin id and a plugin name.
* Change the plugin name :
 * Into the plugin header (comment at the top).
 * Into the set_options() function.
* Change the id :
 * The plugin folder name.
 * The plugin main file name.
 * Into the set_options() function.
 * Into uninstall.php
 * The lang .po & .mo files (wpubaseplugin-fr_FR.mo -> myawesomeplugin-fr_FR.mo)
* Change the PHP class name (use the id with camelcase if you are not inspired)
 * Into the class declaration ( class wpuBasePlugin extends wpuBasePluginUtilities -> class myAwesomePlugin extends wpuBasePluginUtilities )
 * Into the class launch ( $wpuBasePlugin = new wpuBasePlugin(); -> $myAwesomePlugin = new myAwesomePlugin(); )
 * Into the activation & deactivation calls.
 * Into uninstall.php

What can you do with it ?
---

* You can create admin pages.
* You can create paginated tables in admin pages.
* ... and more !