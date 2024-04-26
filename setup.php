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

define('PLUGIN_RGPD_VERSION', '1.0');
define('PLUGIN_RGPD_ROOT', __DIR__);

function plugin_init_rgpd() {

   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['rgpd'] = true;

   if (Session::getLoginUserID()) {

      Plugin::registerClass('PluginRgpdProfile', ['addtabon' => ['Profile']]);
      Plugin::registerClass('PluginRgpdRecord');

      $PLUGIN_HOOKS['change_profile']['rgpd'] = ['PluginRgpdProfile', 'initProfile'];

      $plugin = new Plugin();
      if (!$plugin->isActivated('environment')
         && Session::haveRight('plugin_rgpd_record', READ)) {
         $PLUGIN_HOOKS['menu_toadd']['rgpd'] = ['management' => 'PluginRgpdMenu'];
      }

      if (Session::haveRight('plugin_rgpd_record', UPDATE)
         || Session::haveRight('config', UPDATE)) {
         $PLUGIN_HOOKS['config_page']['rgpd'] = 'front/config.form.php';
      }

      Plugin::registerClass('PluginRgpdControllerInfo', ['addtabon' => ['Entity']]);

      $PLUGIN_HOOKS['post_init']['rgpd'] = 'plugin_rgpd_postinit';
   }

}

function plugin_version_rgpd() {

   return [
      'name' => __("GDPR Records of Processing Activities", 'rgpd'),
      'version' => PLUGIN_RGPD_VERSION,
      'author' => "<a href='https://github.com/xdespujols/'>XDespujols</a>",
      'license' => 'GPLv3+',
      'homepage' => 'https://github.com/xdespujols/rgpd',
      'minGlpiVersion' => '9.4',
      'requirements'   => [
         'glpi' => [
            'min' => '10.0',
            'dev' => false
         ]
      ],
   ];
}

function plugin_rgpd_check_prerequisites() {

   if (version_compare(GLPI_VERSION, '10.0.5', 'lt')) {
      if (method_exists('Plugin', 'messageIncompatible')) {
         echo Plugin::messageIncompatible('core', '10.0');
      } else {
         echo "This plugin requires GLPI >= 10.0.5";
      }
      return false;
   }

   return true;
}

function plugin_rgpd_check_config($verbose = false) {

   if (true) {
      return true;
   }

   if ($verbose) {
      echo __("Installed / not configured");
   }

   return false;
}
