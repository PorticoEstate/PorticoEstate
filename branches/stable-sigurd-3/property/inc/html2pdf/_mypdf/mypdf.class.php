<?php
/**
 * Logiciel : HTML2PDF - classe MyPDF
 * 
 * Convertisseur HTML => PDF, utilise fpdf de Olivier PLATHEY 
 * Distribué sous la licence GPL. 
 *
 * @author		Laurent MINGUET <webmaster@spipu.net>
 * @version		3.11 - 28/08/2008
 */

if (!defined('__CLASS_MYPDF__'))
{
	define('__CLASS_MYPDF__', true);
	
	require_once(dirname(__FILE__).'/01_fpdf_protection.class.php');		// classe fpdf_codebar

	class MyPDF extends FPDF_Protection
	{
		var $footer_param = array();
		
	    var $underline		= false;
	    var $overline		= false;
	    var $linethrough	= false;
	    	
	    function MyPDF($sens = 'P', $unit = 'mm', $format = 'A4')
	    {
	    	$this->underline	= false;
	    	$this->overline		= false;
	    	$this->linethrough	= false;
	    	
	    	$this->FPDF_Protection($sens, $unit, $format);
	    	$this->AliasNbPages();
	    	$this->SetMyFooter();
	    }
	    
	    function SetMyFooter($page = null, $date = null, $heure = null)
	    {
	    	if ($page===null)	$page = false;
	    	if ($date===null)	$date = false;
	    	if ($heure===null)	$heure = false;
	    	
	    	$this->footer_param = array('page' => $page, 'date' => $date, 'heure' => $heure);	
	    }
	    
	    function Footer()
		{ 
			$txt = '';
			if ($this->footer_param['date'] && $this->footer_param['heure'])	$txt.= ($txt ? ' - ' : '').(HTML2PDF::textGET('pdf03'));
			if ($this->footer_param['date'] && !$this->footer_param['heure'])	$txt.= ($txt ? ' - ' : '').(HTML2PDF::textGET('pdf01'));
			if (!$this->footer_param['date'] && $this->footer_param['heure'])	$txt.= ($txt ? ' - ' : '').(HTML2PDF::textGET('pdf02'));
			if ($this->footer_param['page'])	$txt.= ($txt ? ' - ' : '').(HTML2PDF::textGET('pdf04'));
			
			$txt = str_replace('[[date_d]]',	date('d'),			$txt);
			$txt = str_replace('[[date_m]]',	date('m'),			$txt);
			$txt = str_replace('[[date_y]]',	date('Y'),			$txt);
			$txt = str_replace('[[date_h]]',	date('H'),			$txt);
			$txt = str_replace('[[date_i]]',	date('i'),			$txt);
			$txt = str_replace('[[date_s]]',	date('s'),			$txt);
			$txt = str_replace('[[current]]',	$this->PageNo(),	$txt);
			$txt = str_replace('[[nb]]',		'{nb}',				$txt);

			if (strlen($txt)>0)
			{
			 	$this->SetY(-11);
			 	$this->setOverline(false);
			 	$this->setLinethrough(false);
				$this->SetFont('Arial','I',8);
				$this->Cell(0, 10, $txt, 0, 0, 'R');
			}
		}
		
		// redéfinition de la fonction Image de FPDF afin de rajouter la gestion des fichiers PHP, et du clip
		function Image($file, $x=null, $y=null, $w=0, $h=0, $type='', $link='', $clip_x=null, $clip_y=null, $clip_w=null, $clip_h=null)
		{
			//Put an image on the page
			if(!isset($this->images[$file]))
			{
				//First use of this image, get info
				if($type=='')
				{
					/* MODIFICATION HTML2PDF pour le support des images PHP */
					$type = explode('?', $file);
					$type = pathinfo($type[0]);
					if (!isset($type['extension']) || !$type['extension'])
						$this->Error('Image file has no extension and no type was specified: '.$file);
						
					$type = $type['extension'];
					/* FIN MODIFICATION */
				}

				$type=strtolower($type);

				/* MODIFICATION HTML2PDF pour le support des images PHP */
				if ($type=='php')
				{
					// identification des infos
					$infos=@GetImageSize($file);
					if (!$infos) $this->Error('Unsupported image : '.$file);
				
					// identification du type
					$type = explode('/', $infos['mime']);
					if ($type[0]!='image') $this->Error('Unsupported image : '.$file);
					$type = $type[1];
				}
				/* FIN MODIFICATION */
				
				if($type=='jpeg')
					$type='jpg';
				$mtd='_parse'.$type;
				if(!method_exists($this,$mtd))
					$this->Error('Unsupported image type: '.$type);
				$info=$this->$mtd($file);
				$info['i']=count($this->images)+1;
				$this->images[$file]=$info;
			}
			else
				$info=$this->images[$file];
			//Automatic width and height calculation if needed
			if($w==0 && $h==0)
			{
				//Put image at 72 dpi
				$w=$info['w']/$this->k;
				$h=$info['h']/$this->k;
			}
			elseif($w==0)
				$w=$h*$info['w']/$info['h'];
			elseif($h==0)
				$h=$w*$info['h']/$info['w'];
			//Flowing mode
			if($y===null)
			{
				if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
				{
					//Automatic page break
					$x2=$this->x;
					$this->AddPage($this->CurOrientation,$this->CurPageFormat);
					$this->x=$x2;
				}
				$y=$this->y;
				$this->y+=$h;
			}
			if($x===null)
				$x=$this->x;
				
			$clip = '';
			if ($clip_x!==null && $clip_y!==null && $clip_w!==null && $clip_h!==null)
			{
				$clip = sprintf('%.2F %.2F %.2F %.2F re W n', ($x+$clip_x)*$this->k, ($this->h-(($y+$clip_y)+$clip_h))*$this->k, $clip_w*$this->k, $clip_h*$this->k);
			}
			$this->_out(sprintf('q '.$clip.' %.2F 0 0 %.2F %.2F %.2F cm /I%d Do Q',$w*$this->k,$h*$this->k,$x*$this->k,($this->h-($y+$h))*$this->k,$info['i']));
			if($link)
				$this->Link($x,$y,$w,$h,$link);
		}
		
		// Draw a polygon
	    // Auteur	: Andrew Meier
		// Licence	: Freeware
		function Polygon($points, $style='D')
		{
		    if($style=='F')							$op='f';
		    elseif($style=='FD' or $style=='DF')	$op='b';
		    else									$op='s';
		
		    $h = $this->h;
		    $k = $this->k;
		
		    $points_string = '';
		    for($i=0; $i<count($points); $i+=2)
		    {
		        $points_string .= sprintf('%.2f %.2f', $points[$i]*$k, ($h-$points[$i+1])*$k);
		        if($i==0)	$points_string .= ' m ';
		        else		$points_string .= ' l ';
		    }
		    $this->_out($points_string . $op);
		}
		
		function setOverline($value = true)
		{
			$this->overline = $value;
		}

		function setLinethrough($value = true)
		{
			$this->linethrough = $value;
		}
		
		// redéfinition de la methode Text de FPDF afin de rajouter la gestion des overline et linethrough
		function Text($x, $y, $txt)
		{
			//Output a string
			$s=sprintf('BT %.2F %.2F Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));

			/* MODIFICATION HTML2PDF pour le support de underline, overline, linethrough */
			if ($txt!='')
			{
				if($this->underline)	$s.=' '.$this->_dounderline($x,$y,$txt);
				if($this->overline)		$s.=' '.$this->_dooverline($x,$y,$txt);
				if($this->linethrough)	$s.=' '.$this->_dolinethrough($x,$y,$txt);
			}
			/* FIN MODIFICATION */

			if($this->ColorFlag)
				$s='q '.$this->TextColor.' '.$s.' Q';
			$this->_out($s);
		}

		// redéfinition de la methode Cell de FPDF afin de rajouter la gestion des overline et linethrough
		function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
		{
			//Output a cell
			$k=$this->k;
			if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
			{
				//Automatic page break
				$x=$this->x;
				$ws=$this->ws;
				if($ws>0)
				{
					$this->ws=0;
					$this->_out('0 Tw');
				}
				$this->AddPage($this->CurOrientation,$this->CurPageFormat);
				$this->x=$x;
				if($ws>0)
				{
					$this->ws=$ws;
					$this->_out(sprintf('%.3F Tw',$ws*$k));
				}
			}
			if($w==0)
				$w=$this->w-$this->rMargin-$this->x;
			$s='';
			if($fill || $border==1)
			{
				if($fill)
					$op=($border==1) ? 'B' : 'f';
				else
					$op='S';
				$s=sprintf('%.2F %.2F %.2F %.2F re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
			}
			if(is_string($border))
			{
				$x=$this->x;
				$y=$this->y;
				if(strpos($border,'L')!==false)
					$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
				if(strpos($border,'T')!==false)
					$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
				if(strpos($border,'R')!==false)
					$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
				if(strpos($border,'B')!==false)
					$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
			}
			if($txt!=='')
			{
				if($align=='R')
					$dx=$w-$this->cMargin-$this->GetStringWidth($txt);
				elseif($align=='C')
					$dx=($w-$this->GetStringWidth($txt))/2;
				else
					$dx=$this->cMargin;
				if($this->ColorFlag)
					$s.='q '.$this->TextColor.' ';
				$txt2=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
				$s.=sprintf('BT %.2F %.2F Td (%s) Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$txt2);
				
				/* MODIFICATION HTML2PDF pour le support de underline, overline, linethrough */
				if($this->underline)	$s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
				if($this->overline)		$s.=' '.$this->_dooverline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
				if($this->linethrough)	$s.=' '.$this->_dolinethrough($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
				/* FIN MODIFICATION */
				
				if($this->ColorFlag)
					$s.=' Q';
				if($link)
					$this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$this->GetStringWidth($txt),$this->FontSize,$link);
			}
			if($s)
				$this->_out($s);
			$this->lasth=$h;
			if($ln>0)
			{
				//Go to next line
				$this->y+=$h;
				if($ln==1)
					$this->x=$this->lMargin;
			}
			else
				$this->x+=$w;
		}

		function _dounderline($x, $y, $txt)
		{
			//Underline text
			$up=$this->CurrentFont['up'];
			$ut=$this->CurrentFont['ut'];

			$p_x = $x*$this->k;
			$p_y = ($this->h-($y-$up/1000*$this->FontSize))*$this->k;
			$p_w = ($this->GetStringWidth($txt)+$this->ws*substr_count($txt,' '))*$this->k;
			$p_h = -$ut/1000*$this->FontSizePt;

			return sprintf('%.2F %.2F %.2F %.2F re f',$p_x,$p_y,$p_w,$p_h);
		}
		
		function _dooverline($x, $y, $txt)
		{
			//Overline text
			$up=$this->CurrentFont['up'];
			$ut=$this->CurrentFont['ut'];

			$p_x = $x*$this->k;
			$p_y = ($this->h-($y-(1000+1.5*$up)/1000*$this->FontSize))*$this->k;
			$p_w = ($this->GetStringWidth($txt)+$this->ws*substr_count($txt,' '))*$this->k;
			$p_h =  -$ut/1000*$this->FontSizePt;
			
			return sprintf('%.2F %.2F %.2F %.2F re f',$p_x,$p_y,$p_w,$p_h);
		}
		
		function _dolinethrough($x, $y, $txt)
		{
			//Linethrough text
			$up=$this->CurrentFont['up'];
			$ut=$this->CurrentFont['ut'];

			$p_x = $x*$this->k;
			$p_y = ($this->h-($y-(1000+2.5*$up)/2000*$this->FontSize))*$this->k;
			$p_w = ($this->GetStringWidth($txt)+$this->ws*substr_count($txt,' '))*$this->k;
			$p_h = -$ut/1000*$this->FontSizePt;
			
			return sprintf('%.2F %.2F %.2F %.2F re f',$p_x,$p_y,$p_w,$p_h);
		}
	}
}
?>