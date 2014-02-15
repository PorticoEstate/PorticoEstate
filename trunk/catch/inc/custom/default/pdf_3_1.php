<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
 	* @version $Id$
	*/

	/**
	 * Custom class for catch_3_1
	 *
	 */

	class pdf_3_1
	{
		
		var $preview = false;


		public function __construct()
		{


		}
		

		/**
		 * Produce the document for a specified record
		 * @param type $id
		 * @return string document
		 */
		public function get_document($id = 0, $_duplicate='')
		{
			if(!$id)
			{
				return false;
			}

			$sql = "SELECT * FROM fm_catch_3_1 WHERE id ='{$id}'";
			$GLOBALS['phpgw']->db->query($sql,__LINE__,__FILE__);
			$GLOBALS['phpgw']->db->next_record();
			$values = $GLOBALS['phpgw']->db->Record;

//_debug_array($values);
//die();

			$config				= CreateObject('phpgwapi.config','property');

			$config->read();

			$pdf = CreateObject('phpgwapi.pdf');
			$pdf -> ezSetMargins(50,70,50,50);
			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica.afm');

			// put a line top and bottom on all the pages
			$all = $pdf->openObject();
			$pdf->saveState();


			if(isset($config->config_data['order_logo']) && $config->config_data['order_logo'])
			{
				$pdf->addJpegFromFile($config->config_data['order_logo'],
					40,
					800,
					isset($config->config_data['order_logo_width']) && $config->config_data['order_logo_width'] ? $config->config_data['order_logo_width'] : 80
				);
			}

			$pdf->setStrokeColor(0,0,0,1);
			$pdf->line(20,40,578,40);
			//	$pdf->line(20,820,578,820);
			//	$pdf->addText(50,823,6,lang('order'));
			$pdf->addText(50,28,6,$config->config_data['org_name']);
			$pdf->addText(300,28,6,$date);

			if($_duplicate)
			{
				$pdf->setColor(1,0,0);
				$pdf->addText(50,400,30,"Dette er en duplikat av {$_duplicate}",-10);
				$pdf->addText(50,350,30," - og er slettet!",-10);
			}

			$pdf->restoreState();
			$pdf->closeObject();
			// note that object can be told to appear on just odd or even pages by changing 'all' to 'odd'
			// or 'even'.
			$pdf->addObject($all,'all');

			//			$pdf->ezSetDy(-100);

			$pdf->ezStartPageNumbers(500,28,6,'right','{PAGENUM} ' . lang('of') . ' {TOTALPAGENUM}',1);

			$data = array
			(
				array
				(
					'col1'=>"{$config->config_data['org_name']}\n\nOrg.nr: {$config->config_data['org_unit_id']}",
					'col2'=>'MELDING OM INNFLYTTING',
					'col3'=> 'Melding Nr:' . "\n\n{$id}"
				)
			);		

			$pdf->ezTable($data,array('col1'=>'','col2'=>'','col3'=>''),''
				,array('showHeadings'=>0,'shaded'=>0,'xPos'=>0
				,'xOrientation'=>'right','width'=>500
				,'cols'=>array
				(
					'col1'=>array('justification'=>'right','width'=>200, 'justification'=>'left'),
					'col2'=>array('justification'=>'right','width'=>100, 'justification'=>'center'),
					'col3'=>array('justification'=>'right','width'=>200),
				)

			));

			$address = 'Tildelt Leilighet:';
			$address_element = execMethod('property.botts.get_address_element', $values['location_code']);
			foreach($address_element as $entry)
			{
				$address .= "\n   {$entry['text']}: {$entry['value']}";
			}

			$text = "Kontrakt: {$values['kontraktsnummer']}";
			$text .= "\n\nLeietaker:";
			$text .= "{$values['navn_leietaker']}";
			$text .= "\n\n{$address}";
			$text .= "\n\nFødselsdato:";
			$text .= " {$values['foedselsdato']}";
			$text .= "\n\nAvd:";
			$text .= " {$values['avdeling']}";
			$text .= "\n\nTlf:";
			$text .= " {$values['telefonnummer']}";
			$text .= "\n\nInnbetaling av leie:";
			$text .= " {$values['innbetaling_av_leie']}";
			$text .= "\n\nFaktura sendes til:";
			$text .= " {$values['faktura_sendes_til']}";
			$text .= "\n\nFakturaadresse:";
			$text .= " {$values['fakturaadresse']}";
			$text .= "\n\nKostnadssted:";
			$text .= " {$values['kostnadssted']}";
			$text .= "\n\nFirmaadresse:";
			$text .= " {$values['firmaadresse']}\n\n";

			$text .= <<<TXT
1. Innflyttingsdato: {$values['innflyttingsdato']}
Boligtype: {$values['boligtype']}

2. Utflyttingsdato: {$values['utflyttingsdato']}

3. Utlevert antall nøkler: {$values['utlevert_ant_nokler']}
Boligareal (m2): {$values['boligareal']}

4. Langtidsleie:
Husleien er ved kontraktsinngåelsen fastsatt til kr {$values['husleie_pr_mnd']} pr mnd og kan endres iht konsumprisindeksen tidligst et år etter at sist leiefastsetting ble satt i verk.

Det innbetales forskuddsvis leie (kr): {$values['innbetaling_forskudd']}

5. Kortidsleie:
Antall leiedøgn: {$values['antall_leiedoegn']}
Klargjøring boenheter (kr): {$values['klargjoering_av_boenhet']}
Leie pr døgn (kr): {$values['leie_pr_doegn']}
Sengetøy/håndduker kr/døgn: {$values['sengetoey_handduker']}
Samlet leie ved korttidsleie er fastsatt til kr: {$values['samlet_korttidsleie']}

6. Strømab registreres på: {$values['strommaaler_registreres_paa']}

7. Innbo: {$values['innbo']}
Målerstand: {$values['maalerstand_kwh']}
Målernr: {$values['maalernummer']}
Annet innbo: {$values['annet_innbo']}
Vedlagt inventarliste: {$values['inventarliste_vedlagt_kontrakt']}

8. Boligstandard ved innflytting: {$values['bolig_standard_ved_innflytting']}

9. Tilstand spesielt: {$values['tilstand_spesielt']}

Merknader:  {$values['merknader_tilstand']}

10. Jeg er kjent med at skade på leiligheten, mangler, dårlig renhold o.l. ved utflytting blir belastet meg som leietaker

Dato:
TXT;
	

			$pdf->ezSetDy(-20);
			$pdf->ezText($text,12);
			$pdf->ezSetDy(-40);


			$data = array
				(
					array('col1'=>"Sign leietaker:\n\n\n{$values['navn_leietaker']}",'col2' => "Boligforvalter:\n\n\n{$values['boligforvalter']}"),
				);		

			$pdf->ezTable($data,array('col1'=>'','col2'=>''),''
				,array('showHeadings'=>0,'shaded'=>0,'xPos'=>0
				,'xOrientation'=>'right','width'=>500,'showLines'=> 0
				,'cols'=>array
				(
					'col1' =>array('justification' => 'right', 'width' => 250, 'justification' => 'left'),
					'col2' => array('justification' => 'right', 'width' => 250, 'justification' => 'left'),
				)

			));

			$document= $pdf->ezOutput();

			if($this->preview)
			{
				$pdf->print_pdf($document,"NLSH_melding_om_innflytting_{$id}");
			}
			else
			{
				return $document;
			}

		}

	}