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

include("../../../inc/includes.php");

$plugin = new Plugin();

if ($plugin->isActivated('gdprropa')) {

   $config = new PluginGdprropaConfig();

   if (isset($_POST['add'])) {
      $config->check(-1, CREATE, $_POST);
      $config->add($_POST);
      Html::back();
   } else if (isset($_POST['update'])) {
      $config->check($_POST['id'], UPDATE, $_POST);
      $config->update($_POST);
      Html::back();
   } else if (isset($_POST['sampledata'])) {
      $config->check(-1, CREATE, $_POST);
      $config->installSampleData($_POST);
      Html::back();
   } else {
      Html::header(PluginGdprropaRecord::getTypeName(0), '', "management", "plugingdprropamenu");
      $config->showForm(-1, []);
      Html::footer();
   }

}
