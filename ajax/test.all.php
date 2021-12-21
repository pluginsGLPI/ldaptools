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

$AJAX_INCLUDE = 1;
include ("../../../inc/includes.php");
header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();

Session::checkRight("config", UPDATE);

$authldaps_id = intval($_GET["authldaps_id"]) ?? 0;

if (empty($authldaps_id)) {
   http_response_code(400);
   Toolbox::logWarning("Missing parameter 'authldaps_id'.");
   die;
}

$AuthLDAP = new AuthLDAP();

if (!$AuthLDAP->can($authldaps_id, READ)) {
   http_response_code(403);
   Toolbox::logWarning("Missing rights to read AuthLDAP data.");
   die;
}

$AuthLDAP->getFromDB($authldaps_id);

$hostname      = $AuthLDAP->getField('host');
$port_num      = $AuthLDAP->getField('port');
$username      = $AuthLDAP->getField('rootdn');
$password      = Toolbox::sodiumDecrypt($AuthLDAP->getField('rootdn_passwd'));
$base_dn       = $AuthLDAP->getField('basedn');
$login_field   = $AuthLDAP->getField('login_field');

$use_bind = true;
if ($AuthLDAP->isField('use_bind')) {
   $use_bind = $AuthLDAP->getField('use_bind');
}

if ($AuthLDAP->isField('tls_certfile')) {
   $tls_certfile = $AuthLDAP->getField('tls_certfile');
}

if ($AuthLDAP->isField('tls_keyfile')) {
   $tls_keyfile = $AuthLDAP->getField('tls_keyfile');
}

$next = false;

echo '<tr id="ldap_test_'.$ldapServer['id'].'">';
   echo "<td>".$AuthLDAP->getLink()."</td>";

   echo "<td>";
   if (preg_match_all("/(ldap:\/\/|ldaps:\/\/)(.*)/", $hostname, $matches)) {
      $host = $matches[2][0];
   } else {
      $host = $hostname;
   }
   if (fsockopen($host, $port_num, $errno, $errstr, 1)) {
      echo '<span style="color: green;" id="ldap_test_port_'.$authldaps_id.'">';
         echo '<i class="far fa-thumbs-up"></i>';
         echo $host." (TCP/".$port_num.")";
      echo '</span>';
      $next = true;
   } else {
      echo '<span style="color: red;" id="ldap_test_port_'.$authldaps_id.'">';
         echo '<i class="far fa-thumbs-down"></i>';
         echo $host." (TCP/".$port_num.")<br />";
         echo "$errstr ($errno)";
      echo '</span>';
   }
   echo "</td>";

   echo "<td>";
   if ($next) {
      if (empty($base_dn)) {
         echo '<span style="color: red;" id="ldap_test_basedn_'.$authldaps_id.'">';
            echo '<i class="far fa-thumbs-down"></i>';
            echo "BaseDN ERROR: should not been empty!";
         echo '</span>';
      } else {
         echo '<span style="color: green;" id="ldap_test_basedn_'.$authldaps_id.'">';
            echo '<i class="far fa-thumbs-up"></i>';
            echo "BaseDN OK: ".$base_dn;
         echo '</span>';
         $next = true;
      }
   } else {
      echo '<span style="color: red;" id="ldap_test_basedn_'.$authldaps_id.'">';
         echo '<i class="far fa-thumbs-down"></i>';
         echo "Previous test error, please fix.";
      echo '</span>';
   }
   echo "</td>";

   echo "<td>";
   if ($next) {
      if ($ldap = ldap_connect($hostname, $port_num)) {

         
         ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
         ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
         ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
         ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, 1);
         ldap_set_option($ldap, LDAP_OPT_TIMELIMIT, 1);
         ldap_set_option($ldap, LDAP_OPT_SIZELIMIT, 1);

         if (!empty($tls_certfile) && file_exists($tls_certfile)) {
            ldap_set_option(null, LDAP_OPT_X_TLS_CERTFILE, $tls_certfile);
         }

         if (!empty($tls_keyfile) && file_exists($tls_keyfile)) {
            ldap_set_option(null, LDAP_OPT_X_TLS_KEYFILE, $tls_keyfile);
         }

         echo '<span style="color: green;" id="ldap_test_connect_'.$authldaps_id.'">';
          echo '<i class="far fa-thumbs-up"></i>';
          echo "Connect OK";
         echo '</span>';
         $next = true;
      } else {
         echo '<span style="color: red;" id="ldap_test_connect_'.$authldaps_id.'">';
            echo '<i class="far fa-thumbs-down"></i>';
            echo "Connect ERROR<br />";
            echo ldap_error($ldap);
         echo '</span>';
      }
   } else {
      echo '<span style="color: red;" id="ldap_test_connect_'.$authldaps_id.'">';
         echo '<i class="far fa-thumbs-down"></i>';
         echo "Previous test error, please fix.";
      echo '</span>';
   }
   echo "</td>";

   echo "<td>";
   if ($next && $use_bind) {
      $bind_result = ldap_bind($ldap, $username, $password);
      if (!$bind_result) {
         echo '<span style="color: red;" id="ldap_test_bind_'.$authldaps_id.'">';
            echo '<i class="far fa-thumbs-down"></i>';
            echo "Bind ERROR:<br />";
            echo ldap_error($ldap);
         echo '</span>';
      } else {
         echo '<span style="color: green;" id="ldap_test_bind_'.$authldaps_id.'">';
            echo '<i class="far fa-thumbs-up"></i>';
            echo "Bind OK";
         echo '</span>';
         $next = true;
      }
   } else {
      if ($use_bind) {
         echo '<span style="color: red;" id="ldap_test_bind_'.$authldaps_id.'">';
            echo '<i class="far fa-thumbs-down"></i>';
            echo "Previous test error, please fix.";
         echo '</span>';
      } else {
         echo '<span style="color: green;" id="ldap_test_bind_'.$authldaps_id.'">';
            echo '<i class="far fa-thumbs-down"></i>';
            echo "Bind is specifically disabled.";
         echo '</span>';
      }
   }
   echo "</td>";

   echo "<td>";
   if ($next) {
      $results = @ldap_search($ldap, $base_dn, '(cn=*)', [$login_field], 0, 1);
      if (!$results) {
         echo '<span style="color: red;" id="ldap_test_search_'.$authldaps_id.'">';
            echo '<i class="far fa-thumbs-down"></i>';
            echo "Could not search: ".$search."<br />";
            echo ldap_error($ldap);
         echo '</span>';
      } else {
         echo '<span style="color: green;" id="ldap_test_search_'.$authldaps_id.'">';
            echo '<i class="far fa-thumbs-up"></i>';
            echo "LDAP Search OK:<br />";
            $first = ldap_first_entry($ldap, $results);
            $data = ldap_get_dn($ldap, $first);
            echo "First entry: ".$data;
         echo '</span>';
      }
   } else {
      echo '<span style="color: red;" id="ldap_test_search_'.$authldaps_id.'">';
         echo '<i class="far fa-thumbs-down"></i>';
         echo "Previous test error, please fix.";
      echo '</span>';
   }
   echo "</td>";
echo "</tr>";
