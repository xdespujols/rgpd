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

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

class PluginRgpdControllerInfo extends CommonDBChild {

   static public $itemtype = 'Entity';
   static public $items_id = 'entities_id';

   static public $logs_for_parent = true;
   static public $checkParentRights = true;

   static $rightname = 'plugin_rgpd_controllerinfo';

   static function getTypeName($nb = 0) {

      return __("GDPR Controller Info", 'rgpd');
   }

   function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {

      if (!PluginRgpdControllerInfo::canView()) {
         return false;
      }

      switch ($item->getType()) {
         case Entity::class :

            return self::createTabEntry(PluginRgpdControllerInfo::getTypeName(), 0);
      }

      return '';
   }

   static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {

      switch ($item->getType()) {
         case Entity::class :
            $info = new self();
            $info->showForEntity($item);
            break;
      }

      return true;
   }

   function prepareInputForAdd($input) {

      $input['users_id_creator'] = Session::getLoginUserID();

      return parent::prepareInputForAdd($input);
   }

   function prepareInputForUpdate($input) {

      $input['users_id_lastupdater'] = Session::getLoginUserID();

      return parent::prepareInputForUpdate($input);
   }

   function showForEntity(Entity $entity, $options = []) {

      global $CFG_GLPI;

      $colsize1 = '15%';
      $colsize2 = '35%';
      $colsize3 = '15%';
      $colsize4 = '35%';

      $this->getFromDBByCrit(['entities_id' => $entity->fields['id']]);

      if (!isset($this->fields['id'])) {
         $this->fields['id'] = -1;
      }

      $canedit = $this->can($this->fields['id'], UPDATE);

      if ($this->fields['id'] <= 0 && !PluginRgpdControllerInfo::canCreate()) {
         echo "<br><br><span class='b'>" . __("Controller information not set.", 'rgpd') . "</span><br><br>";

         return;
      }

      $options['canedit'] = $canedit;
      $options['formtitle'] = __("Manage entity Controller information", 'rgpd');

      $this->initForm($this->fields['id'], $options);
      $this->showFormHeader($options);

      echo "<tr class='tab_bg_3'><td colspan='4'><center><strong>";
      echo __("GDPR Article 30 1a", 'rgpd');
      echo "</strong></center></td></tr>";

      echo "<tr class='tab_bg_2'><td width='$colsize1'>";
      echo __("Legal representative", 'rgpd');
      echo "</td><td width='$colsize2'>";
      User::dropdown([
         'right' => 'all',
         'name' => 'users_id_representative',
         'value' => array_key_exists('users_id_representative', $this->fields) ? $this->fields['users_id_representative'] : null
      ]);

      echo "</td><td width='$colsize3'>";
      echo __("Data Protection Officer", 'rgpd');
      echo "</td><td  width='$colsize4'>";
      User::dropdown([
         'right' => 'all',
         'name' => 'users_id_dpo',
         'value' => array_key_exists('users_id_dpo', $this->fields) ? $this->fields['users_id_dpo'] : null
      ]);
      echo "</td></tr>";

      echo "</td><td width='$colsize1'>";
      echo __("Controller Name", 'rgpd');
      echo "</td><td colspan='3'>";
      if ($this->fields['id'] <= 0) {
         $this->fields['controllername'] = '';
      }
      $controller_name = Html::cleanInputText($this->fields['controllername']);
      echo "<input type='text' style='width:98%' maxlength=250 name='controllername' required value='" . $controller_name . "'/>";
      echo "</td></tr>";

      echo "<tr class='tab_bg_3'><td colspan='4'><center><strong>";
      echo __("Controller configuration - contract types", 'rgpd');
      echo "</strong></center></td></tr>";

      echo "<tr class='tab_bg_2'><td width='$colsize1'>";
      echo __("Joint Controller Contract Type", 'rgpd');
      echo "</td><td width='$colsize2'>";
      ContractType::dropdown([
         'width' => '75%',
         'name' => 'contracttypes_id_jointcontroller',
         'value' => array_key_exists('contracttypes_id_jointcontroller', $this->fields) ? $this->fields['contracttypes_id_jointcontroller'] : null
      ]);
      echo "</td><td width='$colsize3'>";
      echo __("Processor Contract Type", 'rgpd');
      echo "</td><td width='$colsize4'>";
      ContractType::dropdown([
         'width' => '75%',
         'name' => 'contracttypes_id_processor',
         'value' => array_key_exists('contracttypes_id_processor', $this->fields) ? $this->fields['contracttypes_id_processor'] : null
      ]);
      echo "</td></tr>";

      echo "<tr class='tab_bg_2'><td width='$colsize1'>";
      echo __("Thirdparty Contract Type", 'rgpd');
      echo "</td><td width='$colsize2'>";
      ContractType::dropdown([
         'width' => '75%',
         'name' => 'contracttypes_id_thirdparty',
         'value' => array_key_exists('contracttypes_id_thirdparty', $this->fields) ? $this->fields['contracttypes_id_thirdparty'] : null
      ]);
      echo "</td><td width='$colsize3'>";
      echo __("Internal Contract Type", 'rgpd');
      echo "</td><td width='$colsize4'>";
      ContractType::dropdown([
         'width' => '75%',
         'name' => 'contracttypes_id_internal',
         'value' => array_key_exists('contracttypes_id_internal', $this->fields) ? $this->fields['contracttypes_id_internal'] : null
      ]);
      echo "</td></tr>";

      echo "<tr class='tab_bg_2'><td width='$colsize1'>";
      echo __("Other Contract Type", 'rgpd');
      echo "</td><td width='$colsize2'>";
      ContractType::dropdown([
         'width' => '75%',
         'name' => 'contracttypes_id_other',
         'value' => array_key_exists('contracttypes_id_other', $this->fields) ? $this->fields['contracttypes_id_other'] : null
      ]);
      echo "</td><td width='$colsize3'>";
      echo "</td><td width='$colsize4'>";
      echo "</td></tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td class='center' colspan='4'>";
      echo "<input type='hidden' name='entities_id' value='" . $entity->fields['id'] . "'/>";
      echo "</td></tr>";

      $this->showFormButtons($options);
      Html::closeForm();

      if ((Session::haveRight('plugin_rgpd_createpdf', CREATE))) {
         echo "<div class='firstbloc'>";
         echo "<form method='GET' action=\"" . $CFG_GLPI['root_doc'] . "/plugins/rgpd/front/createpdf.php\">";
         echo "<table class='tab_cadre_fixe' id='mainformtable'>";
         echo "<tbody>";
         echo "<tr class='headerRow'>";
         echo "<th colspan='3' class=''>" . __('PDF creation settings', 'rgpd') . "</th>";
         echo "</tr>";

         $_config = PluginRgpdCreatePDF::getDefaultPrintOptions();
         $_config['report_type'] = PluginRgpdCreatePDF::REPORT_FOR_ENTITY;
         PluginRgpdCreatePDF::showConfigFormElements($_config);

         echo "</table>";
         echo "<input type='hidden' name='report_type' value=\"" . PluginRgpdCreatePDF::REPORT_FOR_ENTITY . "\">";
         echo "<input type='hidden' name='entities_id' value='" . $entity->fields['id'] . "'>";
         echo "<input type='hidden' name='action' value=\"print\">";
         echo "<input type='submit' class='submit' name='createpdf' value='" . __("Create Controller RoPA PDF for Entity", 'rgpd') . "' />";
         Html::closeForm();
         echo "</div>";
      }

      echo "<p></p>";

   }

   static function getFirstControllerInfo($entity_id, $allow_from_ancestors = true) {

      $controllerinfo = new PluginRgpdControllerInfo();
      $controllerinfo->getFromDBByCrit(['entities_id' => $entity_id]);

      if (!$allow_from_ancestors) {
         return $controllerinfo;
      } else {
         if ((isset($controllerinfo->fields['id'])) &&
               ($controllerinfo->fields['is_recursive'] &&
               ($controllerinfo->fields['entities_id'] == $entity_id))
            ) {
            return $controllerinfo;
         } else {
            $ancestors = getAncestorsOf('glpi_entities', $entity_id);
            foreach (array_reverse($ancestors) as $ancestor) {
               return PluginRgpdControllerInfo::getFirstControllerInfo($ancestor);
            }
         }
      }
   }

   static function getContractTypes($entity_id, $compact = false) {

      $controllerinfo = PluginRgpdControllerInfo::getFirstControllerInfo($entity_id, PluginRgpdConfig::getConfig('system', 'allow_controllerinfo_from_ancestor'));

      $out = [
         'contracttypes_id_jointcontroller' => -1,
         'contracttypes_id_processor' => -1,
         'contracttypes_id_thirdparty' => -1,
         'contracttypes_id_internal' => -1,
         'contracttypes_id_other' => -1,
      ];

      foreach ($out as $key => $value) {
         if (isset($controllerinfo->fields[$key])) {
            $out[$key] = $controllerinfo->fields[$key];
         }
      }

      if ($compact) {
         $out = array_values($out);
      }

      return $out;
   }

   static function getSearchOptionsControllerInfo() {

      $options = [];

      $options[5601] = [
         'id' => '5601',
         'table' => 'glpi_users',
         'field' => 'name',
         'linkfield' => 'users_id_representative',
         'name' => __("Legal representative", 'rgpd'),
         'massiveaction' => false,
         'datatype' => 'dropdown',
         'joinparams' => [
            'beforejoin' => [
               'table' => self::getTable(),
               'joinparams' => [
                  'jointype' => 'child'
               ]
            ]
         ]
      ];

      $options[5602] = [
         'id' => '5602',
         'table' => 'glpi_users',
         'field' => 'name',
         'linkfield' => 'users_id_dpo',
         'name' => __("Data Protection Officer", 'rgpd'),
         'massiveaction' => false,
         'datatype' => 'dropdown',
         'joinparams' => [
            'beforejoin' => [
               'table' => self::getTable(),
                  'joinparams' => [
                     'jointype' => 'child'
               ]
            ]
         ]
      ];

      $options[5603] = [
         'id' => '5603',
         'table' => self::getTable(),
         'field' => 'controllername',
         'name' => __("Controller Name", 'rgpd'),
         'massiveaction' => false,
         'joinparams' => [
            'jointype' => 'child'
         ],
      ];

      return $options;
   }

   function rawSearchOptions() {

      $tab = [];

      $tab[] = [
         'id' => '11',
         'table' => $this->getTable(),
         'field' => 'controllername',
         'name' => __("Controller Name", 'rgpd'),
         'massiveaction' => false,
         'datatype' => 'text',
      ];

      $tab = array_merge(parent::rawSearchOptions(), $tab);

      return $tab;
   }

}
