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
		'currentapp'	=> 'manual'
	);
	$phpgw_info['flags'] = $phpgw_flags;
	include('../../../header.inc.php');
	$font = $phpgw_info['theme']['font'];
?>
<img src="<?php echo $phpgw->common->image('calendar','navbar.gif'); ?>" border="0">
<font face="<?php echo $font; ?>" size="2"><p/>
Hakutoiminnolla varustetu p�iv�-, viikko- ja kuukausikalenteri /
aikataulusovellus joka muistuttaa t�rkeist� tapahtumista.<br/>
<ul><li><b>Tapahtuman lis��minen:</b> <img src="<?php echo $phpgw->common->image('calendar','new.gif'); ?>"><br/>
Lis�t�ksesi uuden tapahtuman itsellesi / ryhm�llesi, napsauta yll� olevan
n�k�ist� kuvaketta. N�yt�lle avautuu lomake, johon voit sy�tt�� tarvittavat
tiedot.</li><p/>
<table width="80%">
<td bgcolor="#ccddeb" width="50%" valign="top">
<font face="<?php echo $font ?>" size="2">
Lyhyt kuvaus:<br/>
Kuvaus:<br/>
P�iv�:<br/>
Aika:<br/>
Kesto:<br/>
T�rkeys:<br/>
N�kyvyys:</td>
<td bgcolor="#ccddeb" width="50%" valign="top">
<font face="<?php echo $font; ?>" size="2">
Ryhm�:<br/>
Osallistujat:<br/>
Toistuvuus:<br/>
Viimeisen kerran:<br/>
Jakso:</td></table>
T�yt� vain kent�t ja napsauta L�het�.</ul><br/>
<b>Huomaa:</b>
Kuten muidenkin sovellusten kohdalla, voit antaa tiedon
k�ytt�oikeudeksi Yksityinen, Oma ryhm� tai Kaikki.<p/></font>
<?php $phpgw->common->phpgw_footer(); ?>
