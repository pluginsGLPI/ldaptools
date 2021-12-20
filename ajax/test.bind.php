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
$hostname = $AuthLDAP->getField('host');
$port_num = $AuthLDAP->getField('port');
$username = $AuthLDAP->getField('rootdn');
$password = Toolbox::sodiumDecrypt($AuthLDAP->getField('rootdn_passwd'));

$ldap = ldap_connect($hostname, $port_num);

ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, 1);
ldap_set_option($ldap, LDAP_OPT_TIMELIMIT, 1);
ldap_set_option($ldap, LDAP_OPT_SIZELIMIT, 1);

$bind_result = ldap_bind($ldap, $username, $password);

if (empty($username)) {
   $username = "Empty username";
}

if (!$bind_result) {
   echo '<span style="color: red;" id="ldap_test_bind_'.$authldaps_id.'">';
      echo '<i class="far fa-thumbs-down"></i>';
      echo "Bind ERROR: ".$username."<br />";
      echo ldap_error($ldap);
   echo '</span>';
} else {
   echo '<span style="color: green;" id="ldap_test_bind_'.$authldaps_id.'">';
      echo '<i class="far fa-thumbs-up"></i>';
      echo "Bind OK: ".$username;
   echo '</span>';
}
