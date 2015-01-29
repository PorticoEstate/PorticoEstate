<?php 

$template_name = "Leie av personalbolig";
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
<!--<img src="http://www.nordlandssykehuset.no/getfile.php/NLSH_bilde%20og%20filarkiv/Internett/NLSH_logo_siste.jpg%20%28352x58%29.jpg" alt="Nordlanssykehuset logo" />-->

<table>
	<tr>
		<td>
			<h1>LEIEKONTRAKT FOR PERSONALBOLIG</h1>
		</td>
		<td align ='right'>
			<img src="http://portico.nlsh.no/portico/logovariant1.png" alt="Nordlanssykehuset logo" height="39" width="375" />
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<h3>Kontraktsnummer: <?php echo $contract->get_old_contract_id();?></h3>
		</td>
	</tr>
</table>
<form action="" method="post">
<?php
$disabled="";
$color_checkbox = "checkbox_bg";
$checkb_in_value = true;

if (isset($_POST['preview']))
{
	$disabled = 'disabled="disabled"';
	$color_checkbox = "";
}

if(isset($_POST['checkb_gab'])){?><input type="hidden" name="checkb_gab_hidden"  /><?php }
if(isset($_POST['checkb_unit'])){?><input type="hidden" name="checkb_unit_hidden"  /><?php }
if(isset($_POST['checkb_kitchen'])){?><input type="hidden" name="checkb_kitchen_hidden"  /><?php }
if(isset($_POST['checkb_bath'])){?><input type="hidden" name="checkb_bath_hidden"  /><?php }
if(isset($_POST['checkb_other'])){?><input type="hidden" name="checkb_other_hidden"  /><?php }
if(isset($_POST['checkb_outer_space'])){?><input type="hidden" name="checkb_outer_space_hidden"  /><?php }
if(isset($_POST['checkb_limitations'])){?><input type="hidden" name="checkb_limitations_hidden"  /><?php }
if(isset($_POST['checkb_duration'])){?><input type="hidden" name="checkb_duration_hidden"  /><?php }
if(isset($_POST['checkb_type'])){?><input type="hidden" name="checkb_type_hidden"  /><?php }
if(isset($_POST['checkb_termination'])){?><input type="hidden" name="checkb_termination_hidden"  /><?php }
if(isset($_POST['checkb_termination2'])){?><input type="hidden" name="checkb_termination2_hidden"  /><?php }
if(isset($_POST['checkb_electricity'])){?><input type="hidden" name="checkb_electricity_hidden" value ="<?php echo $_POST['checkb_electricity'] ?>"/><?php }
if(isset($_POST['checkb_sublease_allowed'])){?><input type="hidden" name="checkb_sublease_allowed_hidden"  /><?php }
if(isset($_POST['checkb_sublease_disallowed'])){?><input type="hidden" name="checkb_sublease_disallowed_hidden"  /><?php }
if(isset($_POST['checkb_animals'])){?><input type="hidden" name="checkb_animals_hidden" value ="<?php echo $_POST['checkb_animals'] ?>"/><?php }
if(isset($_POST['checkb_remarks1'])){?><input type="hidden" name="checkb_remarks1_hidden"  /><?php }
if(isset($_POST['checkb_remarks2'])){?><input type="hidden" name="checkb_remarks2_hidden"  /><?php }
if(isset($_POST['checkb_remarks3'])){?><input type="hidden" name="checkb_remarks3_hidden"  /><?php }
if(isset($_POST['checkb_remarks4'])){?><input type="hidden" name="checkb_remarks4_hidden"  /><?php }
if(isset($_POST['checkb_pay1'])){?><input type="hidden" name="checkb_pay1_hidden"  /><?php }
if(isset($_POST['checkb_pay2'])){?><input type="hidden" name="checkb_pay2_hidden"  /><?php }

$termin_name = str_replace("lig", "", $contract->get_term_id_title());
$termin_name = str_replace("vis", "", $termin_name);
?>

<table class="header">
	<tr>
		<th>Utleier</th>
		<th colspan="2" width="50%">Leier</th>
	</tr>
	<tr>
		<td>Nordlandssykehuset HF</td>
		<td bgcolor="#C0C0C0" width="120px">Navn:</td>
		<td><?php echo $contract_party->get_first_name()." ". $contract_party->get_last_name();?></td>
	</tr>
	<tr>
		<td>v/ Boligforvalter <?php echo $GLOBALS['phpgw']->accounts->get($contract->get_executive_officer_id())-> __toString();?></td>
		<?php
			$identifier = $contract_party->get_identifier();
			if (strlen($identifier) == 11)
			{
				echo '<td bgcolor="#C0C0C0">Fødselsdato:</td>';
				echo '<td>' . substr($identifier,0,6) . '</td>';
			}
			else
			{
				echo '<td bgcolor="#C0C0C0">Firma:</td>';
				echo "<td>{$identifier}</td>";
			}
		?>
	</tr>
	<tr>
		<td>Epost:  <?php
			switch($contract->get_responsibility_id())
			{
				case '3015':
					$epost = 'bolig.gravdal@nlsh.no';
					break;
				case '4034':
					$epost = 'bolig.stokmarknes@nlshl.no';
					break;
				case '4036':
					$epost = 'bolig.stokmarknes@nlshl.no';
					break;
				case '8018':
					$epost = 'bolig.bodo@nlsh.no';
					break;
				default:
					$epost = 'bolig.bodo@nlsh.no';
					break;
			}
			echo $epost;
			
			?>
		</td>
		<td bgcolor="#C0C0C0">Arbeidssted:</td>
		<td>
			<?php
				echo $contract_party->get_department();
			?>
		</td>
	</tr>
	<tr>
		<td></td>
		<td bgcolor="#C0C0C0">Adresse:</td>
		<td>
			<?php 
				if (isset($_POST['preview']) || isset($_POST['make_PDF'])){ 
					echo $_POST['address']?>
					<input type="hidden" name="address" value="<?php echo $_POST['address']?>" /> 
			<?php
				}else{
			?> 
					<input type="text" name="address" value="<?php echo $contract_party->get_address_1().", ".$contract_party->get_address_2();?>" /> 
			<?php
				}
			?>
		</td>
	</tr>
	<tr>
		<td></td>
		<td bgcolor="#C0C0C0">Postnr/Sted:</td>
		<td>
			<?php 
				if (isset($_POST['preview']) || isset($_POST['make_PDF'])){ 
					echo $_POST['postal_code']?>
					<input type="hidden" name="postal_code" value="<?php echo $_POST['postal_code']?>" /> 
			<?php
				}else{
			?> 
					<input type="text" name="postal_code" value="<?php echo $contract_party->get_postal_code()." ".$contract_party->get_place();?>" /> 
			<?php
				}
			?>
		</td>
	</tr>
</table>

<div class="section">
<dl class="section_header">
	<dt>1.</dt>
	<dd>Leieobjektet</dd>
</dl>


<dl class="checkbox_list">
	<dt>
	</dt>
	<?php
	
	foreach ($units as $unit){
	
	$gb = preg_split('/ /', $unit->get_location()->get_gab_id(), -1);
	if(!($gb[0]=="")){
	?><dt></dt>
	<dd>Leieforholdet gjelder for eiendom: G.nr. <?php echo $gb[0];?>  B.nr.  <?php echo $gb[2];?>  i Bodø kommune.</dd>
<?php }}?>
</dl>
</div>

<div class="section">
<dl class="section_header">
	<dt>2.</dt>
	<dd>Begrensning</dd>
</dl>
<dl class="checkbox_list">
	<dt><input type="checkbox" name="checkb_limitations" <?php echo $disabled; if(isset($_POST['checkb_limitations']) || isset($_POST['checkb_limitations_hidden'])) {echo 'checked="checked"';}?>  /></dt>
	<dd>Særskilt innskrenkning i leierett, leietaker har ikke rett til følgende:<br/>
	<?php if (isset($_POST['preview'])|| isset($_POST['make_PDF']) )
{
	?>
<p><?php echo $_POST['limitations']?></p>
<input type="hidden" name="limitations" value="<?php echo $_POST['limitations']?>" />
	<?php
}
else
{
	?> <textarea rows="3" cols="" name="limitations"><?php echo $_POST['limitations']?></textarea> <?php
}
?> 
	</dd>

</dl>
</div>


<div class="section">
<dl class="section_header">
	<dt>3.</dt>
	<dd>Kontrakten art og varighet</dd>
</dl>

	<p>
		Leieavtalen gjelder bolig i anledning ansettelse ved Nordlandssykehuset HF.<br/>
		Leieforholdet er inngått i forståelse mellom partene om å være et midlertidig tilbud
		knyttet til tiltredelse av stilling ved Nordlandssykehuset HF.<br/>
		Leieforholdet kan forlenges ved særskilt skriftlig avtale mellom partene.</br>
	</p>
	
	<p> Leieforholdet er tidsbestemt og starter den <?php echo date($date_format, $contract_dates->get_start_date());?> 
		og opphører uten oppsigelse den <?php echo date($date_format, $contract_dates->get_end_date());?> kl. 1200<br />
		<i>
			Leieavtalen kan uavhengig av gjeldende frist sies opp av begge parter ved en av partenes vesentlige mislighold av avtalen.
			Med vesentlig mislighold forstås samme vilkår som nevnt i Husleieloven § 9-9.
		</i>
	</p>
</div>
<div class="section">
<dl class="section_header">
	<dt>4.</dt>
	<dd>Leiesum</dd>
</dl>

<?php
foreach ($price_items as $item)
{
	if($item->get_title()=="Utleie"){
		?>
			<p>Leien er ved kontraktsinngåelse fastsatt til kr <?php  echo $valuta_prefix; ?> &nbsp; <?php echo number_format(($item->get_total_price()/12)*$months,2,',',' '); ?> &nbsp; <?php  echo $valuta_suffix; ?> pr. <?php echo  strtolower($termin_name);?></p><br/>
		<?php
	}
}?>
		<p>Leiesummen skal betales forskuddsvis hver mnd.<br/>
		Ved forsinket betaling skal det betales forsinkelsesrente etter lov om forsinket betaling av 17.des. 1976 nr. 100.<br/>
		Ved mislighold av leiebetaling samtykker leietaker i at utleier kan foreta direkte trekk av tilgodehavende husleie direkte av leietakers lønn.<br/>
		Husleie skal reguleres i takt med konsumprisindeksen hvert år. I tillegg kan utleier justere leie til enhver tid markedsleie for denne type bolig.</p>

</div>

<div class="section">
<dl class="section_header">
	<dt>5.</dt>
	<dd>trøm og brensel</dd>
</dl>

<dl class="checkbox_list">
	<dt><input type="radio" name="checkb_electricity" value = "included" <?php echo $disabled; if($_POST['checkb_electricity'] == "included"  || $_POST['checkb_electricity_hidden'] == "included") {echo 'checked';}?>  /></dt>
	<dd>Leien er <i>inklusiv</i> strøm.<br />

	<dt><input type="radio" name="checkb_electricity" value = "excluded" <?php echo $disabled; if((isset($_POST['checkb_electricity']) && $_POST['checkb_electricity'] == "excluded" ) || (isset($_POST['checkb_electricity_hidden']) && $_POST['checkb_electricity_hidden'] == "excluded")) {echo 'checked';}?>  /></dt>
	<dd>I tillegg til den avtalte månedlige husleie er leietaker personlig ansvarlig for strøm,	forsikring, oppvarming og avtalt vedlikehold av boligen/boligrommet.</dd>
</dl>

</div>

<div class="section">
<dl class="section_header">
	<dt>6.</dt>
	<dd>Utleiers plikter</dd>
</dl>
	<p>Utleier plikter å stille boligen til leiers disposisjon ved leieforholdets start.</p>
	<p>Boligen skal overleveres leietaker, ryddet, rengjort og forskriftsmessig stand.</p>
	<p>Utleier har i leieperioden ansvaret for at boligen er i forskriftsmessig stand, eller i samme stand som ved inngåelse av leieforholdet.
		Det henvises til husleielovens bestemmelser om dette.</p>
	<p>Utleier skal forestå vedlikehold av boligens ytre fasade.</p>
	<p>Likeså skal utleier vedlikeholde og eventuelt utbedre maling, tapet gulvbelegg innefor boligens vegger som skyldes elde og naturlig slitasje.</p>
	<p>For øvrig vises til husleielovens kap. 2 om overlevering og krav til husrommet.</p>
</div>

<div class="section">
<dl class="section_header">
	<dt>7.</dt>
	<dd>Leietakers vedlikeholdsplikt</dd>
</dl>
	<p>Leietaker skal ivareta daglig vask og vedlikehold av boligen. Skade påført boligen av leietaker kan utleier kreve utbedret av leietaker.
		For det tilfelle at leietaker ikke sørger for utbedring av påført skade kan utleier utbedre skaden(e) for leietakers regning.
		Ved signering av denne avtale samtykker leietaker at utleier kan foreta direkte trekk i lønn for nødvendige kostnader relatert til skadens omfang. </p>

<p>Leietaker kan ikke uten utleiers samtykke foreta forandringer i/av husrommet eller på eiendommen for øvrig jfr. husleieloven § 5-4, 2. ledd.</p>
</div>

<div class="section">
<dl class="section_header">
	<dt>8.</dt>
	<dd>Leietakers øvrige plikter</dd>
</dl>
<p>Leietaker forplikter seg å behandle boligen med tilbørlig aktsomhet og i samsvar med denne avtale og husleielovens kap.5.
	Leietaker forplikter seg til å gjøre seg godt kjent med gjeldende ordensregler og påbud som fastsatt av utleier for boligen.
	Leietaker er erstatningspliktig for all skade som påføres eiendommen av leietaker ved uaktsom bruk av eiendommen jfr. husleielovens § 5-8.</p>

<p>Leietaker plikter å foreta renhold av felles areal som trapper og trappeganger.</p>

<p>Utleier har rett til å gjennomføre nødvendig tilsyn av boligen. Leietaker plikter å stille boligen disponibel for utleier ved tilsyn av boligen.
	Likeså gjelder utleiers nødvendige vedlikehold av boligen.</p>
</div>

<div class="section">
<dl class="section_header">
	<dt>9.</dt>
	<dd>Forsikring</dd>
</dl>
<p>Leietaker innestår ved signering av denne avtale at nødvendige forsikringer er tegnet og aktive.
	Dokumentasjon for dette skal fremlegges ved signering av denne leieavtale eller senest inntil fem dager etter signering av denne leieavtale.</p>
	<p>Utleier holder bygning og fast inventar forsikret. </p>
</div>

<div class="section">
<dl class="section_header">
	<dt>10.</dt>
	<dd>Framleie</dd>
</dl>
	<p>Framleie av boligen er ikke tillatt.</p>

</div>

<div class="section">
<dl class="section_header">
	<dt>11.</dt>
	<dd>Dyrehold</dd>
</dl>
<dl class="checkbox_list">
	<dt><input type="radio" name="checkb_animals" value = "disallowed" <?php echo $disabled; if((isset($_POST['checkb_animals']) && $_POST['checkb_animals'] == "disallowed" ) || (isset($_POST['checkb_animals_hidden']) && $_POST['checkb_animals_hidden'] == "disallowed")) {echo 'checked="checked"';}?>  /></dt>
	<dd>Dyrehold er ikke tillatt, med mindre det er skriftlig avtalt.</dd>
	<dt><input type="radio" name="checkb_animals"  value = "allowed" <?php echo $disabled; if((isset($_POST['checkb_animals']) && $_POST['checkb_animals'] == "allowed" ) || (isset($_POST['checkb_animals_hidden']) && $_POST['checkb_animals_hidden'] == "allowed")) {echo 'checked="checked"';}?>  /></dt>
	<dd>Dyrehold er særskilt avtalt, ved at leier kan ha: <?php if (isset($_POST['preview']) || isset($_POST['make_PDF']))
	{
		?> <?php echo $_POST['animals']?> <input type="hidden" name="animals" value="<?php echo $_POST['animals']?>" /> <?php
	}
	else
	{
		?> <input type="text" name="animals" value="<?php echo $_POST['animals']?>" /> <?php
	}
?></dd>
</dl>
</div>
<div class="section">
<dl class="section_header">
	<dt>12</dt>
	<dd>Oppsigelse</dd>
</dl>
	<p>Leieforholdet er tidsbestemt og opphører uten ytterligere varsel fra utleier jfr. husleielovens § 9-2.</p>
</div>
<div class="section">
<dl class="section_header">
	<dt>13</dt>
	<dd>Leiers mislighold</dd>
</dl>
	<p>
		Ved vesentlig mislighold av leieavtalen kan utleier si opp leieavtalen i samsvar med husleieloven § 9-2, 2.ledd. For å forhindre vesentlig skade på bolig kan utleier si opp
		denne avtale med øyeblikkelig virkning hvor dette antas påkrevd for å begrense/hindre ytterligere skade.
	</p>
	<br/>
	<p>
		Leier samtykker i at ved alminnelig mislighold av denne avtale kan leietaker kreve fraflytting ved tvang hvor avtalt husleie inkl., omkostninger ikke er betalt innen fjorten – 14 – dager etter skriftlig varsel om dette. For øvrig vises til husleielovens § 9-7 om dette. Nevnte gjelder også ved opphør av leieavtalen.
	</p>
</div>

<div class="section">
<dl class="section_header">
	<dt>14.</dt>
	<dd>Fraflytting</dd>
</dl>
	<p>Ved leieforholdets slutt plikter leietaker å overlevere eiendommen i rengjort stand. Eiendommen skal overleveres i samme stand som ved inngåelse av leieforholdet.</p>
	<p>Leietaker plikter å erstatte eventuelle avdekkede skader påført eiendommen i leieperioden. </p>
	<p>Utleier plikter å befare eiendommen ved overlevering av eiendommen. For det tilfelle at utleier ikke gjennomfører befaring av eiendommen taper utleier sin rett til å påberope eventuelle mangler/skader på eiendommen.
	</p>
</div>

<div class="section">

<table>
	<tr>
		<td colspan="2" align="center"><i>Kontrakt sendes pr e-post til leier, og lagres hos Nordlandssykehuset HF i ePhorte under kontraktsnummer.</i></td>
	</tr>
	<tr>
		<td colspan="2" ><br /></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><i>
			<?php
			switch($contract->get_responsibility_id())
			{
				case '3015':
					$lokasjon = 'Bodø';
					break;
				case '4034':
					$lokasjon = 'Bodø';
					break;
				case '4036':
					$lokasjon = 'Bodø';
					break;
				case '8018':
					$lokasjon = 'Bodø';
					break;
				default:
					$lokasjon = 'Bodø';
					break;
			}
			echo "{$lokasjon} den" . date($date_format, time());?></i></td>
	</tr>
	<tr>
		<th>Utleier</th>
		<th>Leier</th>
	</tr>
	<tr>
	<td align="center">Nordlandssykehuset v/boligseksjonen</td><td></td>
	</tr>
	<tr>
		<td align="center">
		<p class="sign"><?php echo $GLOBALS['phpgw']->accounts->get($contract->get_executive_officer_id())-> __toString();?><br />
		Boligforvalter</p>
		</td>
		<td align="center">
		<p class="sign"><?php echo $contract_party->get_first_name()." ". $contract_party->get_last_name();?><br />
		&nbsp</p>
		</td>
	</tr>
</table>
</div>
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
