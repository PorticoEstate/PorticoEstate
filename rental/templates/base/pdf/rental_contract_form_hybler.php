<?php 
$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
$valuta_prefix = isset($config->config_data['currency_prefix']) ? $config->config_data['currency_prefix'] : '';
$valuta_suffix = isset($config->config_data['currency_suffix']) ? $config->config_data['currency_suffix'] : '';
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
	<dd><?php echo $contract_party->get_first_name()." ". $contract_party->get_last_name();?></dd>
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
<?php
foreach ($price_items as $item)
{
	?>
	<tr>
		<td width="80%"><?php echo $item->get_title();?></td>
		<td>Kr.:</td>
		<td align="right"><?php  echo $valuta_prefix; ?> &nbsp; <?php echo number_format($item->get_total_price()/12,2,',',' '); ?> &nbsp; <?php  echo $valuta_suffix; ?></td>
		<td>Pr.mnd.</td>
	</tr>

	<?php
}
?>
</table>
</div>


<div class="one_column">
<p>Merknader: <strong>Boligen (hybelen) skal ved flytting være ryddet og rengjort.</strong></p>
<textarea rows="3" cols=""></textarea>
<br />
</div>

<div class="one_column">
<p>Dato: <?php echo date($date_format, time());?></p>
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
