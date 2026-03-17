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
 * @copyright Copyright (C) 2021-2024 by Teclib'.
 * @license   GPLv3 https://www.gnu.org/licenses/gpl-3.0.html
 * @link      https://github.com/pluginsGLPI/ldaptools
 * -------------------------------------------------------------------------
 */

define('PLUGIN_LDAPTOOLS_VERSION', '1.0.0');

// Minimal GLPI version, inclusive
define('PLUGIN_LDAPTOOLS_MIN_GLPI', '11.0.0');
// Maximum GLPI version, exclusive
define('PLUGIN_LDAPTOOLS_MAX_GLPI', '11.0.99');

define('PLUGIN_LDAPTOOLS_ROOT', Plugin::getPhpDir('ldaptools'));

/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 * @return void
 */
function plugin_init_ldaptools(): void
{
    /** @var array $PLUGIN_HOOKS */
    global $PLUGIN_HOOKS;

    // Register legacy class aliases so GLPI's itemtype resolver (Html::header,
    // getMenuContent) can find 'PluginLdaptoolsMenu' / 'PluginLdaptoolsTest'.
    // The inc/ files are NOT autoloaded for PSR-4 plugins, so aliases must be
    // registered here, at init time, before any itemtype resolution occurs.
    if (!class_exists('PluginLdaptoolsMenu', false)) {
        class_alias(GlpiPlugin\Ldaptools\Menu::class, 'PluginLdaptoolsMenu');
    }
    if (!class_exists('PluginLdaptoolsTest', false)) {
        class_alias(GlpiPlugin\Ldaptools\LdapTest::class, 'PluginLdaptoolsTest');
    }

    $PLUGIN_HOOKS[Glpi\Plugin\Hooks::CSRF_COMPLIANT]['ldaptools'] = true;

    $plugin = new Plugin();

    if (
        Session::getLoginUserID()
        && $plugin->isActivated('ldaptools')
        && Session::haveRight('config', UPDATE)
    ) {
        $PLUGIN_HOOKS[Glpi\Plugin\Hooks::CONFIG_PAGE]['ldaptools']          = 'front/menu.php';
        // Use the legacy class alias string — GLPI's itemtype resolver does not fully support
        // FQDN namespaced plugin classes for menu registration.
        $PLUGIN_HOOKS[Glpi\Plugin\Hooks::MENU_TOADD]['ldaptools']['tools']  = 'PluginLdaptoolsMenu';
    }
}


/**
 * Get the name and the version of the plugin
 * REQUIRED
 *
 * @return array
 */
function plugin_version_ldaptools(): array
{
    return [
        'name'         => __('LDAP Tools', 'ldaptools'),
        'version'      => PLUGIN_LDAPTOOLS_VERSION,
        'author'       => '<a href="https://services.glpi-network.com">teclib\'</a>',
        'license'      => 'GPLv3',
        'homepage'     => 'https://github.com/pluginsglpi/ldaptools',
        'requirements' => [
            'glpi' => [
                'min' => PLUGIN_LDAPTOOLS_MIN_GLPI,
                'max' => PLUGIN_LDAPTOOLS_MAX_GLPI,
            ],
            'php'  => [
                'min' => '8.1',
            ],
        ],
    ];
}
