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

if (preg_match_all("/(ldap:\/\/|ldaps:\/\/)(.*)/", $hostname, $matches)) {
   $host = $matches[2][0];
} else {
   $host = $hostname;
}
if (fsockopen($host, $port_num, $errno, $errstr, 3)) {
   echo '<span style="color: green;" id="ldap_test_port_'.$authldaps_id.'">';
      echo '<i class="far fa-thumbs-up"></i>';
      echo $host." (TCP/".$port_num.")";
   echo '</span>';
} else {
   echo '<span style="color: red;" id="ldap_test_port_'.$authldaps_id.'">';
      echo '<i class="far fa-thumbs-down"></i>';
      echo $host." (TCP/".$port_num.")<br />";
      echo "$errstr ($errno)";
   echo '</span>';
}
