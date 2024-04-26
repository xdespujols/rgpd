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

include("../../../inc/includes.php");
Plugin::load('rgpd', true);

if (strpos($_SERVER['PHP_SELF'], 'record_retention_retention_type_dropdown.php')) {

   $AJAX_INCLUDE = 1;

   header("Content-Type: text/html; charset=UTF-8");
   Html::header_nocache();
}

if (array_key_exists('type', $_POST)) {

   switch ($_POST['type']) {

      case PluginRgpdRecord_Retention::RETENTION_TYPE_CONTRACT:
         PluginRgpdRecord_Retention::showContractInputs($_POST);
         break;

      case PluginRgpdRecord_Retention::RETENTION_TYPE_LEGALBASISACT:
         PluginRgpdRecord_Retention::showLegalBasesInputs($_POST);
         break;

      case PluginRgpdRecord_Retention::RETENTION_TYPE_OTHER:
         PluginRgpdRecord_Retention::showOtherInputs($_POST);
         break;
   }
} else {
   echo '';
}
