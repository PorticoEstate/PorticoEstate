<?php
  /**************************************************************************\
  * phpGroupWare - Inventory                                                 *
  * (http://www.phpgroupware.org)                                            *
  * Written by Bettina Gille  [ceb@phpgroupware.org]                         *
  * Modified by Lars Kneschke  [lars@kneschke.de]                            *
  * --------------------------------------------------------                 *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
  /* $Id: delivery_pdf.php 8746 2001-12-23 22:55:32Z bettina $ */

    $phpgw_info["flags"] = array("currentapp" => "inv",
                               "noheader" => True, 
                               "nonavbar" => True);

    include("../header.inc.php");

    include("./inc/pdf_layout.inc.php");

    $logo = "doc/logo.jpg";

    $delivery = new deliveryPDF();

    $fp = fopen($GLOBALS['phpgw_info']['server']['temp_dir'] . '/test.pdf','w');
    $pdf = PDF_open($fp);
    pdf_set_info_author($pdf, "Inventory for PHPGroupware");
    PDF_set_info_title($pdf, "Delivery");
    pdf_set_info_creator($pdf, "Inventory for PHPGroupware");
    pdf_set_info_subject($pdf, "Delivery");

    PDF_begin_page($pdf, 595, 842);
    PDF_add_outline($pdf, lang("Page")." 1");

    pdf_set_text_rendering($pdf, 0);

    $d = CreateObject('phpgwapi.contacts');
    $delivery->createAddress($pdf,$logo);

    $delivery->createBody($pdf);

    $delivery->createFooter($pdf);

    PDF_end_page($pdf);
    PDF_close($pdf);
    fclose($fp);

    $fp = fopen($GLOBALS['phpgw_info']['server']['temp_dir'] . '/test.pdf','r');
    header("Content-type: application/pdf");
    fpassthru($fp);
?>
