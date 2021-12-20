<?php
/**
 * --------------------------------------------------------------------------
 * LICENSE
 *
 * This file is part of ldaptools.
 *
 * ldaptools is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * ldaptools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * --------------------------------------------------------------------------
 * @author    François Legastelois
 * @copyright Copyright (C) 2021-2022 by Teclib'.
 * @license   GPLv3 https://www.gnu.org/licenses/gpl-3.0.html
 * @link      https://github.com/pluginsGLPI/ldaptools/
 * -------------------------------------------------------------------------
 */

define('PLUGIN_LDAPTOOLS_VERSION', '1.0.0');

// Minimal GLPI version, inclusive
define('PLUGIN_LDAPTOOLS_MIN_GLPI', '9.5.0');
// Maximum GLPI version, exclusive
define('PLUGIN_LDAPTOOLS_MAX_GLPI', '11.0.0');

define('PLUGIN_LDAPTOOLS_ROOT', Plugin::getPhpDir('ldaptools'));

/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 * @return void
 */
function plugin_init_ldaptools() {

   global $PLUGIN_HOOKS, $CFG_GLPI;

   $plugin = new Plugin();

   $PLUGIN_HOOKS['csrf_compliant']['ldaptools'] = true;

   if (Session::getLoginUserID()
         && $plugin->isActivated('ldaptools')
         && Session::haveRight("config", UPDATE)) {

      $PLUGIN_HOOKS['config_page']['ldaptools'] = 'front/menu.php';
      $PLUGIN_HOOKS['menu_toadd']['ldaptools']['config'] = 'PluginLdaptoolsMenu';
   }
}


/**
 * Get the name and the version of the plugin
 * REQUIRED
 *
 * @return array
 */
function plugin_version_ldaptools() {
   return [
      'name'           => __('LDAP Tools', 'ldaptools'),
      'version'        => PLUGIN_LDAPTOOLS_VERSION,
      'author'         => '<a href="http://www.teclib.com">teclib\'</a>',
      'license'        => 'GPLv3',
      'homepage'       => 'https://github.com/pluginsGLPI/ldaptools',
      'requirements'   => [
         'glpi' => [
            'min' => PLUGIN_LDAPTOOLS_MIN_GLPI,
            'max' => PLUGIN_LDAPTOOLS_MAX_GLPI,
         ]
      ]
   ];
}
