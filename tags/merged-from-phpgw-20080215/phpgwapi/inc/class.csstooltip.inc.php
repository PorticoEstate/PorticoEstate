<?php
	/**
	* csstooltip wrapper-class
	*
	*	Example:
	* <code>
	* $tooltip = new csstooltip();
	*	$html = $tooltip->createHelpTooltip(lang('select your language'));
	* // insert $html in your template
	* </code>
	*
	* @author Dirk Schaller <dschaller@probusiness.de>
	* @copyright Copyright (C) 2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id$
	*/
	
	/**
	* csstooltip wrapper-class
	*
	* @package phpgwapi
	* @subpackage gui
	* @internal The constructor load the necessary css-files
	*/
	class csstooltip
	{
		/**
		* Constructor of class csstooltip
		*
		* @access public
		*/
		function csstooltip()
		{
			if(!is_object($GLOBALS['phpgw']->css))
			{
				$GLOBALS['phpgw']->css = CreateObject('phpgwapi.css');
			}
	
			$GLOBALS['phpgw']->css->validate_file("tooltip", "tooltip");
		}
		
		/**
		* Creates a help tooltip with a question mark as tooltip link and 'help' as tooltip headline
		*
		* @access public
		* @param string $tooltip_content content to show in toottip
		* @return string xhtml tooltip
		*/
		function createHelpTooltip($tooltip_content)
		{
			$link_content = '<span class="helptooltip">?</span>';
			return $this->createTextTooltip($link_content, $tooltip_content, lang('help'));
		}
	
		/**
		* Creates an info tooltip with a 'i' as tooltip link and 'info' as tooltip headline
		*
		* @access public
		* @param string $tooltip_content content to show in toottip
		* @return string xhtml tooltip
		*/
		function createInfoTooltip($tooltip_content)
		{
			$link_content = '<span class="infotooltip">i</span>';
			return $this->createTextTooltip($link_content, $tooltip_content, lang('info'));
		}
	
		/**
		* Creates a text tooltip with individual output text and tooltip headline
		*
		* @access public
		* @param string $link_content content to show as tooltip link
		* @param string $tooltip_content content to show in toottip
		* @param string $tooltip_headline content to show in toottip headline, default is 'help'
		* @return string xhtml tooltip
		*/
		function createTextTooltip($link_content, $tooltip_content, $tooltip_headline='')
		{
			if (!$tooltip_headline)
				$tooltip_headline = lang('help');
			return $this->_createTooltipHTML($link_content, $tooltip_content, $tooltip_headline);
		}
		
		/**
		* Creates a image tooltip
		*
		* @access public
		* @param string $image_src url of the image to show as tooltip link.
		* @param string $tooltip_content content to show in toottip
		* @param string $tooltip_headline content to show in toottip headline, default is 'help'. Is used as the image alt attribute.
		* @return string xhtml tooltip
		*/
		function createImageTooltip($image_src='', $tooltip_content, $tooltip_headline='')
		{
			if (!$image_src)
				$image_src = $GLOBALS['phpgw']->common->image('phpgwapi','tooltip');
			if (!$tooltip_headline)
				$tooltip_headline = lang('help');
	
			$link_content = '<img src="'.$image_src.'" border="0" alt="'.$tooltip_headline.'">';
	
			return $this->_createTooltipHTML($link_content, $tooltip_content, $tooltip_headline);
		}
	
		/**
		* Creates a individual tooltip 
		*
		* @access public
		* @param string $link_content content to show as tooltip link
		* @param string $tooltip_content content to show in toottip
		* @param string $tooltip_headline content to show in toottip headline
		* @return string xhtml tooltip
		*/
		function createTooltip($link_content, $tooltip_content, $tooltip_headline)
		{
			return $this->_createTooltipHTML($link_content, $tooltip_content, $tooltip_headline);
		}
		
		/**
		* Creates the tooltip output
		*
		* @access private
		* @param string $link_content content to show as tooltip link
		* @param string $tooltip_content content to show in toottip
		* @param string $tooltip_headline content to show in toottip headline
		* @return string xhtml tooltip
		*/
		function _createTooltipHTML($link_content, $tooltip_content, $tooltip_headline)
		{
			return '<a href="#" class="tooltip">'.$link_content.'<span class="tooltip"><span class="tooltip_headline">'.$tooltip_headline.'</span><span class="tooltip_line">&nbsp;</span><span class="tooltip_content">'.$tooltip_content.'</span></span></a>';
		}
	}
?>