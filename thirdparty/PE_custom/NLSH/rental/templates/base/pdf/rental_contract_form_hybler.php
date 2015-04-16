<?php 
//
$template_name = "Korttidskontrakt";


if(!$get_template_config){
if (isset($_POST['preview']))
{
ob_start();
}
$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
$valuta_prefix = isset($config->config_data['currency_prefix']) ? $config->config_data['currency_prefix'] : '';
$valuta_suffix = isset($config->config_data['currency_suffix']) ? $config->config_data['currency_suffix'] : '';
?>
<style>
<?php include "css/contract.css"?>
</style>
<div class="contract">
<img src="http://www.nordlandssykehuset.no/getfile.php/NLSH_bilde%20og%20filarkiv/Internett/NLSH_logo_siste.jpg%20%28352x58%29.jpg" alt="Nordlanssykehuset logo" />
<h1>Melding om inn/utflytting - Hybler</h1>
<h3>Kontraktsnummer: <?php echo $contract->get_old_contract_id();?></h3>
<h3>Type: <?php echo lang($contract->get_contract_type_title()).' / '.lang(rental_socontract::get_instance()->get_contract_type_label($contract->get_contract_type_id()));?></h3>

<form action="" method="post">
<?php
$disabled="";
$checkb_in_value = true;

if (isset($_POST['preview']) )
{
	$disabled = 'disabled="disabled"';
}

if(isset($_POST['checkb_in'])){?><input type="hidden" name="checkb_in_hidden"  /><?php }
if(isset($_POST['checkb_out'])){?><input type="hidden" name="checkb_out_hidden"  /><?php }
if(isset($_POST['checkb_keys'])){?><input type="hidden" name="checkb_keys_hidden"  /><?php }
if(isset($_POST['checkb_janitor'])){?><input type="hidden" name="checkb_janitor_hidden"  /><?php }
if(isset($_POST['checkb_phone'])){?><input type="hidden" name="checkb_phone_hidden"  /><?php }
if(isset($_POST['checkb_HR'])){?><input type="hidden" name="checkb_HR_hidden"  /><?php }
if(isset($_POST['checkb_payroll_office'])){?><input type="hidden" name="checkb_payroll_office_hidden"  /><?php }

$termin_name = str_replace("lig", "", $contract->get_term_id_title());
$termin_name = str_replace("vis", "", $termin_name);



?>

<div class="two_column">

<dl class="left_column">
	<dt><input type="checkbox" name="checkb_in" <?php echo $disabled; if(isset($_POST['checkb_in']) || isset($_POST['checkb_in_hidden'])) {echo 'checked="checked"';}?> />&nbsp Innflytting</dt>
	<dd>&nbsp</dd>
	<dt>Navn:</dt>
	<dd><?php // echo $contract_party->get_first_name()." ". $contract_party->get_last_name();?>
		<?php echo $contract->get_party_name();?>
	</dd>
	<dt>Fnr.:</dt>
	<dd><?php echo $contract_party->get_identifier();?>&nbsp;</dd>
	<dt>Adresse:</dt>
	<dd><?php echo $contract_party->get_address_1();
	if($contract_party->get_address_2())
	{
		echo ", " .$contract_party->get_address_2().", ";
	}
	echo $contract_party->get_postal_code(). " ".$contract_party->get_place()  ;
	?>
	</dd>
	<dt>Tildelt bolig:</dt>
	<dd>
		<?php
			$unit = $units[0];
			$location_code = $unit->get_location()->get_location_code();
			$location = explode('-', $location_code);
			$loc1 = (int) $location[0];

			if($loc1 > 8006 && $loc1 < 8100)
			{
				$municipal = "Bodø";
			}
			else if($loc1 > 8499 && $loc1 < 8600)
			{
				$municipal = "Hadsel";
			}
			else if($loc1 > 8599 && $loc1 < 8700)
			{
				$municipal = "Gravdal";
			}

			echo $unit->get_location()->get_location_code() . ' Adresse:' . $unit->get_location()->get_address_1() . "i {$municipal} kommune.";
		?>&nbsp;</dd>
	<dt>E-post.:</dt>
	<dd>
		<?php
			$parties = rental_soparty::get_instance()->get(null, null, null, null, null, null, array('contract_id' => $contract->get_id()));
			$party_email = array();
			foreach($parties as $party)
			{
				if($party->get_email())
				{
					$party_email[] = $party->get_email();
				}
			}
			if($party_email)
			{
				echo implode(", ", $party_email);
			}
		?>
	</dd>

</dl>


<dl class="right_column">
	<dt><input type="checkbox" name="checkb_out" <?php echo $disabled; if(isset($_POST['checkb_out'])|| isset($_POST['checkb_out_hidden'])) {echo 'checked="checked"';}?>/>&nbsp Utflytting</dt>
	<dd>&nbsp</dd>
	<dt>Stilling:</dt>
	<dd><?php echo $contract_party->get_title();?>&nbsp;</dd>
	<dt>Avd.:</dt>
	<dd><?php echo $contract_party->get_department();?>&nbsp;</dd>
	<dt>Innflytting-dato:</dt>
	<dd><?php echo date($date_format, $contract_dates->get_start_date());?>&nbsp;</dd>
	<dt>Utflytting-dato:</dt>
	<dd><?php echo date($date_format, $contract_dates->get_end_date());?>&nbsp;</dd>
</dl>
</div>


<div class="one_column">
<dl class="checkbox_list">
	<dt><input type="checkbox" name="checkb_keys" <?php echo $disabled; if(isset($_POST['checkb_keys']) || isset($_POST['checkb_keys_hidden'])) {echo 'checked="checked"';}?> /></dt>
	<dd>Lever nøkler etter utflytting til vaktmesters postkasse i postkasserommet</dd>
	<dt><input type="checkbox" name="checkb_janitor" <?php echo $disabled; if(isset($_POST['checkb_janitor']) || isset($_POST['checkb_janitor_hidden'])) {echo 'checked="checked"';}?> /></dt>
	<dd>Underrett vaktmester vedr. eventuelle mangler/skader</dd>
	<dt><input type="checkbox" name="checkb_phone" <?php echo $disabled; if(isset($_POST['checkb_phone']) || isset($_POST['checkb_phone_hidden'])) {echo 'checked="checked"';}?> /></dt>
	<dd>Har du tjenestetelefon – meld fra til personalkontoret (ikke Telenor)</dd>
</dl>
</div>

<?php if(sizeof($one_time_price_items)>0){ ?>
<div class="one_column">

<table border="0">
<tr><td><strong>Engangsbeløp</strong></td></tr>
<?php
foreach ($one_time_price_items as $item)
{
	?>
	<tr>
		<td><?php echo $item->get_title();?>: <?php echo $item->get_count();?> stk. &aacute  kr. <?php  echo $valuta_prefix; ?> &nbsp; <?php echo number_format($item->get_price(),2,',',' '); ?> &nbsp; <?php  echo $valuta_suffix; ?> </td>
		
		
		<td align="right">Kr.: <?php  echo $valuta_prefix; ?> &nbsp; <?php echo number_format($item->get_total_price(),2,',',' '); ?> &nbsp; <?php  echo $valuta_suffix; ?></td>
	</tr>

	<?php	
}
?>
</table>
</div>
<?php }

?>


<?php if(sizeof($termin_price_items)>0){ ?>
<div class="one_column">

<table border="0">
<tr><td><strong>Fastbeløp pr. <?php echo  strtolower($termin_name);?> </strong></td></tr>
<?php

foreach ($termin_price_items as $item)

{
	?>
	<tr>
		<td ><?php echo $item->get_title();?></td>
		
		<td align="right"> Kr.:<?php  echo $valuta_prefix; ?>&nbsp;<?php echo number_format(($item->get_total_price()/12)*$months,2,',',' '); ?>&nbsp;<?php  echo $valuta_suffix; ?>&nbsp;Pr.&nbsp;<?php echo strtolower($termin_name)?></td>
		
	</tr>

	<?php
}
?>
</table>
</div>
<?php }?>

<div class="one_column">
<p>Merknader: <strong>Boligen (hybelen) skal ved flytting være ryddet og rengjort.</strong></p>
<?php if (isset($_POST['preview']) )
{
	?>
<p><?php echo $_POST['notes']?></p>
<input type="hidden" name="notes" value="<?php echo (htmlspecialchars($_POST['notes'],ENT_QUOTES, UTF-8,false));?>" />
	<?php
}
else
{
	?> <textarea rows="3" cols="" name="notes"><?php echo (htmlspecialchars($_POST['notes'],ENT_QUOTES, UTF-8,false));?></textarea> <?php
}
?> <br />
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
<p><input type="checkbox" name="checkb_HR" <?php echo $disabled; if(isset($_POST['checkb_HR']) || isset($_POST['checkb_HR_hidden'])) {echo 'checked="checked"';}?> />Personalkontoret</p>
<p><input type="checkbox" name="checkb_payroll_office"<?php echo $disabled; if(isset($_POST['checkb_payroll_office']) || isset($_POST['checkb_payroll_office_hidden'])) {echo 'checked="checked"';}?> />Lønningskontoret</p>


<?php if (isset($_POST['preview'])  ){ 
$HtmlCode= ob_get_contents();
ob_end_flush();

$_SESSION['contract_html'] = $HtmlCode;
	
	?>
<input type="submit" value="Rediger" name="edit"> 
</form>

<form action="<?php echo(html_entity_decode(self::link(array('menuaction' => 'rental.uimakepdf.makePDF', 'id' => $contract->get_id(), 'initial_load' => 'no'))));?>" method="post">
<input type="submit" value="Lagre som PDF" name="make_PDF" /> 

</form>
<?php


}else{?>

<input type="submit" value="Forhåndsvis" name="preview"> </form>
<?php }?>

</div>

<?php }
