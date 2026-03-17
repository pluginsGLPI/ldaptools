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
 * @copyright Copyright (C) 2021-2024 by Teclib'.
 * @license   GPLv3 https://www.gnu.org/licenses/gpl-3.0.html
 * @link      https://github.com/pluginsGLPI/ldaptools
 * -------------------------------------------------------------------------
 */

namespace GlpiPlugin\Ldaptools;

use CommonGLPI;
use Config;
use Plugin;
use Session;
use Toolbox;
use Glpi\Application\View\TemplateRenderer;

class Menu extends CommonGLPI
{
    public static $rightname = 'config';

    public static function getTypeName($nb = 0): string
    {
        return __('LDAP Tools', 'ldaptools');
    }

    public static function getMenuName(): string
    {
        return __('LDAP Tools', 'ldaptools');
    }

    public static function canView(): bool
    {
        return Config::canView();
    }

    public static function canUpdate(): bool
    {
        return Config::canUpdate();
    }

    public static function getIcon(): string
    {
        return 'ti ti-plug-connected';
    }

    public static function getMenuContent(): array|false
    {
        $menu = [];

        $base_dir = '/' . Plugin::getWebDir('ldaptools', false);

        if (static::canUpdate()) {
            $menu['title'] = static::getMenuName();
            $menu['page']  = "$base_dir/front/menu.php";
            $menu['icon']  = static::getIcon();

            $menu['options']['test'] = [
                'title' => LdapTest::getTypeName(Session::getPluralNumber()),
                'page'  => "$base_dir/front/test.php",
                'icon'  => LdapTest::getIcon(),
                'links' => [],
            ];
        }

        if (count($menu)) {
            return $menu;
        }

        return false;
    }

    public static function showCentralPage(): void
    {
        $tools = [];

        if (Toolbox::canUseLdap()) {
            // Absolute path required — relative 'front/test.php' would resolve to
            // /plugins/ldaptools/front/front/test.php when current URL is /front/menu.php
            $base_dir = '/' . Plugin::getWebDir('ldaptools', false);
            $tools[] = [
                'link'    => $base_dir . '/front/test.php',
                'name'    => LdapTest::getTypeName(),
                'comment' => LdapTest::getComment(),
                'icon'    => LdapTest::getIcon(),
            ];
        }

        TemplateRenderer::getInstance()->display('@ldaptools/menu.html.twig', [
            'can_use' => Toolbox::canUseLdap(),
            'tools'   => $tools,
        ]);
    }
}
