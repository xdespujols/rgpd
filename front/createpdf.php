<?php

/*
 -------------------------------------------------------------------------
 GDPR Records of Processing Activities plugin for GLPI
 Copyright © 2020-2025 by Yild.

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
 If not, see <https://www.gnu.org/licenses/>.

 Based on DPO Register plugin, by Karhel Tmarr.

 --------------------------------------------------------------------------

  @package   gdprropa
  @author    Yild
  @copyright Copyright © 2020-2025 by Yild
  @license   GPLv3+
             https://www.gnu.org/licenses/gpl.txt
  @link      https://github.com/yild/gdprropa
  @since     1.0.0
 --------------------------------------------------------------------------
 */

namespace GlpiPlugin\Gdprropa;

use Html;
use Plugin;
use Session;

include("../../../inc/includes.php");

Plugin::load('gdprropa', true);

Session::checkCentralAccess();

if (isset($_GET['createpdf'])) {
    $print_options = CreatePDF::preparePrintOptionsFromForm($_GET);

    if (isset($_GET['action'])) {
        if ($_GET['action'] == 'prepare') {
            if (isset($_GET['report_type'])) {
                $type = $_GET['report_type'];
            } else {
                $type = CreatePDF::REPORT_ALL;
            }

            Html::header(Record::getTypeName(0), '', "management", Menu::class);
            CreatePDF::showPrepareForm($type);
        } elseif ($_GET['action'] == 'print') {
            $pdfoutput = new CreatePDF();
            $pdfoutput->generateReport($_GET, $print_options);
            $pdfoutput->showPDF();
        }
    }
}
