<?php
/*
 -------------------------------------------------------------------------
 GDPR Records of Processing Activities plugin for GLPI
 Copyright (C) 2020 by Yild.

 https://github.com/yild/gdprropa
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

  @package   gdprropa
  @author    Yild
  @copyright Copyright (c) 2020 by Yild
  @license   GPLv3+
             http://www.gnu.org/licenses/gpl.txt
  @link      https://github.com/yild/gdprropa
  @since     2020
 --------------------------------------------------------------------------
 */

define('PLUGIN_GDPRROPA_VERSION', '1.0.2');
define('PLUGIN_GDPRROPA_ROOT', __DIR__);

function plugin_init_gdprropa() {

   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['gdprropa'] = true;

   if (class_exists('Session') && method_exists('Session', 'getLoginUserID') && Session::getLoginUserID()) {

      if (class_exists('Plugin') && method_exists('Plugin', 'registerClass')) {
         Plugin::registerClass('PluginGdprropaProfile', ['addtabon' => ['Profile']]);
         Plugin::registerClass('PluginGdprropaRecord');
      }

      if (class_exists('PluginGdprropaProfile') && method_exists('PluginGdprropaProfile', 'initProfile')) {
         $PLUGIN_HOOKS['change_profile']['gdprropa'] = ['PluginGdprropaProfile', 'initProfile'];
      }

      $plugin = class_exists('Plugin') ? new Plugin() : null;
      if ($plugin instanceof Plugin
         && method_exists($plugin, 'isActivated')
         && method_exists('Session', 'haveRight')
         && !$plugin->isActivated('environment')
         && Session::haveRight('plugin_gdprropa_record', READ)) {
         $PLUGIN_HOOKS['menu_toadd']['gdprropa'] = ['management' => 'PluginGdprropaMenu'];
      }

      if (method_exists('Session', 'haveRight')
         && (Session::haveRight('plugin_gdprropa_record', UPDATE)
            || Session::haveRight('config', UPDATE))) {
         $PLUGIN_HOOKS['config_page']['gdprropa'] = 'front/config.form.php';
      }

      if (class_exists('Plugin') && method_exists('Plugin', 'registerClass')) {
         Plugin::registerClass('PluginGdprropaControllerInfo', ['addtabon' => ['Entity']]);
      }

      if (function_exists('plugin_gdprropa_postinit')) {
         $PLUGIN_HOOKS['post_init']['gdprropa'] = 'plugin_gdprropa_postinit';
      }
   }

}

function plugin_version_gdprropa() {

   return [
      'name' => __("GDPR Records of Processing Activities", 'gdprropa'),
      'version' => PLUGIN_GDPRROPA_VERSION,
      'author' => "<a href='https://github.com/yild/'>Yild</a>",
      'license' => 'GPLv3+',
      'homepage' => 'https://github.com/yild/gdprropa',
      'minGlpiVersion' => '10.0',
      'requirements'   => [
         'glpi' => [
            'min' => '10.0',
            'dev' => false
         ]
      ],
   ];
}

function plugin_gdprropa_check_prerequisites() {

   if (version_compare(GLPI_VERSION, '10.0', 'lt')) {
      if (method_exists('Plugin', 'messageIncompatible')) {
         echo Plugin::messageIncompatible('core', '10.0');
      } else {
         echo "This plugin requires GLPI >= 10.0";
      }
      return false;
   }

   return true;
}

function plugin_gdprropa_check_config($verbose = false) {

   if (true) {
      return true;
   }

   if ($verbose) {
      echo __("Installed / not configured");
   }

   return false;
}
