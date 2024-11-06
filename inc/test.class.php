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
        return "fas fa-bug";
    }

    public static function show()
    {
        echo "<div class='center'>";
         echo "<table border='0' class='tab_cadrehov'>";
            echo "<thead>";
               echo "<tr class='tab_bg_2'>";
                  echo "<th>" . AuthLDAP::getTypeName() . "</th>";
                  echo "<th>" . __('TCP stream', 'ldaptools') . "</th>";
                  echo "<th>" . __('BaseDN', 'ldaptools') . "</th>";
                  echo "<th>" . __('LDAP URI', 'ldaptools') . "</th>";
                  echo "<th>" . __('Bind auth', 'ldaptools') . "</th>";
                  echo "<th>";
                     echo __('Generic search', 'ldaptools');
                     Html::showToolTip(__('Forced limit : 50 entries max.', 'ldaptools'));
                  echo "</th>";
                  echo "<th>";
                     echo __('Filtered search', 'ldaptools');
                     Html::showToolTip(__('Forced limit : 50 entries max.', 'ldaptools'));
                  echo "</th>";
                  echo "<th>" . __('Attributes', 'ldaptools') . "</th>";
               echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

        $ldaps_map = array_map(function ($ldap_master) {
            return [
                "master" => $ldap_master,
                "replicat" => AuthLDAP::getAllReplicateForAMaster($ldap_master['id'])
            ];
        }, AuthLDAP::getLdapServers());

        foreach ($ldaps_map as $ldap_items) {
            $ldap_master = $ldap_items["master"];
            self::addRow($ldap_master);
            foreach ($ldap_items["replicat"] as $ldap_replicat) {
                self::addRow($ldap_master, $ldap_replicat);
            }
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    }

    private static function addRow(array $ldap_master, array $ldap_replicat = ["id" => 0])
    {
        $ajax_url = Plugin::getWebDir('ldaptools') . "/ajax/test.php";
        echo '<tr id="ldap_test_' . $ldap_master["id"] . '_' . $ldap_replicat["id"] . '">';
            echo '<td colspan="6"><i class="fas fa-spinner fa-pulse"></i></td>';
            echo Html::scriptBlock('
                $(document).ready(function() {
                    $.ajax({
                        type: "GET",
                        url: "' . $ajax_url . '",
                        data: {
                            authldaps_id: "' . $ldap_master["id"] . '",
                            authldapreplicates_id: "' . $ldap_replicat["id"] . '"
                        },
                        success: function(data){
                            $("[id=ldap_test_' . $ldap_master["id"] . '_' . $ldap_replicat["id"] . ']").replaceWith(data);
                        },
                    });
                });
            ');
        echo "</tr>";
    }
}
