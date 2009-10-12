<?php
/**
 * Logiciel : exemple d'utilisation de HTML2PDF
 * 
 * Convertisseur HTML => PDF, utilise fpdf de Olivier PLATHEY 
 * Distribué sous la licence GPL. 
 *
 * @author		Laurent MINGUET <webmaster@spipu.net>
 */
 	ob_start();
?>
<style type="text/css">
<!--
table
{
	padding: 0;
	border:	solid 1mm LawnGreen;
	font-size:	12pt;
}

td
{
	padding:	1mm;
	border: solid 1mm black;
	text-align:	center;
}
-->
</style>
<table cellspacing="4">
	<tr>
		<td>a A1</td>
		<td>aa A2</td>
		<td>aaa A3</td>
		<td>aaaa A4</td>
	</tr>
	<tr>
		<td rowspan="2">B1</td>
		<td style="font-size: 16pt">B2</td>
		<td colspan="2">B3</td>
	</tr>
	<tr>
		<td>C1</td>
		<td>C2</td>
		<td>C3</td>
	</tr>
	<tr>
		<td colspan="2">D1</td>
		<td colspan="2">D2</td>
	</tr>
</table>
<br>
<table>
	<tr>
		<td colspan="2">CoucouCoucou !</td>
		<td>B</td>
		<td>CC</td>
	</tr>
	<tr>
		<td>AA</td>
		<td colspan="2">CoucouCoucou !</td>
		<td>CC</td>
	</tr>
	<tr>
		<td>AA</td>
		<td>B</td>
		<td colspan="2">CoucouCoucou !</td>
	</tr>
</table>
<br>
<table>
	<tr>
		<td>AA</td>
		<td>AA</td>
		<td>AA</td>
		<td rowspan="2">AA</td>
	</tr>
	<tr>
		<td>AA</td>
		<td rowspan="2" colspan="2" >CoucouCoucou !</td>
	</tr>
	<tr>
		<td>AA</td>
		<td>CC</td>
	</tr>
	<tr>
		<td colspan="2">D1</td>
		<td colspan="2">D2</td>
	</tr>
</table>
<?php
	$content = ob_get_clean();
	require_once(dirname(__FILE__).'/../html2pdf.class.php');
	$pdf = new HTML2PDF('P','A4', 'fr');
	$pdf->WriteHTML($content, isset($_GET['vuehtml']));
	$pdf->Output();
?>