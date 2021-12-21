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

class PluginLdaptoolsTest extends CommonGLPI {

   static $rightname = 'config';

   public static function getTypeName($nb = 0) {
      return __('LDAP Test', 'ldaptools');
   }

   public static function getMenuName() {
      return __('LDAP Test', 'ldaptools');
   }

   public static function getLink() {
      return 'test.php';
   }

   public static function getComment() {
      return __('Performs various tests on all the LDAP directories declared in GLPI.', 'ldaptools');
   }

   public static function canView() {
      return Config::canView();
   }

   public static function canUpdate() {
      return Config::canUpdate();
   }

   public static function getIcon() {
      return "fas fa-sign-in-alt";
   }

   public static function show() {

      if ($_SESSION['glpi_use_mode'] == Session::DEBUG_MODE) {
         $_SESSION['glpi_use_mode'] = Session::NORMAL_MODE;
      }

      Session::checkRight("config", UPDATE);

      echo "<div class='center'>";
         echo "<table border='0' class='tab_cadrehov'>";
            echo "<thead>";
               echo "<tr class='tab_bg_2'>";
                  echo "<th>".AuthLDAP::getTypeName()."</th>";
                  echo "<th>".__('TCP port', 'ldaptools')."</th>";
                  echo "<th>".__('BaseDN', 'ldaptools')."</th>";
                  echo "<th>".__('LDAP connect', 'ldaptools')."</th>";
                  echo "<th>".__('Bind auth', 'ldaptools')."</th>";
                  echo "<th>".__('Generic search', 'ldaptools')."</th>";
                  echo "<th>".__('Filtered search', 'ldaptools')."</th>";
               echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            foreach (AuthLDAP::getLdapServers() as $ldapServer) {

               $AuthLDAP = new AuthLDAP();
               $AuthLDAP->getFromDB($ldapServer['id']);

               echo '<tr id="ldap_test_'.$ldapServer['id'].'">';
               echo '<td colspan="6"><i class="fas fa-spinner fa-pulse"></i></td>';
               $ajax_url = Plugin::getWebDir('ldaptools')."/ajax/test.all.php";
               echo Html::scriptBlock('
                  $(document).ready(function() {
                     $.ajax({
                        type: "GET",
                        url: "'.$ajax_url.'",
                        data: {
                           authldaps_id: "'.$ldapServer['id'].'"
                        },
                        success: function(data){
                           $("[id=ldap_test_'.$ldapServer['id'].']").replaceWith(data);
                        },
                     });
                  });
               ');
               echo "</tr>";
            }

            echo "</tbody>";
         echo "</table>";
      echo "</div>";
   }
}
