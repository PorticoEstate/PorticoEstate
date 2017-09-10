<?php
/***
 * class PHP odt2xhtml : file config
 * Copyright (C) 2006  Stephane HUC
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * Contact information:
 *   Stephane HUC
 *   <devs@stephane-huc.net>
 *
 ***/
define('ODT2XHTML_FILE_ODT','odt2xhtml.odt');	// name file with extension .odt, .ott or .sxw, .stw!
define('ODT2XHTML_FILE_CSS',0);	// to obtain css in file .css
define('ODT2XHTML_FRONTEND','/');	// directory where file odt to converse *** NOT RUN WITH PHP_CLI ***
define('ODT2XHTML_TITLE', 'element_title');	// file_name or element_title
define('ODT2XHTML_PUB', ' :: converted by Odt2Xhtml in PHP5 :: http://odt2xhtml.eu.org');	// message publicis

/*** NOT TOUCH ***/
define('ODT2XHTML_DEBUG',0);
define('ODT2XHTML_PHPCLI',0);	// to use with PHP CLI *** USE WITH PRECAUTION, @ YOURS RISKS ***