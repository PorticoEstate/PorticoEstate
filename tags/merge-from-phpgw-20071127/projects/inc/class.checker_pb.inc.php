<?php
require_once 'class.checker.inc.php';

class checker_pb extends checker
{
	function checkProjectNr($pNr = '')
  {
  	if(!parent::checkProjectNr($pNr))
  	{
  		return false;
  	}

  	/* Dokumentation regex
		 * 
	 
		^[PSIDV] // Typ; Kein A, da keine Vorplanung im Tool, wegen Projektleichen.
		[0-9]{2} // Jahr
		[0][1-6] // Niederlassung
	
		// Redundanter Auftragstyp als Nummer kodiert
		(
	  (?<=^A[0-9]{4})0 | 
	  (?<=^P[0-9]{4})1 | 
	  (?<=^I[0-9]{4})7 | 
	  (?<=^V[0-9]{4})5 | 
	  (?<=^S[0-9]{4})8 | 
	  (?<=^D[0-9]{4})9 
		)
	
		[0-9]{5} // Kundennummer, Laufende Projektnummer
	
		*/
		  	
  	//Check auf alte oder neue Projekt-Nr.
		if($pNr == '-')
		{
			return true;
		}
		else
		{
	  	if(strlen($pNr) != 11)
	  	{
				$this->setErrorMsg('Projekt-Nr. muss eine Länge von 11 Zeichen haben');
				return false;
	  	}


	  	$year = substr($pNr, 1, 2);
	  	$location = substr($pNr, 3, 2);
			$p_12_map = array('01' => 'Hannover', '02' => 'Berlin', '03' => 'Frankfurt', '04' => 'Düsseldorf', '05' => 'München', '06' => 'Hamburg');
	  	
			if(is_numeric($year) && (int) $year < 4)
			{
				if(preg_match('/^[P][0-9]{2}[0][1-6][0-9]{6}$/', $pNr))
				{
					return true;
				}

				// genauen Fehler für genauere Fehlermeldung bestimmen
				if(!isset($p_12_map[$location]))
				{
					$this->setErrorMsg('Fehler in der Projekt-Nr.: 4. und 5. Stelle (\'Code für die Niederlassung\') ist ungültig');
					return false;
				}

				$p_0_5_map = array('P'=>1);
				if(!isset($p_0_5_map[$pNr[0]]))
				{
					$this->setErrorMsg('Fehler in der Projekt-Nr.: 1. Stelle (\'Art der Nummer\') muss eine Buchstabe (P) sein');
					return false;
				}

				$this->setErrorMsg('Fehler in der Projekt-Nr.');
				return false;
			}
			elseif(is_numeric($year))
			{
				if(preg_match('/^[PSIDV][0-9]{2}[0][1-6]((?<=^P[0-9]{4})1|(?<=^S[0-9]{4})8|(?<=^I[0-9]{4})7|(?<=^D[0-9]{4})9|(?<=^V[0-9]{4})5)[0-9]{5}$/', $pNr, $matches))
				{
					return true;
				}

				// genauen Fehler für genauere Fehlermeldung bestimmen
				if(!isset($p_12_map[$location]))
				{
					$this->setErrorMsg('Fehler in der Projekt-Nr.: 4. und 5. Stelle (\'Code für die Niederlassung\') ist ungültig');
					return false;
				}

				$p_0_5_map = array('P'=>1, 'S'=>8, 'I'=>7, 'D'=>9, 'V'=>5);
				if(!is_numeric($pNr[5]))
				{
					$this->setErrorMsg('Fehler in der Projekt-Nr.: 6. Stelle (\'Art des Auftrages\') muss eine Ziffer sein');
					return false;
				}

				if(!isset($p_0_5_map[$pNr[0]]))
				{
					$this->setErrorMsg('Fehler in der Projekt-Nr.: 1. Stelle (\'Art der Nummer\') muss eine Buchstabe (P, S, I, D oder V) sein');
					return false;
				}
						
				if( (int)$pNr[5] != $p_0_5_map[$pNr[0]] )
				{
					$this->setErrorMsg('Fehler in der Projekt-Nr.: 1. und 6. Stelle (\'Art der Nummer umd des Auftrages\') müssen korrespondieren ('.$pNr[0].'....'.$p_0_5_map[$pNr[0]].'.....)');
					return false;
				}

				$this->setErrorMsg('Fehler in der Projekt-Nr.');
				return false;
			}

			$this->setErrorMsg('Fehler in der Projekt-Nr.: 2. und 3. Stelle (\'Code für das Geschäftsjahr\') müssen eine zweistellige Jahresangabe sein');
			return false;
	  }
  }
}
?>