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

Session::checkCentralAccess();

if (isset($_GET['createpdf'])) {

   $print_options = PluginRgpdCreatePDF::preparePrintOptionsFromForm($_GET);

   if (isset($_GET['action'])) {

      if ($_GET['action'] == 'prepare') {

         if (isset($_GET['report_type'])) {
            $type = $_GET['report_type'];
         } else {
            $type = PluginRgpdCreatePDF::REPORT_ALL;
         }

         Html::header(PluginRgpdRecord::getTypeName(0), '', "management", "pluginrgpdamenu");
         PluginRgpdCreatePDF::showPrepareForm($type);
      } else if ($_GET['action'] == 'print') {
         $pdfoutput = new PluginRgpdCreatePDF();
         $pdfoutput->generateReport($_GET, $print_options);
         $pdfoutput->showPDF();
      }
   }

}

