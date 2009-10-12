<?php
	/**
	* Creates images using GD graphics library
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id$
	* @internal This class based on htmlGD.php3 - Double Choco Latte - Source Configuration Management System - Copyright (C) 1999  Michael L. Dean & Tim R. Norman
	*/


	/**
	* Creates images using GD graphics library
	*
	* @package phpgwapi
	* @subpackage gui
	*/
	class gdimage
	{
		var $filename;
		var $type;
		var $cur_x;
		var $cur_y;
		var $width;
		var $height;
		var $hImage;
		var $colormap;
		var $hColor;
		var $font;
		var $temp_file;

		function gdimage()
		{
			$this->gd = $this->check_gd();

			if ($this->gd == 0)
			{
				echo 'Your PHP installation does not seem to have the required GD library.
						Please see the PHP documentation on how to install and enable the GD library.';
				exit;
			}

			$this->cur_x = 0;
			$this->cur_y = 0;
			$this->width = 0;
			$this->height = 0;
			$this->hImage = 0;
			$this->colormap = array();
			$this->hColor = 0;
			$this->font = 0;
			$this->type = 'png';			
			$this->temp_dir	= PHPGW_SERVER_ROOT . '/phpgwapi/images/';
//			$this->temp_dir	= $GLOBALS['phpgw_info']['server']['temp_dir'] . '/';
		}

		function check_gd()
		{
			ob_start();
			phpinfo(8); // load the modules
			$a = ob_get_contents();
			ob_end_clean();

			if(preg_match('/.*GD Version.*(1[0-9|\.]+).*/',$a,$m))
			{
				$r=1; //$v=$m[1];
			}
			elseif(preg_match('/.*GD Version.*(2[0-9|\.]+).*/',$a,$m))
			{
				$r=2; //$v=$m[1];
			}
			else
			{
				$r=0; //$v=$m[1];
			}
			return $r;
		}

		function Init()
		{
			$this->hImage = ImageCreate($this->width, $this->height) or die;
			return True;
		}

		function Done()
		{
			ImageDestroy($this->hImage);
		}

		/* Create a tmp filename */
		function tmp_file()
		{
			$name = 'draw_tmp_' . $GLOBALS['phpgw_info']['user']['account_id'] . '_';
			srand((double)microtime()*1000000);
	//		return($this->tmpdir . $name . rand(1,100000));
			return($name . rand(1,100000));
		}

		function MoveTo($x, $y)
		{
			if ($x >= 0 && $x <= $this->width && $y >= 0 && $y <= $this->height)
			{
				$this->cur_x = $x;
				$this->cur_y = $y;

				return true;
			}
			return false;
		}

		function LineTo($x, $y, $linestyle = 'solid')
		{
			if ($x >= 0 && $x <= $this->width && $y >= 0 && $y <= $this->height)
			{
				if ($linestyle == 'dashed')
					ImageDashedLine($this->hImage, $this->cur_x, $this->cur_y, $x, $y, $this->hColor);
				else
					ImageLine($this->hImage, $this->cur_x, $this->cur_y, $x, $y, $this->hColor);

				$this->cur_x = $x;
				$this->cur_y = $y;

				return true;
			}

			return false;
		}

		function Line($x1, $y1, $x2, $y2, $linestyle = 'solid')
		{
			if ($x1 >= 0 && $x1 <= $this->width && $y1 >= 0 && $y1 <= $this->height && $x2 >= 0 && $x2 <= $this->width && $y2 >= 0 && $y2 <= $this->height)
			{
				if ($linestyle == 'solid')
					ImageLine($this->hImage, $x1, $y1, $x2, $y2, $this->hColor);
				else
					ImageDashedLine($this->hImage, $x1, $y1, $x2, $y2, $this->hColor);

				$this->cur_x = $x2;
				$this->cur_y = $y2;

				return true;
			}

			return false;
		}

		function SetColor($r, $g, $b, $set_transparent=False)
		{
			$key = "$r,$g,$b";
			if (!IsSet($this->colormap[$key]))
			{
				$this->hColor = ImageColorAllocate($this->hImage, $r, $g, $b);
				$this->colormap[$key] = $this->hColor;
			}
			else
			{
				$this->hColor = $this->colormap[$key];
			}

			if ($set_transparent)
			{
				ImageColorTransparent($this->hImage,$this->hColor);
			}
			return true;
		}

		function SetColorByName($name)
		{
			$r = 0;
			$g = 0;
			$b = 0;
			switch ($name)
			{
				case 'red':
					$r = 180;
					break;
				case 'green':
					$g = 180;
					break;
				case 'blue':
					$b = 180;
					break;
				case 'bright red':
					$r = 255;
					break;
				case 'bright green':
					$g = 255;
					break;
				case 'bright blue':
					$b = 255;
					break;
				case 'dark red':
					$r = 80;
					break;
				case 'dark green':
					$g = 80;
					break;
				case 'dark blue':
					$b = 80;
					break;
				case 'olivedrab4':
					$r = 105;
					$g = 139;
					$b = 34;
					break;
				case 'dove':
					$r = 0x2c;
					$g = 0x6D;
					$b = 0xAF;
					break;
				case 'seagreen':
					$r = 46;
					$g = 139;
					$b = 87;
					break;
				case 'midnightblue':
					$r = 25;
					$g = 25;
					$b = 112;
					break;
				case 'darkorange':
					$r = 255;
					$g = 140;
					break;
				case 'yellow':
					$r = 255;
					$g = 215;
					break;
				case 'grey':
					$r = 180;
					$g = 180;
					$b = 180;
					break;
			}
			return $this->SetColor($r, $g, $b);
		}

		function SetFont($font)
		{
			if ($font < 1 || $font > 5)
				return false;

			$this->font = $font;

			return true;
		}

		function GetFontHeight()
		{
			return ImageFontHeight($this->font);
		}

		function GetFontWidth()
		{
			return ImageFontWidth($this->font);
		}

		function DrawText($params)
		{
			$text			= $params['text'];
			$direction		= (isset($params['direction'])?$params['direction']:'');
			$justification	= (isset($params['justification'])?$params['justification']:'center');
			$margin_left	= (isset($params['margin_left'])?$params['margin_left']:'');

			$textwidth = ImageFontWidth($this->font) * strlen($text);

			/*if (isset($margin_left) && $textwidth >= $margin_left)
			{
				$text = strlen($text) - 1 . '.';
			}*/

			if ($justification == 'center')
			{
				if ($direction == 'up')
				{
					$this->cur_y += $textwidth / 2;
					if ($this->cur_y > $this->height)
						$this->cur_y = $this->height;
				}
				else
				{
					$this->cur_x -= $textwidth / 2;
					if ($this->cur_x < 0)
						$this->cur_x = 0;
				}
			}
			else if ($justification == 'right')
				{
					if ($direction == 'up')
					{
						$this->cur_y += $textwidth;
						if ($this->cur_y > $this->height)
							$this->cur_y = $this->height;
					}
					else
					{
						$this->cur_x -= $textwidth;
						if ($this->cur_x < 0)
							$this->cur_x = 0;
					}
				}

			if ($direction == 'up')
				ImageStringUp($this->hImage, $this->font, $this->cur_x, $this->cur_y, $text, $this->hColor);
			else
				ImageString($this->hImage, $this->font, $this->cur_x, $this->cur_y, $text, $this->hColor);

			return true;
		}

		function draw_triangle($points)
		{
			imagefilledpolygon($this->hImage,$points,3,$this->hColor);
			return True;
		}

		function draw_rectangle($data = 0, $style = 'unused')
		{
			imagefilledrectangle ($this->hImage, $data[0], $data[1], $data[2], $data[3],$this->hColor);

			if($style != 'unused')
			{
				$this->SetColor(255,255,255);
				switch($style)
				{
					case 'open':
						imagefilledrectangle ($this->hImage, $data[0]+1, $data[1]+4, $data[2]-1, $data[3]-4,$this->hColor);
						break;
					default:
						imagefilledrectangle ($this->hImage, $data[0]+1, $data[1]+4, $data[2]-1, $data[3]-4,$this->hColor);
						imagefilledrectangle ($this->hImage, $data[0]+4, $data[1]+1, $data[2]-4, $data[3]-1,$this->hColor);
				}
			}
			return True;
		}

		function check_tmp_files()
		{
			if (is_dir($this->temp_dir))
			{
				$basedir = opendir($this->temp_dir);

				while ($files = readdir($basedir))
				{
					if (($files != '.') && ($files != '..'))
					{
						$to_find = '_' . $GLOBALS['phpgw_info']['user']['account_id'] . '_';
						$pos = strpos($files,$to_find);
						if($pos)
						{
							unlink($this->temp_dir . $files);
						}
					}
				}
				closedir($basedir);
				return True;
			}
			return False;
		}

		function save_img()
		{
			$this->check_tmp_files();
			$filename = $this->tmp_file();
			ImagePNG($this->hImage,$this->temp_dir . $filename);
			return $filename;
		}

		function ToBrowser()
		{
			//header('Content-type: image/' . $this->type);

			switch ($this->type)
			{
				case 'png':
					ImagePNG($this->hImage,$this->temp_file);
					break;
				case 'gif':
					ImageGIF($this->hImage);
					break;
				case 'jpeg':
					ImageJPEG($this->hImage);
					break;
			}
		}
	}
?>
