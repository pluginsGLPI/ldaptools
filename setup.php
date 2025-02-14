<?php

/**
 * -------------------------------------------------------------------------
 * ldaptools plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of ldaptools.
 *
 * ldaptools is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * ldaptools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ldaptools. If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @author    François Legastelois
 * @copyright Copyright (C) 2021-2022 by Teclib'.
 * @license   GPLv3 https://www.gnu.org/licenses/gpl-3.0.html
 * @link      https://services.glpi-network.com
 * -------------------------------------------------------------------------
 */

define('PLUGIN_LDAPTOOLS_VERSION', '0.1.0');

// Minimal GLPI version, inclusive
define('PLUGIN_LDAPTOOLS_MIN_GLPI', '10.0.0');
// Maximum GLPI version, exclusive
define('PLUGIN_LDAPTOOLS_MAX_GLPI', '11.0.0');

define('PLUGIN_LDAPTOOLS_ROOT', Plugin::getPhpDir('ldaptools'));

/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 * @return void
 */
function plugin_init_ldaptools()
{
    /** @var array $CFG_GLPI */
    /** @var array $PLUGIN_HOOKS */
    global $PLUGIN_HOOKS, $CFG_GLPI;

    $plugin = new Plugin();

    $PLUGIN_HOOKS['csrf_compliant']['ldaptools'] = true;

    if (
        Session::getLoginUserID()
         && $plugin->isActivated('ldaptools')
         && Session::haveRight('config', UPDATE)
    ) {
        $PLUGIN_HOOKS['config_page']['ldaptools']         = 'front/menu.php';
        $PLUGIN_HOOKS['menu_toadd']['ldaptools']['tools'] = 'PluginLdaptoolsMenu';
    }
}


/**
 * Get the name and the version of the plugin
 * REQUIRED
 *
 * @return array
 */
function plugin_version_ldaptools()
{
    return [
        'name'         => __('LDAP Tools', 'ldaptools'),
        'version'      => PLUGIN_LDAPTOOLS_VERSION,
        'author'       => '<a href="https://services.glpi-network.com">teclib\'</a>',
        'license'      => 'GPLv3',
        'homepage'     => 'https://services.glpi-network.com',
        'requirements' => [
            'glpi' => [
                'min' => PLUGIN_LDAPTOOLS_MIN_GLPI,
                'max' => PLUGIN_LDAPTOOLS_MAX_GLPI,
            ],
        ],
    ];
}
