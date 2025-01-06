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

class PluginLdaptoolsMenu extends CommonGLPI
{
    public static $rightname = 'config';

    public static function getTypeName($nb = 0)
    {
        return __('LDAP Tools', 'ldaptools');
    }

    public static function getMenuName()
    {
        return __('LDAP Tools', 'ldaptools');
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
        return "fas fa-sign-in-alt";
    }

    public static function getMenuContent()
    {
        $menu = [];

        $base_dir = '/' . Plugin::getWebDir('ldaptools', false);

        if (PluginLdaptoolsMenu::canUpdate()) {
            $menu['title'] = self::getMenuName();
            $menu['page']  = "$base_dir/front/menu.php";
            $menu['icon']  = self::getIcon();

            $link_text = "<span class='d-none d-xxl-block'>" .
                      PluginLdaptoolsMenu::getTypeName(Session::getPluralNumber()) .
                      "</span>";
            $links =  [
                "<i class='" . PluginLdaptoolsMenu::getIcon() . "'></i>$link_text"
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

    public static function showCentralPage()
    {
        $filepaths = [];
        if (Toolbox::canUseLdap()) {
            foreach (glob(PLUGIN_LDAPTOOLS_ROOT . '/inc/*') as $filepath) {
                if (preg_match("/inc.(.+)\.class.php/", $filepath, $matches)) {
                    $filepaths[$filepath] = [];
                    $classname = 'PluginLdaptools' . ucfirst($matches[1]);
                    if (method_exists($classname, 'getLink')) {
                        $filepaths[$filepath]['link'] = $classname::getLink();
                        $filepaths[$filepath]['name'] = $classname::getTypeName();
                        if (method_exists($classname, 'getComment')) {
                            $filepaths[$filepath]['comment'] = $classname::getComment();
                        } else {
                            $filepaths[$filepath]['comment'] = __('No comments');
                        }
                    }
                }
            }
        }
        TemplateRenderer::getInstance()->display('@ldaptools/menu.html.twig', [
            'can_use' => Toolbox::canUseLdap(),
            'filepaths' => $filepaths
        ]);
    }
}
