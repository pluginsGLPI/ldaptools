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

include ("../../../inc/includes.php");

Session::checkRight("config", UPDATE);

Html::header(__('LDAP Tools', 'ldaptools'),
   $_SERVER['PHP_SELF'],
   'tools',
   'PluginLdaptoolsMenu',
   'menu'
);

if (!Toolbox::canUseLdap()) {
   echo "<div class='center warning' style='width: 40%; margin: auto;'>";
   echo "<i class='fa fa-exclamation-triangle fa-3x'></i>";
   echo "<p>"._x('error', __("The LDAP extension of your PHP parser isn't installed"))."</p>";
   echo "</div>";
} else {
   echo "<div class='center'>";
      echo "<table class='tab_cadre'>";
         echo "<thead>";
            echo "<th>".__('LDAP Tools', 'ldaptools')."</th>";
            echo "<th>".__('Comment')."</th>";
         echo "</thead>";
         echo "<tbody>";

         foreach (glob(PLUGIN_LDAPTOOLS_ROOT.'/inc/*') as $filepath) {
            if (preg_match("/inc.(.+)\.class.php/", $filepath, $matches)) {
               $classname = 'PluginLdaptools' . ucfirst($matches[1]);
               if (method_exists($classname, 'getLink')) {
                  echo "<tr>";
                  echo "<td>".Html::link(_x('action', $classname::getTypeName(), 'ldaptools'),
                                 $classname::getLink())."</td>";
                  if (method_exists($classname, 'getComment')) {
                     echo "<td>".$classname::getComment()."</td>";
                  } else {
                     echo "<td>".__('No comments')."</td>";
                  }
                  echo "</tr>";
               }
            }
         }

         echo "</tbody>";
      echo "</table>";
   echo "</div>";
}

Html::footer();
