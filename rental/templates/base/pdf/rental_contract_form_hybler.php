<?php 
$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
?>
<style>
<?php include "css/contract.css"?>
</style>


<img src="http://www.nordlandssykehuset.no/getfile.php/NLSH_bilde%20og%20filarkiv/Internett/NLSH_logo_siste.jpg%20%28352x58%29.jpg" alt="Nordlanssykehuset logo" />
<h1>Melding om inn/utflytting - Hybler</h1>

<form action="" method="post">
<div class="two_column">

<dl class="left_column">
	<dt><span class="checkbox_bg"><input type="checkbox" /></span>&nbsp Innflytting</dt>
	<dd>&nbsp</dd>
	<dt>Navn:</dt>
	<dd><?php echo $contract->get_party_name_as_list();?></dd>
	<dt>Fnr.:</dt>
	<dd><?php echo $contract_party->get_identifier();?></dd>
	<dt>Adresse:</dt>
	<dd><?php echo $contract_party->get_address_1().", ".$contract_party->get_address_2().", ".$contract_party->get_postal_code(). " ".$contract_party->get_place()  ;?></dd>
	<dt>Tildelt bolig:</dt>
	<dd><?php echo $composite->get_name();?></dd>
</dl>


<dl class="right_column">
	<dt><span class="checkbox_bg"><input type="checkbox" /></span>&nbsp Utflytting</dt>
	<dd>&nbsp</dd>
	<dt>Stilling:</dt>
	<dd><?php echo $contract_party->get_title();?></dd>
	<dt>Avd.:</dt>
	<dd><?php echo $contract_party->get_department();?></dd>
	<dt>Innflytting-dato:</dt>
	<dd><?php echo date($date_format, $contract_dates->get_start_date());?></dd>
	<dt>Utflytting-dato:</dt>
	<dd><?php echo date($date_format, $contract_dates->get_end_date());?></dd>
</dl>
</div>


<div class="one_column">
<dl class="checkbox_list">
	<dt><span class="checkbox_bg"><input type="checkbox"  /></span></dt>
	<dd>Lever nøkler etter utflytting til vaktmesters postkasse i postkasserommet</dd>
	<dt><span class="checkbox_bg"><input type="checkbox"  /></span></dt>
	<dd>Underrett vaktmester vedr. eventuelle mangler/skader</dd>
	<dt><span class="checkbox_bg"><input type="checkbox"  /></span></dt>
	<dd>Har du tjenestetelefon – meld fra til personalkontoret (ikke Telenor)</dd>
</dl>
</div>

<div class="one_column">

<table>
	<tr>
		<td>Husleie uten møbler</td>
		<td>Kr.:</td>
		<td>[hentes fra db]</td>
		<td>Pr.mnd.</td>
	</tr>
	<tr>
		<td>Husleie med møbler</td>
		<td>Kr.:</td>
		<td>[hentes fra db]</td>
		<td>Pr.mnd.</td>
	</tr>
	<tr>
		<td>Utstyr</td>
		<td>Kr.:</td>
		<td>[hentes fra db]</td>
		<td>Pr.mnd.</td>
	</tr>
	<tr>
		<td>Sengetøy/Håndduker</td>
		<td>Kr.:</td>
		<td>[hentes fra db]</td>
		<td>Pr.mnd.</td>
	</tr>
	<tr>
		<td>Husleie</td>
		<td>Kr.:</td>
		<td>[hentes fra db]</td>
		<td>Pr.mnd.</td>
	</tr>
	<tr>
		<td>1 mnd forskudd</td>
		<td>Kr.:</td>
		<td>[hentes fra db]</td>
		<td>Pr.mnd.</td>
	</tr>
</table>
</div>


<div class="one_column">
<p>Merknader: <strong>Boligen (hybelen) skal ved flytting være ryddet og rengjort.</strong></p>
<textarea rows="3" cols=""></textarea>
<br />
</div>

<div class="one_column">
<p>Dato:</p>
<table>
	<tr>
		<td align="center">
		<p class="sign">Underskrift leietaker</p>
		</td>
		<td align="center">
		<p class="sign">Underskrift vaktmester</p>
		</td>
	</tr>
</table>
</div>


<p>Kopi:</p>
<p><span class="checkbox_bg"><input type="checkbox"  /></span>Personalkontoret</p>
<p><span class="checkbox_bg"><input type="checkbox"  /></span>Lønningskontoret</p>

<input type="submit" value="Lag pdf"> 
</form>
