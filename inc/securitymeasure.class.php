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

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

class PluginGdprropaSecurityMeasure extends CommonDropdown {

   static $rightname = 'plugin_gdprropa_securitymeasure';

   public $dohistory = true;

   const SECURITYMEASURE_TYPE_ORGANIZATION = 1;
   const SECURITYMEASURE_TYPE_PHYSICAL = 4;
   const SECURITYMEASURE_TYPE_IT = 8;

   const DROPDOWN_GROUPBY_TYPE = 1;
   const DROPDOWN_GROUPBY_TYPE_2 = 2;
   const DROPDOWN_GROUPBY_ENTITY = 3;

   static function getTypeName($nb = 0) {

      return _n("Security Measure", "Security Measures", $nb, 'gdprropa');
   }

   function getAdditionalFields() {

      return [
         [
            'name' => 'type',
            'label' => __("Type"),
            'list' => true,
         ],
         [
            'name' => 'content',
            'label' => __("Description"),
            'type' => 'textarea',
         ],
      ];
   }

   static function getSpecificValueToDisplay($field, $values, array $options = []) {

      if (!is_array($values)) {
         $values = [$field => $values];
      }

      switch ($field) {
         case 'type' :
            $types = self::getAllTypesArray();

            return $types[$values[$field]];
      }

      return '';
   }

   static function getSpecificValueToSelect($field, $name = '', $values = '', array $options = []) {

      if (!is_array($values)) {
         $values = [$field => $values];
      }
      $options['display'] = false;

      switch ($field) {
         case 'type' :

            return self::dropdownTypes($name, $values[$field], false);
      }

      return parent::getSpecificValueToSelect($field, $name, $values, $options);
   }

   function displaySpecificTypeField2($id, $field = []) {

      if ($field['name'] == 'type') {
         self::dropdownTypes($field['name'], $this->fields[$field['name']], true);
      }
   }

   static function dropdownTypes($name, $value = 0, $display = true) {

      return Dropdown::showFromArray($name, self::getAllTypesArray(), [
         'value' => $value, 'display' => $display]);
   }

   static function getAllTypesArray() {

      return [
         //'' => Dropdown::EMPTY_VALUE,
         self::SECURITYMEASURE_TYPE_ORGANIZATION => __("Organizational", 'gdprropa'),
         self::SECURITYMEASURE_TYPE_PHYSICAL     => __("Physical", 'gdprropa'),
         self::SECURITYMEASURE_TYPE_IT           => __("IT", 'gdprropa')
      ];
   }

   function prepareInputForAdd($input) {

      $input['users_id_creator'] = Session::getLoginUserID();

      return parent::prepareInputForAdd($input);
   }

   function prepareInputForUpdate($input) {

      $input['users_id_lastupdater'] = Session::getLoginUserID();

      return parent::prepareInputForUpdate($input);
   }

   function cleanDBonPurge() {

      $rel = new PluginGdprropaRecord_SecurityMeasure();
      $rel->deleteByCriteria(['plugin_gdprropa_securitymeasures_id' => $this->fields['id']]);

   }

   static function dropdown($options = []) {

      global $DB;

      $p = [
         'name'             => 'plugin_gdprropa_securitymeasures_id',
         'value'            => '',
         'all'              => 0,
         'width'            => '80%',
         'entity'           => -1,
         'entity_sons'      => false,
         'used'             => [],
         'rand'             => mt_rand(),
         'display'          => true,
         'specific_tags'    => [],
         'option_tooltips'  => [],
         'group_by'         => self::DROPDOWN_GROUPBY_TYPE,
      ];

      if (is_array($options) && count($options)) {
         foreach ($options as $key => $val) {
            $p[$key] = $val;
         }
      }

      if ((strlen($p['value']) == 0) || !is_numeric($p['value'])) {
         $p['value'] = 0;
      }

      $tab = [];
      $tab[] = Dropdown::EMPTY_VALUE;

      $error = false;

      if (!($p['entity'] < 0) && $p['entity_sons']) {
         if (is_array($p['entity'])) {
            $tab[] = "entity_sons options is not available with array of entity";
            $error = true;
         } else {
            $p['entity'] = getSonsOf('glpi_entities', $p['entity']);
         }
      }

      if (!$error) {

         $entities = getAncestorsOf('glpi_entities', $p['entity']);
         array_push($entities, $p['entity']);

         $query = '
            SELECT
               `glpi_plugin_gdprropa_securitymeasures`.`id`,
               `glpi_plugin_gdprropa_securitymeasures`.`name`,
               `glpi_plugin_gdprropa_securitymeasures`.`type`,
               `glpi_plugin_gdprropa_securitymeasures`.`entities_id`,
               `glpi_entities`.`completename`
            FROM
               `glpi_plugin_gdprropa_securitymeasures` 
            LEFT JOIN
               `glpi_entities` ON (`glpi_plugin_gdprropa_securitymeasures`.`entities_id` = `glpi_entities`.`id`)
            WHERE
               (
                  (`glpi_plugin_gdprropa_securitymeasures`.`is_recursive` = 1 AND
                   `glpi_plugin_gdprropa_securitymeasures`.`entities_id` IN (' . implode(',', $entities) . ')
                  ) OR (
                   `glpi_plugin_gdprropa_securitymeasures`.`entities_id` = ' . $p['entity'] . '
                  )
               )
            ORDER BY
               FIELD(`glpi_plugin_gdprropa_securitymeasures`.`entities_id`, 4) DESC,
               `glpi_plugin_gdprropa_securitymeasures`.`type`';

         $types = self::getAllTypesArray();

         $result = $DB->request($query);

         $p['group_by'] = self::DROPDOWN_GROUPBY_TYPE_2;

         switch ($p['group_by']) {
            case self::DROPDOWN_GROUPBY_ENTITY :

               $cur_name = '';
               while ($item = $result->next()) {
                  if ($cur_name != $item['completename']) {
                     $cur_name = $item['completename'];
                  }

                  $name = $types[$item['type']] . ':&nbsp&nbsp&nbsp&nbsp' . $item['name'];
                  if ($_SESSION['glpiis_ids_visible'] || empty($item['name'])) {
                     $name = sprintf(__('%1$s (%2$s)'), $name, $item['id']);
                  }
                  $tab[$cur_name][$item['id']] = $name;
                  $p['option_tooltips'][$cur_name]['__optgroup_label'] = '';
               }
               break;

            case self::DROPDOWN_GROUPBY_TYPE :
            case self::DROPDOWN_GROUPBY_TYPE_2 :

               $cur_type = '';
               while ($item = $result->next()) {
                  if ($cur_type != $item['type']) {
                     $cur_type = $item['type'];
                  }

                  if ($p['group_by'] == self::DROPDOWN_GROUPBY_TYPE) {
                     $name = $item['completename'] . ':&nbsp&nbsp&nbsp&nbsp' . $item['name'];
                  } else {
                     $name = $item['name'] .' &nbsp&nbsp(' . $item['completename']. ')';
                  }
                  if ($_SESSION['glpiis_ids_visible'] || empty($item['name'])) {
                     $name = sprintf(__('%1$s (%2$s)'), $name, $item['id']);
                  }
                  $tab[$types[$item['type']]][$item['id']] = $name;
                  $p['option_tooltips'][$types[$item['type']]]['__optgroup_label'] = '';
               }

               break;

         }

      }

      return Dropdown::showFromArray($p['name'], $tab, $p);
   }

   function rawSearchOptions() {

      $tab = [];

      $tab[] = [
         'id'                 => 'common',
         'name'               => __("Characteristics")
      ];

      $tab[] = [
         'id'                 => '1',
         'table'              => $this->getTable(),
         'field'              => 'name',
         'name'               => __("Name"),
         'datatype'           => 'itemlink',
         'massiveaction'      => false,
         'autocomplete'       => true,
      ];

      $tab[] = [
         'id'                 => '2',
         'table'              => $this->getTable(),
         'field'              => 'id',
         'name'               => __("ID"),
         'massiveaction'      => false,
         'datatype'           => 'number',
      ];

      $tab[] = [
         'id'                 => '3',
         'table'              => $this->getTable(),
         'field'              => 'type',
         'name'               => __("Type", 'gdprropa'),
         'searchtype'         => 'equals',
         'massiveaction'      => true,
         'datatype'           => 'specific'
      ];

      $tab[] = [
         'id'                 => '4',
         'table'              => $this->getTable(),
         'field'              => 'content',
         'name'               => __("Description"),
         'datatype'           => 'text',
         'toview'             => true,
         'massiveaction'      => true,
      ];

      $tab[] = [
         'id'                 => '5',
         'table'              => $this->getTable(),
         'field'              => 'comment',
         'name'               => __("Comments"),
         'datatype'           => 'text',
         'toview'             => true,
         'massiveaction'      => true,
      ];

      $tab[] = [
         'id'                 => '6',
         'table'              => 'glpi_entities',
         'field'              => 'completename',
         'name'               => __("Entity"),
         'massiveaction'      => true,
         'datatype'           => 'dropdown',
      ];

      $tab[] = [
         'id'                 => '7',
         'table'              => $this->getTable(),
         'field'              => 'is_recursive',
         'name'               => __("Child entities"),
         'massiveaction'      => false,
         'datatype'           => 'bool',
      ];

      return $tab;
   }

}
