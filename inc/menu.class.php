<?php
/*
 -------------------------------------------------------------------------
 GDPR Records of Processing Activities plugin for GLPI
 Copyright (C) 2020 by Yild.

 https://github.com/xdespujols/rgpd
 -------------------------------------------------------------------------

 LICENSE

 This file is part of GDPR Records of Processing Activities.

 GDPR Records of Processing Activities is free software; you can
 redistribute it and/or modify it under the terms of the
 GNU General Public License as published by the Free Software
 Foundation; either version 3 of the License, or (at your option)
 any later version.

 GDPR Records of Processing Activities is distributed in the hope that
 it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 See the GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GDPR Records of Processing Activities.
 If not, see <http://www.gnu.org/licenses/>.

 Based on DPO Register plugin, by Karhel Tmarr.

 --------------------------------------------------------------------------

  @package   rgpd
  @author    XDespujols
  @copyright Copyright (c) 2020 by Yild
  @license   GPLv3+
             http://www.gnu.org/licenses/gpl.txt
  @link      https://github.com/xdespujols/rgpd
  @since     2020
 --------------------------------------------------------------------------
 */

class PluginRgpdMenu extends CommonGLPI
{
   static $rightname = 'plugin_rgpd_record';

   static function getMenuName() {

      return PluginRgpdRecord::getTypeName(2);

   }

   static function getMenuContent() {

      $image = "<i class='fas fa-print fa-2x' title='" . __("Create PDF for all records within active entity and its sons", 'rgpd') . "'></i>";

      $menu = [];
      $menu['title'] = PluginRgpdMenu::getMenuName();
      $menu['page'] = '/plugins/rgpd/front/record.php';
      $menu['links']['search'] = PluginRgpdRecord::getSearchURL(false);
      $menu['links'][$image] = PluginRgpdCreatepdf::getSearchURL(false) . '?createpdf&action=prepare&type=' . PluginRgpdCreatePDF::REPORT_ALL;
      if (PluginRgpdRecord::canCreate()) {
         $menu['links']['add'] = PluginRgpdRecord::getFormURL(false);
      }

      $menu['options']['rgpd']['title'] = PluginRgpdMenu::getMenuName();
      $menu['options']['rgpd']['page'] = PluginRgpdRecord::getSearchURL(false);
      $menu['options']['rgpd']['links']['search'] = PluginRgpdRecord::getSearchURL(false);
      $menu['options']['rgpd']['links'][$image] = PluginRgpdCreatepdf::getSearchURL(false) . '?createpdf&action=prepare&type=' . PluginRgpdCreatePDF::REPORT_ALL;
      if (PluginRgpdRecord::canCreate()) {
         $menu['options']['rgpd']['links']['add'] = PluginRgpdRecord::getFormURL(false);
      }

      return $menu;
   }

   static function removeRightsFromSession() {

      if (isset($_SESSION['glpimenu']['admin']['types']['PluginRgpdMenu'])) {
         unset($_SESSION['glpimenu']['admin']['types']['PluginRgpdMenu']);
      }
      if (isset($_SESSION['glpimenu']['admin']['content']['PluginRgpdMenu'])) {
         unset($_SESSION['glpimenu']['admin']['content']['PluginRgpdMenu']);
      }

   }
}
