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

use Glpi\Application\View\TemplateRenderer;

class PluginLdaptoolsTest extends CommonGLPI
{
    public static $rightname = 'config';

    public static function getTypeName($nb = 0)
    {
        return __('LDAP Test', 'ldaptools');
    }

    public static function getMenuName()
    {
        return __('LDAP Test', 'ldaptools');
    }

    public static function getLink()
    {
        return 'test.php';
    }

    public static function getComment()
    {
        return __('Performs various tests on all the LDAP directories declared in GLPI.', 'ldaptools');
    }

    public static function canView()
    {
        return Config::canView();
    }

    public static function canUpdate()
    {
        return Config::canUpdate();
    }

    public static function getIcon()
    {
        return 'fas fa-bug';
    }

    public static function showResult()
    {
        $ldaps_map = array_map(function ($ldap_master) {
            return [
                'master'   => $ldap_master,
                'replicat' => AuthLDAP::getAllReplicateForAMaster($ldap_master['id']),
            ];
        }, AuthLDAP::getLdapServers());

        TemplateRenderer::getInstance()->display('@ldaptools/test.html.twig', [
            'ldap'         => self::class,
            'ldap_servers' => $ldaps_map,
        ]);
    }
}
