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
<img src="<?php echo $phpgw->common->image('addressbook','navbar.gif'); ?>" border="0">
<font face="<?php echo $font ?>" size="2"><p/>
Hakutoiminnolla varustettu osoitekirja yhteystietojen tallentamiseen.
<ul><li><b>Lis��:</b><br/>
Napsauta lis�� -painiketta ja n�yt�lle avautuu lomake, jossa on seuraavat kent�t:
<table width="80%">
<td bgcolor="#ccddeb" width="50%" valign="top">
<font face="<?php echo $font; ?>" size="2">
Sukunimi:<br/>
E-Mail:<br/>
Kotinumero:<br/>
Ty�numero:<br/>
Matkapuhelin:<br/>
Katuosoite:<br/>
Kaupunki:<br/>
Osavaltio:<br/>
Postinumero:<br/>
K�ytt�oikeus:<br/>
Ryhm�n asetukset:<br/>
Muuta:</td>
<td bgcolor="#ccddeb" width="50%" valign="top">
<font face="<?php echo $font; ?>" size="2">
Etunimi:<br/>
Yritys:<br/>
Fax:<br/>
Hakulaite:<br/>
Muu numero:<br/>
Syntym�p�iv�:</td></table>
...ja paljon muita.
T�yt� tiedot kenttiin ja napsauta OK.</li><p/></ul>
Tiedon k�ytt�oikeus voidaan rajoittaa yksityiseksi, mik� ohittaa ACL:n
asetuksen. Osoitekirjan asetuksista voit antaa muille k�ytt�jille
oikeuden selata, muokata tai jopa poistaa tallentamiasi tietoja.<p/>
<?php $phpgw->common->phpgw_footer(); ?>
