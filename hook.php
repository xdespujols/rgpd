<?php
/*
 -------------------------------------------------------------------------
 GDPR Records of Processing Activities plugin for GLPI
 
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
  @copyright Copyright (c) 2024 by XDespujols
  @license   GPLv3+
             http://www.gnu.org/licenses/gpl.txt
  @link      https://github.com/xdespujols/rgpd
  @since     2020
 --------------------------------------------------------------------------
 */

function plugin_rgpd_install() {

   global $DB;

   $install = false;
   if (!$DB->tableExists('glpi_plugin_rgpd_records')) {
      $install = true;
   }

   if ($install) {

      if (!$DB->tableExists('glpi_plugin_rgpd_configs')) {
         $query = "CREATE TABLE `glpi_plugin_rgpd_configs` (
                     `id` int(11) NOT NULL auto_increment,
                     `entities_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_entities (id)',
                     `config` TEXT NOT NULL default '{}',

                     `date_creation` datetime default NULL,
                     `users_id_creator` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',
                     `date_mod` datetime default NULL,
                     `users_id_lastupdater` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',

                     PRIMARY KEY  (`id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());
      }

      if (!$DB->tableExists('glpi_plugin_rgpd_controllerinfos')) {
         $query = "CREATE TABLE `glpi_plugin_rgpd_controllerinfos` (
                     `id` int(11) NOT NULL auto_increment,
                     `entities_id` int(11) COMMENT 'RELATION to glpi_entities (id)',
                     `is_recursive` tinyint(1) NOT NULL default '1',
                     `users_id_representative` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',
                     `users_id_dpo` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',
                     `contracttypes_id_jointcontroller` int(11) default NULL COMMENT 'RELATION to glpi_contracttypes (id)',
                     `contracttypes_id_processor` int(11) default NULL COMMENT 'RELATION to glpi_contracttypes (id)',
                     `contracttypes_id_thirdparty` int(11) default NULL COMMENT 'RELATION to glpi_contracttypes (id)',
                     `contracttypes_id_internal` int(11) default NULL COMMENT 'RELATION to glpi_contracttypes (id)',
                     `contracttypes_id_other` int(11) default NULL COMMENT 'RELATION to glpi_contracttypes (id)',
                     `controllername` varchar(250) default NULL,

                     `date_creation` datetime default NULL,
                     `users_id_creator` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',
                     `date_mod` datetime default NULL,
                     `users_id_lastupdater` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',

                     PRIMARY KEY  (`id`),
                     UNIQUE `entities_id` (`entities_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());

      }

      if (!$DB->tableExists('glpi_plugin_rgpd_datasubjectscategories')) {
         $query = "CREATE TABLE `glpi_plugin_rgpd_datasubjectscategories` (
                     `id` int(11) NOT NULL auto_increment,
                     `name` varchar(255) collate utf8_unicode_ci default NULL,
                     `comment` text collate utf8_unicode_ci,
                     `entities_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_entities (id)',
                     `is_recursive` tinyint(1) NOT NULL default '1',

                     `date_creation` datetime default NULL,
                     `users_id_creator` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',
                     `date_mod` datetime default NULL,
                     `users_id_lastupdater` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',

                     PRIMARY KEY  (`id`),
                     KEY `name` (`name`),
                     UNIQUE `un_per_record` (`name`, `entities_id`)
                  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());

         $dp = new DisplayPreference();
         $dp->add([
            'itemtype' => 'PluginRgpdDataSubjectsCategory',
            'num' => 3,
            'rank' => 1,
            'users_id' => 0
         ]);
      }

      if (!$DB->tableExists('glpi_plugin_rgpd_legalbasisacts')) {
         $query = "CREATE TABLE `glpi_plugin_rgpd_legalbasisacts` (
                     `id` int(11) NOT NULL auto_increment,
                     `name` varchar(255) collate utf8_unicode_ci default NULL,
                     `type` tinyint(1) NOT NULL default '0',
                     `content` varchar(1024) collate utf8_unicode_ci default NULL,
                     `comment` text collate utf8_unicode_ci,
                     `injected` tinyint(1) NOT NULL default '0' COMMENT 'is record injected ad plugin install',
                     `entities_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_entities (id)',
                     `is_recursive` tinyint(1) NOT NULL default '1',

                     `date_creation` datetime default NULL,
                     `users_id_creator` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',
                     `date_mod` datetime default NULL,
                     `users_id_lastupdater` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',

                     PRIMARY KEY  (`id`),
                     KEY `name` (`name`),
                     UNIQUE `un_per_record` (`name`, `type`, `entities_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());

         $dp = new DisplayPreference();
         $dp->add([
            'itemtype' => 'PluginRgpdLegalBasisAct',
            'num' => 3,
            'rank' => 1,
            'users_id' => 0
         ]);
         $dp->add([
            'itemtype' => 'PluginRgpdLegalBasisAct',
            'num' => 4,
            'rank' => 2,
            'users_id' => 0
         ]);
      }

      if (!$DB->tableExists('glpi_plugin_rgpd_personaldatacategories')) {
         $query = "CREATE TABLE `glpi_plugin_rgpd_personaldatacategories` (
                     `id` int(11) NOT NULL auto_increment,
                     `name` varchar(255) collate utf8_unicode_ci default NULL,
                     `completename` text collate utf8_unicode_ci default NULL,
                     `level` int(11) NOT NULL default '0',
                     `comment` text collate utf8_unicode_ci,
                     `plugin_rgpd_personaldatacategories_id` int(11) NOT NULL default '0',
                     `entities_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_entities (id)',
                     `is_recursive` tinyint(1) NOT NULL default '1',
                     `is_special_category` tinyint(1) default '0',

                     `date_creation` datetime default NULL,
                     `users_id_creator` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',
                     `date_mod` datetime default NULL,
                     `users_id_lastupdater` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',

                     PRIMARY KEY  (`id`),
                     KEY `name` (`name`),
                     UNIQUE `un_per_record` (`name`, `entities_id`)
                  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());

         $dp = new DisplayPreference();
         $dp->add([
            'itemtype' => 'PluginRgpdPersonalDataCategory',
            'num' => 3,
            'rank' => 1,
            'users_id' => 0
         ]);
         $dp->add([
            'itemtype' => 'PluginRgpdPersonalDataCategory',
            'num' => 4,
            'rank' => 2,
            'users_id' => 0
         ]);
      }

      if (!$DB->tableExists('glpi_plugin_rgpd_securitymeasures')) {
         $query = "CREATE TABLE `glpi_plugin_rgpd_securitymeasures` (
                     `id` int(11) NOT NULL auto_increment,
                     `name` varchar(255) collate utf8_unicode_ci default NULL,
                     `type` tinyint(1) NOT NULL default '0',
                     `content` varchar(1000) collate utf8_unicode_ci default NULL,
                     `comment` text collate utf8_unicode_ci,
                     `entities_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_entities (id)',
                     `is_recursive` tinyint(1) NOT NULL default '1',

                     `date_creation` datetime default NULL,
                     `users_id_creator` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',
                     `date_mod` datetime default NULL,
                     `users_id_lastupdater` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',

                     PRIMARY KEY  (`id`),
                     KEY `name` (`name`),
                     UNIQUE `un_per_record` (`name`, `type`, `entities_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());

         $dp = new DisplayPreference();
         $dp->add([
            'itemtype' => 'PluginRgpdSecurityMeasure',
            'num' => 3,
            'rank' => 1,
            'users_id' => 0
         ]);
         $dp->add([
            'itemtype' => 'PluginRgpdSecurityMeasure',
            'num' => 4,
            'rank' => 2,
            'users_id' => 0
         ]);
      }

      if (!$DB->tableExists('glpi_plugin_rgpd_records')) {
         $query = "CREATE TABLE `glpi_plugin_rgpd_records` (
                     `id` int(11) NOT NULL auto_increment,
                     `entities_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_entities (id)',
                     `is_recursive` tinyint(1) NOT NULL default '1',
                     `is_deleted` tinyint(1) NOT NULL default '0',
                     `name` varchar(255) collate utf8_unicode_ci default NULL,
                     `content` varchar(1000) collate utf8_unicode_ci default NULL,
                     `additional_info` varchar(1000) collate utf8_unicode_ci default NULL,
                     `states_id` int(11) NOT NULL default '1' COMMENT 'RELATION to glpi_states (id)',
                     `storage_medium` int(11) NOT NULL default '0'  COMMENT 'Default status to UNDEFINED',
                     `pia_required` tinyint(1) NOT NULL default '0',
                     `pia_status` int(11) NOT NULL default '0' COMMENT 'Default status to UNDEFINED',
                     `first_entry_date` datetime default NULL,
                     `consent_required` tinyint(1) NOT NULL default '0',
                     `consent_storage` varchar(1000) collate utf8_unicode_ci default NULL,

                     `date_creation` datetime default NULL,
                     `users_id_creator` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',
                     `date_mod` datetime default NULL,
                     `users_id_lastupdater` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',

                     PRIMARY KEY  (`id`),
                     KEY `name` (`name`),
                     UNIQUE `un_per_record` (`name`, `entities_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());

         $dp = new DisplayPreference();
         $dp->add([
            'itemtype' => 'PluginRgpdRecord',
            'num' => 2,
            'rank' => 1,
            'users_id' => 0
         ]);
         $dp->add([
            'itemtype' => 'PluginRgpdRecord',
            'num' => 5,
            'rank' => 2,
            'users_id' => 0
         ]);
         $dp->add([
            'itemtype' => 'PluginRgpdRecord',
            'num' => 3,
            'rank' => 3,
            'users_id' => 0
         ]);
         $dp->add([
            'itemtype' => 'PluginRgpdRecord',
            'num' => 6,
            'rank' => 4,
            'users_id' => 0
         ]);
         $dp->add([
            'itemtype' => 'PluginRgpdRecord',
            'num' => 9,
            'rank' => 5,
            'users_id' => 0
         ]);
         $dp->add([
            'itemtype' => 'PluginRgpdRecord',
            'num' => 10,
            'rank' => 6,
            'users_id' => 0
         ]);
         $dp->add([
            'itemtype' => 'PluginRgpdRecord',
            'num' => 12,
            'rank' => 7,
            'users_id' => 0
         ]);
      }

      if (!$DB->tableExists('glpi_plugin_rgpd_records_contracts')) {
         $query = "CREATE TABLE `glpi_plugin_rgpd_records_contracts` (
                     `id` int(11) NOT NULL auto_increment,
                     `plugin_rgpd_records_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_rgpd_records (id)',
                     `contracts_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_contracts (id)',

                     PRIMARY KEY  (`id`),
                     UNIQUE `un_per_record` (`plugin_rgpd_records_id`, `contracts_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());
      }

      if (!$DB->tableExists('glpi_plugin_rgpd_records_datasubjectscategories')) {
         $query = "CREATE TABLE `glpi_plugin_rgpd_records_datasubjectscategories` (
                     `id` int(11) NOT NULL auto_increment,
                     `plugin_rgpd_records_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_rgpd_records (id)',
                     `plugin_rgpd_datasubjectscategories_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_rgpd_datasubjectscategories (id)',

                     PRIMARY KEY  (`id`),
                     UNIQUE `un_per_record` (`plugin_rgpd_records_id`, `plugin_rgpd_datasubjectscategories_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());
      }

      if (!$DB->tableExists('glpi_plugin_rgpd_records_legalbasisacts')) {
         $query = "CREATE TABLE `glpi_plugin_rgpd_records_legalbasisacts` (
                     `id` int(11) NOT NULL auto_increment,
                     `plugin_rgpd_records_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_rgpd_records (id)',
                     `plugin_rgpd_legalbasisacts_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_rgpd_legalbasisacts (id)',

                     PRIMARY KEY  (`id`),
                     UNIQUE `un_per_record` (`plugin_rgpd_records_id`, `plugin_rgpd_legalbasisacts_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());
      }

      if (!$DB->tableExists('glpi_plugin_rgpd_records_personaldatacategories')) {
         $query = "CREATE TABLE `glpi_plugin_rgpd_records_personaldatacategories` (
                     `id` int(11) NOT NULL auto_increment,
                     `plugin_rgpd_records_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_rgpd_records (id)',
                     `plugin_rgpd_personaldatacategories_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugin_rgpd_personaldatacategories (id)',

                     PRIMARY KEY  (`id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());
      }

      if (!$DB->tableExists('glpi_plugin_rgpd_records_retentions')) {
         $query = "CREATE TABLE `glpi_plugin_rgpd_records_retentions` (
                     `id` int(11) NOT NULL auto_increment,
                     `plugin_rgpd_records_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_rgpd_records (id)',
                     `type` int(11) NOT NULL default '0'  COMMENT 'Default status to UNDEFINED',
                     `contracts_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_contracts (id)',
                     `contract_until_is_valid` tinyint(1) NOT NULL default '0',
                     `contract_after_end_of` tinyint(1) NOT NULL default '0',
                     `contract_retention_value` int(11) NOT NULL default '0',
                     `contract_retention_scale` char(1) NOT NULL default 'y',
                     `plugin_rgpd_legalbasisacts_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_rgpd_legalbasisacts (id)',
                     `additional_info`  varchar(1000) collate utf8_unicode_ci default NULL,

                     PRIMARY KEY  (`id`),
                     UNIQUE `un_per_record` (`plugin_rgpd_records_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());
      }

      if (!$DB->tableExists('glpi_plugin_rgpd_records_securitymeasures')) {
         $query = "CREATE TABLE `glpi_plugin_rgpd_records_securitymeasures` (
                     `id` int(11) NOT NULL auto_increment,
                     `plugin_rgpd_records_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_rgpd_records (id)',
                     `plugin_rgpd_securitymeasures_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugin_rgpd_securitymeasures (id)',

                     PRIMARY KEY  (`id`),
                     UNIQUE `un_per_record` (`plugin_rgpd_records_id`, `plugin_rgpd_securitymeasures_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());
      }

      if (!$DB->tableExists('glpi_plugin_rgpd_records_softwares')) {
         $query = "CREATE TABLE `glpi_plugin_rgpd_records_softwares` (
                     `id` int(11) NOT NULL auto_increment,
                     `plugin_rgpd_records_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_rgpd_records (id)',
                     `softwares_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_softwares (id)',

                     PRIMARY KEY  (`id`),
                     UNIQUE `un_per_record` (`plugin_rgpd_records_id`, `softwares_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());
      }

   }

   PluginRgpdProfile::initProfile();
   PluginRgpdProfile::createFirstAccess($_SESSION['glpiactiveprofile']['id']);

   return true;
}

function plugin_rgpd_uninstall() {

   global $DB;

   $tables = [
      'glpi_plugin_rgpd_configs',
      'glpi_plugin_rgpd_controllerinfos',
      'glpi_plugin_rgpd_datasubjectscategories',
      'glpi_plugin_rgpd_legalbasisacts',
      'glpi_plugin_rgpd_personaldatacategories',
      'glpi_plugin_rgpd_securitymeasures',
      'glpi_plugin_rgpd_records',
      'glpi_plugin_rgpd_records_contracts',
      'glpi_plugin_rgpd_records_retentions',
      'glpi_plugin_rgpd_records_datasubjectscategories',
      'glpi_plugin_rgpd_records_legalbasisacts',
      'glpi_plugin_rgpd_records_personaldatacategories',
      'glpi_plugin_rgpd_records_securitymeasures',
      'glpi_plugin_rgpd_records_softwares',
   ];

   foreach ($tables as $table) {
      $DB->query("DROP TABLE IF EXISTS `$table`;");
   }

   $query = "DELETE FROM `glpi_logs`
               WHERE
                     `itemtype` LIKE 'PluginRgpd%'
                  OR `itemtype_link` LIKE 'PluginRgpd%'";
   $DB->queryOrDie($query, $DB->error());

   $dp = new DisplayPreference();
   $dp->deleteByCriteria(['itemtype' => [
      'PluginRgpdRecord',
      'PluginRgpdLegalBasisAct',
      'PluginRgpdSecurityMeasure',
      'PluginRgpdDataSubjectsCategory',
      'PluginRgpdPersonalDataCategory'
      ]
   ]);

   $profileRight = new ProfileRight();
   foreach (PluginRgpdProfile::getAllRights() as $right) {
      $profileRight->deleteByCriteria(['name' => $right['field']]);
   }

   PluginRgpdMenu::removeRightsFromSession();
   PluginRgpdProfile::removeRightsFromSession();

   return true;
}

function plugin_rgpd_getDropdown() {

   return [
      PluginRgpdLegalBasisAct::class => PluginRgpdLegalBasisAct::getTypeName(2),
      PluginRgpdSecurityMeasure::class => PluginRgpdSecurityMeasure::getTypeName(2),
      PluginRgpdDataSubjectsCategory::class => PluginRgpdDataSubjectsCategory::getTypeName(2),
      PluginRgpdPersonalDataCategory::class => PluginRgpdPersonalDataCategory::getTypeName(2),
   ];
}

function plugin_rgpd_getAddSearchOptions($itemtype) {

   $options = [];

   if ($itemtype == 'Entity') {
      $options = PluginRgpdControllerInfo::getSearchOptionsControllerInfo();
   }

   return $options;
}

function plugin_rgpd_getDatabaseRelations() {

   $plugin = new Plugin();

   if ($plugin->isActivated('rgpd')) {

      return [
         'glpi_entites' => [
            'glpi_plugin_rgpd_configs' => 'entities_id',
            'glpi_plugin_rgpd_records' => 'entities_id',
            'glpi_plugin_rgpd_controllerinfos' => 'entities_id',
            'glpi_plugin_rgpd_datasubjectscategories' => 'entities_id',
            'glpi_plugin_rgpd_legalbasisacts' => 'entities_id',
            'glpi_plugin_rgpd_personaldatacategories' => 'entities_id',
            'glpi_plugin_rgpd_securitymeasures' => 'entities_id',
            ],

         'glpi_users' => [
            'glpi_plugin_rgpd_controllerinfos' => [
               'users_id_representative',
               'users_id_dpo',
            ],
            'glpi_plugin_rgpd_configs' => [
               'users_id_creator',
               'users_id_lastupdater',
            ],
            'glpi_plugin_rgpd_datasubjectscategories' => [
               'users_id_creator',
               'users_id_lastupdater',
            ],
            'glpi_plugin_rgpd_legalbasisacts' => [
               'users_id_creator',
               'users_id_lastupdater',
            ],
            'glpi_plugin_rgpd_personaldatacategories' => [
               'users_id_creator',
               'users_id_lastupdater',
            ],
            'glpi_plugin_rgpd_records' => [
               'users_id_creator',
               'users_id_lastupdater',
               'users_id_owner',
            ],
            'glpi_plugin_rgpd_securitymeasures' => [
               'users_id_creator',
               'users_id_lastupdater',
            ],
         ],

         'glpi_contracts' => [
            'glpi_plugin_rgpd_records_contracts' => 'contracts_id',
            'glpi_plugin_rgpd_controllerinfos' => [
               'contracttypes_id_jointcontroller',
               'contracttypes_id_processor',
               'contracttypes_id_thirdparty',
               'contracttypes_id_internal',
               'contracttypes_id_other',
            ],
            'glpi_plugin_rgpd_records_retentions' => 'contracts_id',
         ],
         'glpi_plugin_rgpd_records' => [
            'glpi_plugin_rgpd_records_contracts' => 'plugin_rgpd_records_id',
            'glpi_plugin_rgpd_records_datasubjectscategories' => 'plugin_rgpd_records_id',
            'glpi_plugin_rgpd_records_legalbasisacts' => 'plugin_rgpd_records_id',
            'glpi_plugin_rgpd_records_personaldatacategories' => 'plugin_rgpd_records_id',
            'glpi_plugin_rgpd_records_retentions' => 'plugin_rgpd_records_id',
            'glpi_plugin_rgpd_records_securitymeasures' => 'plugin_rgpd_records_id',
            'glpi_plugin_rgpd_records_softwares' => 'plugin_rgpd_records_id',
         ],

         'glpi_plugin_rgpd_datasubjectscategories' => [
            'glpi_plugin_rgpd_records_datasubjectscategories' => 'plugin_rgpd_datasubjectscategories_id',
         ],

         'glpi_plugin_rgpd_legalbasisacts' => [
            'glpi_plugin_rgpd_records_legalbasisacts' => 'plugin_rgpd_legalbasisacts_id',
            'glpi_plugin_rgpd_records_retentions' => 'plugin_rgpd_legalbasisacts_id',
         ],

         'glpi_plugin_rgpd_personaldatacategories' => [
            'glpi_plugin_rgpd_records_personaldatacategories' => 'plugin_rgpd_personaldatacategories_id',
         ],

         'glpi_plugin_rgpd_securitymeasures' => [
            'glpi_plugin_rgpd_records_securitymeasures' => 'plugin_rgpd_securitymeasures_id',
         ],

         'glpi_softwares' => [
            'glpi_plugin_rgpd_records_softwares' => 'softwares_id',
         ],

      ];

   }

   return [];
}

function plugin_rgpd_postinit() {

   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['item_purge']['rgpd'] = [];
   $PLUGIN_HOOKS['item_purge']['rgpd']['Contract'] = ['PluginRgpdRecord_Contract', 'cleanForItem'];
   $PLUGIN_HOOKS['item_purge']['rgpd']['Software'] = ['PluginRgpdRecord_Software', 'cleanForItem'];

}
