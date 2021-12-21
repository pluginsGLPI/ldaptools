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

class PluginLdaptoolsMenu extends CommonGLPI {

   static $rightname = 'config';

   public static function getTypeName($nb = 0) {
      return __('LDAP Tools', 'ldaptools');
   }

   public static function getMenuName() {
      return __('LDAP Tools', 'ldaptools');
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

   static function getMenuContent() {
      $menu = [];

      $base_dir = '/'.Plugin::getWebDir('ldaptools', false);

      if (PluginLdaptoolsMenu::canUpdate()) {
         $menu['title'] = self::getMenuName();
         $menu['page']  = "$base_dir/front/menu.php";
         $menu['icon']  = self::getIcon();

         $link_text = "<span class='d-none d-xxl-block'>".
                      PluginLdaptoolsMenu::getTypeName(Session::getPluralNumber()).
                      "</span>";
         $links =  [
            "<i class='".PluginLdaptoolsMenu::getIcon()."'></i>$link_text"
               => PluginLdaptoolsMenu::getSearchURL(false)
         ];

         $menu['options']['test'] = [
            'title' => PluginLdaptoolsTest::getTypeName(Session::getPluralNumber()),
            'page'  => "$base_dir/front/test.php",
            'icon'  => PluginLdaptoolsTest::getIcon(),
            'links' => []
         ];
      }

      if (count($menu)) {
         return $menu;
      }

      return false;
   }

}
