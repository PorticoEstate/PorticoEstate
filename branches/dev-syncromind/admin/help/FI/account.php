<?php
  /**************************************************************************\
  * phpGroupWare - User manual                                               *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id$ */

	$phpgw_flags = Array(
		'currentapp'	=> 'manual',
		'admin_header'	=> True,
	);
	$phpgw_info['flags'] = $phpgw_flags;
	include('../../../header.inc.php');
	$appname = 'admin';
?>
<img src="<?php echo $phpgw->common->image($appname,'navbar.gif'); ?>" border=0>
<font face="<?php echo $phpgw_info['theme']['font']; ?>" size="2"><p/>
T�m� sovellus on yleens� vain j�rjestelm�n p��k�ytt�j�n k�ytett�viss�.
Sovelluksella hallitaan kaikkia sovelluksia, k�ytt�ji�, ryhmi� ja istuntojen
lokeja.
<ul>
<li><b>K�ytt�jien hallinta:</b><p/>
<i>K�ytt�j�tunnukset:</i><br/>
Toiminnolla voit lis�t�, muuttaa ja poistaa k�ytt�j�tunnuksia sek� asettaa
ryhm�t joihin tunnus kuuluu ja mihin sovelluksiin k�ytt�j�ll� on k�ytt�oikeus.<p/>
<i>K�ytt�j�ryhm�t:</i><br/>
Toiminnolla voit lis�t�, muuttaa ja poistaa k�ytt�j�ryhmi�.<p/>
</ul></font>
