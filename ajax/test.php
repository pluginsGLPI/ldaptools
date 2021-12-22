<?php

/**
 *  --------------------------------------------------------------------------
 *  LICENSE
 *
 *  This file is part of ldaptools.
 *
 *  ldaptools is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  ldaptools is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  --------------------------------------------------------------------------
 *  @author    François Legastelois
 *  @copyright Copyright (C) 2021-2022 by Teclib'.
 *  @license   GPLv3 https://www.gnu.org/licenses/gpl-3.0.html
 *  @link      https://services.glpi-network.com
 *  -------------------------------------------------------------------------
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
$port_num      = intval($AuthLDAP->getField('port'));
$username      = $AuthLDAP->getField('rootdn');
$password      = Toolbox::sodiumDecrypt($AuthLDAP->getField('rootdn_passwd'));
$base_dn       = $AuthLDAP->getField('basedn');
$login_field   = $AuthLDAP->getField('login_field');
$use_tls       = $AuthLDAP->getField('use_tls');
$filter        = Html::entity_decode_deep($AuthLDAP->getField('condition'));
$search        = "(cn=*)";

$use_bind = true;
if ($AuthLDAP->isField('use_bind')) {
   $use_bind = $AuthLDAP->getField('use_bind');
}

$tls_certfile = NULL;
if ($AuthLDAP->isField('tls_certfile')) {
   $tls_certfile = $AuthLDAP->getField('tls_certfile');
}

$tls_keyfile = NULL;
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
   if (fsockopen($host, $port_num, $errno, $errstr, 5)) {
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
            echo __('BaseDN should not been empty!', 'ldaptools');
         echo '</span>';
         $next = false;
      } else {
         echo '<span style="color: green;" id="ldap_test_basedn_'.$authldaps_id.'">';
            echo '<i class="far fa-thumbs-up"></i>';
         echo '</span>';
         $next = true;
      }
   } else {
      echo '<span style="color: red;" id="ldap_test_basedn_'.$authldaps_id.'">';
         echo '<i class="far fa-hand-point-left"></i>';
         echo __('Fix previous!', 'ldaptools');
      echo '</span>';
      $next = false;
   }
   echo "</td>";

   echo "<td>";
   if ($next) {

      if ($ldap = ldap_connect($hostname, $port_num)) {

         ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
         ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
         ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
         ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, 5);
         ldap_set_option($ldap, LDAP_OPT_TIMELIMIT, 10);
         ldap_set_option($ldap, LDAP_OPT_SIZELIMIT, 50);

         if (!empty($tls_certfile) && file_exists($tls_certfile)) {
            ldap_set_option(null, LDAP_OPT_X_TLS_CERTFILE, $tls_certfile);
         }

         if (!empty($tls_keyfile) && file_exists($tls_keyfile)) {
            ldap_set_option(null, LDAP_OPT_X_TLS_KEYFILE, $tls_keyfile);
         }

         if ($use_tls) {
            @ldap_start_tls($ldap);
         }

         echo '<span style="color: green;" id="ldap_test_connect_'.$authldaps_id.'">';
            echo '<i class="far fa-thumbs-up"></i>';
         echo '</span>';
         $next = true;
      } else {
         echo '<span style="color: red;" id="ldap_test_connect_'.$authldaps_id.'">';
            //TRANS: %s is the LDAP error number
            $toolTip = sprintf(__('Error number: %s', 'ldaptools'), ldap_errno($ldap));
            //TRANS: %s is the LDAP error message
            $toolTip.= sprintf(__('Error message: %s', 'ldaptools'), ldap_err2str(ldap_errno($ldap)));
            Html::showToolTip($toolTip);
         echo '</span>';
         $next = false;
      }
   } else {
      echo '<span style="color: red;" id="ldap_test_connect_'.$authldaps_id.'">';
         echo '<i class="far fa-hand-point-left"></i>';
         echo __('Fix previous!', 'ldaptools');
      echo '</span>';
      $next = false;
   }
   echo "</td>";

   echo "<td>";
   if ($next && $use_bind) {
      $bind_result = ldap_bind($ldap, $username, $password);
      if (!$bind_result) {
         echo '<span style="color: red;" id="ldap_test_bind_'.$authldaps_id.'">';
            echo __('Could not bind', 'ldaptools');
            //TRANS: %s is the LDAP error number
            $toolTip = sprintf(__('Error number: %s', 'ldaptools'), ldap_errno($ldap));
            //TRANS: %s is the LDAP error message
            $toolTip.= sprintf(__('Error message: %s', 'ldaptools'), ldap_err2str(ldap_errno($ldap)));
            Html::showToolTip($toolTip);
         echo '</span>';
         $next = false;
      } else {
         echo '<span style="color: green;" id="ldap_test_bind_'.$authldaps_id.'">';
            echo '<i class="far fa-thumbs-up"></i>';
         echo '</span>';
         $next = true;
      }
   } else {
      if ($use_bind) {
         echo '<span style="color: red;" id="ldap_test_bind_'.$authldaps_id.'">';
            echo '<i class="far fa-hand-point-left"></i>';
            echo __('Fix previous!', 'ldaptools');
         echo '</span>';
         $next = false;
      } else {
         echo '<span style="color: orange;" id="ldap_test_bind_'.$authldaps_id.'">';
            echo "Disabled";
            Html::showToolTip(
               __("Bind user / password authentication is disabled,
               which means your LDAP server allows anonymous requests,
               or authenticates with a key. If it's voluntary, don't worry.<br />
               If the following tests are in error, you should check this authentication!
               Maybe it's ultimately necessary ;)", 'ldaptools')
            );
         echo '</span>';
      }
   }
   echo "</td>";

   echo "<td>";
   if ($next) {
      $results = @ldap_search($ldap, $base_dn, $search, [], 0, 50);
      if (!$results) {
         echo '<span style="color: red;" id="ldap_test_search_'.$authldaps_id.'">';
            //TRANS: %s is the LDAP error number
            $toolTip = sprintf(__('Error number: %s', 'ldaptools'), ldap_errno($ldap));
            //TRANS: %s is the LDAP error message
            $toolTip.= sprintf(__('Error message: %s', 'ldaptools'), ldap_err2str(ldap_errno($ldap)));
            Html::showToolTip($toolTip);
            //TRANS: %s is the search content
            echo sprintf(__('Search error: %s', 'ldaptools'), $search);
         echo '</span>';
         $next = false;
      } else {
         $count_entries = ldap_count_entries($ldap, $results);
         if ($count_entries > 0) {
            echo '<span style="color: green;" id="ldap_test_filter_'.$authldaps_id.'">';
               echo '<i class="far fa-thumbs-up"></i>';
               echo $count_entries." entries";
               $entriesToolTip = '<span style="font-weight:bold;">First entry</span><br />';
               $firtEntry = ldap_first_entry($ldap, $results);
               $entriesToolTip .= ldap_get_dn($ldap, $firtEntry);
               Html::showToolTip($entriesToolTip);
            echo '</span>';
            $next = true;
         } else {
            echo '<span style="color: red;" id="ldap_test_filter_'.$authldaps_id.'">';
               echo '<i class="far fa-thumbs-down"></i>';
               //TRANS: %s is the LDAP search content
               echo sprintf(__('No entry found: %s', 'ldaptools'), $search);
            echo '</span>';
            $next = false;
         }
      }
   } else {
      echo '<span style="color: red;" id="ldap_test_search_'.$authldaps_id.'">';
         echo '<i class="far fa-hand-point-left"></i>';
         echo __('Fix previous!', 'ldaptools');
      echo '</span>';
      $next = false;
   }
   echo "</td>";

   ldap_free_result($results);

   echo "<td>";
   if ($next) {

      if (empty($filter)) {
         $filter = $search;
      }

      $results = @ldap_search($ldap, $base_dn, $filter, [], 0, 50);
      if (!$results) {
         echo '<span style="color: red;" id="ldap_test_filter_'.$authldaps_id.'">';
            //TRANS: %s is the LDAP error number
            $toolTip = sprintf(__('Error number: %s', 'ldaptools'), ldap_errno($ldap));
            //TRANS: %s is the LDAP error message
            $toolTip.= sprintf(__('Error message: %s', 'ldaptools'), ldap_err2str(ldap_errno($ldap)));
            Html::showToolTip($toolTip);
            //TRANS: %s is the LDAP search filter content
            echo sprintf(__('Filter error: %s', 'ldaptools'), $filter);
         echo '</span>';
         $next = false;
      } else {
         $entries = ldap_get_entries($ldap, $results);
         if ($count_entries > 0) {
            echo '<span style="color: green;" id="ldap_test_filter_'.$authldaps_id.'">';
               echo '<i class="far fa-thumbs-up"></i>';
               //TRANS: %s is the LDAP count entries returned
               echo sprintf(__('%s entries', 'ldaptools'), $count_entries);
               $entriesToolTip = '<span style="font-weight:bold;">'.__("First entry", "ldaptools").'</span><br />';
               $firtEntry = ldap_first_entry($ldap, $results);
               $entriesToolTip .= ldap_get_dn($ldap, $firtEntry);
               Html::showToolTip($entriesToolTip);
            echo '</span>';
            $next = true;
         } else {
            echo '<span style="color: red;" id="ldap_test_filter_'.$authldaps_id.'">';
               echo '<i class="far fa-thumbs-down"></i>';
               //TRANS: %s is the LDAP search filter content
               echo sprintf(__('No entry found: %s', 'ldaptools'), $filter);
            echo '</span>';
            $next = false;
         }
      }
   } else {
      echo '<span style="color: red;" id="ldap_test_filter_'.$authldaps_id.'">';
         echo '<i class="far fa-hand-point-left"></i>';
         echo __('Fix previous!', 'ldaptools');
      echo '</span>';
      $next = false;
   }
   echo "</td>";

   echo "<td>";
   if ($next) {
      $first = ldap_first_entry($ldap, $results);
      $attrs = ldap_get_attributes($ldap, $first);
      if (!$attrs) {
         echo '<span style="color: red;" id="ldap_test_attributes_'.$authldaps_id.'">';
            //TRANS: %s is the LDAP error number
            $toolTip = sprintf(__('Error number: %s'), ldap_errno($ldap));
            //TRANS: %s is the LDAP error message
            $toolTip.= sprintf(__('Error message: %s'), ldap_err2str(ldap_errno($ldap)));
            Html::showToolTip($toolTip);
            echo __('Get attributes error', 'ldaptools');
         echo '</span>';
         $next = false;
      } else {
         echo '<span style="color: green;" id="ldap_test_attributes_'.$authldaps_id.'">';
            echo '<i class="far fa-thumbs-up"></i>';
            $attrsToolTip = '<span style="font-weight:bold;">'.__("Available attributes", "ldaptools").'</span><br />';
            for ($i=0; $i < $attrs["count"]; $i++) {
               $attrsToolTip .= $attrs[$i] . "<br />";
            }
            Html::showToolTip($attrsToolTip);
         echo '</span>';
         $next = true;
      }
   } else {
      echo '<span style="color: red;" id="ldap_test_attributes_'.$authldaps_id.'">';
         echo '<i class="far fa-hand-point-left"></i>';
         echo __('Fix previous!', 'ldaptools');
      echo '</span>';
      $next = false;
   }
   echo "</td>";
echo "</tr>";

ldap_free_result($results);
